<?php

namespace Tests\Feature;

use App\Enums\ContactRequestSource;
use App\Enums\ContactRequestStatus;
use App\Models\ContactRequest;
use App\Models\Post;
use App\Models\Product;
use App\Models\Redirect;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SiteSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function publicPages(): array
    {
        $pages = [];

        foreach (['ro', 'en', 'ru'] as $locale) {
            $pages["home {$locale}"] = ["/{$locale}", 'Home'];
            $pages["about {$locale}"] = ["/{$locale}/about", 'About'];
            $pages["catalog {$locale}"] = ["/{$locale}/catalog", 'Catalog\/Index'];
            $pages["services {$locale}"] = ["/{$locale}/services", 'Services\/Index'];
            $pages["news {$locale}"] = ["/{$locale}/news", 'News\/Index'];
            $pages["media {$locale}"] = ["/{$locale}/media", 'Media\/Index'];
            $pages["contacts {$locale}"] = ["/{$locale}/contacts", 'Contacts'];
            $pages["privacy {$locale}"] = ["/{$locale}/privacy-policy", 'Page'];
            $pages["cookie {$locale}"] = ["/{$locale}/cookie-policy", 'Page'];
        }

        return $pages;
    }

    #[DataProvider('publicPages')]
    public function test_public_page_renders(string $url, string $component): void
    {
        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee('"component":"'.$component.'"', escape: false);
    }

    public function test_detail_pages_render(): void
    {
        $product = Product::query()->active()->firstOrFail();
        $service = Service::query()->active()->firstOrFail();
        $post = Post::query()->published()->firstOrFail();

        $this->get("/ro/catalog/{$product->slug}")->assertOk();
        $this->get("/ro/services/{$service->slug}")->assertOk();
        $this->get("/ro/news/{$post->slug}")->assertOk();
    }

    public function test_root_resolves_default_locale(): void
    {
        $this->get('/')->assertOk()->assertSee('"component":"Home"', escape: false);
    }

    public function test_sitemap_lists_every_locale(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');

        foreach (['ro', 'en', 'ru'] as $locale) {
            $response->assertSee("/{$locale}/catalog", escape: false);
        }
    }

    public function test_robots_points_to_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: ', escape: false)
            ->assertSee('Disallow: /admin', escape: false);
    }

    public function test_contact_form_stores_request_and_sends_mail(): void
    {
        Mail::fake();

        $response = $this->from('/ro/contacts')->post('/ro/contacts', [
            'name' => 'Marco Rossi',
            'email' => 'marco@example.com',
            'company' => 'Maison Aurelia',
            'message' => 'Ne interesează producția unei colecții de rochii.',
            'consent' => true,
            'source' => 'contacts',
        ]);

        $response->assertRedirect('/ro/contacts');
        $response->assertSessionHas('success');

        $request = ContactRequest::query()->firstOrFail();

        $this->assertSame('marco@example.com', $request->email);
        $this->assertSame(ContactRequestStatus::New, $request->status);
        // IP не хранится в открытом виде — только хеш
        $this->assertNotSame(request()->ip(), $request->ip_hash);
    }

    public function test_dialog_request_keeps_chosen_services_in_subject(): void
    {
        Mail::fake();

        $response = $this->from('/ru')->post('/ru/contacts', [
            'name' => 'Анна Петрова',
            'email' => 'anna@example.com',
            'subject' => 'Производство под заказ (CMT), Контроль качества',
            'message' => 'Нужен расчёт на партию платьев из вискозы.',
            'consent' => true,
            'source' => 'dialog',
        ]);

        $response->assertRedirect('/ru');
        $response->assertSessionHas('success');

        $request = ContactRequest::query()->firstOrFail();

        $this->assertSame(ContactRequestSource::Dialog, $request->source);
        $this->assertStringContainsString('Контроль качества', (string) $request->subject);
    }

    public function test_contact_form_rejects_bots(): void
    {
        $this->post('/ro/contacts', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Lorem ipsum dolor sit amet consectetur.',
            'consent' => true,
            'company_website' => 'https://spam.example',
        ])->assertSessionHasErrors('company_website');

        $this->assertSame(0, ContactRequest::query()->count());
    }

    public function test_consent_is_logged(): void
    {
        $response = $this->postJson('/consent', ['categories' => ['analytics']]);

        $response->assertOk();
        $response->assertJsonStructure(['anonymous_id', 'categories']);

        $this->assertDatabaseCount('consent_logs', 1);
    }

    public function test_redirect_rule_is_applied(): void
    {
        Redirect::query()->create([
            'from_path' => '/old-catalog',
            'to_path' => '/ro/catalog',
            'status_code' => 301,
            'is_active' => true,
        ]);

        $this->get('/old-catalog')->assertRedirect('/ro/catalog');
    }

    public function test_api_returns_catalog(): void
    {
        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'slug', 'name']], 'meta']);
    }

    public function test_url_without_locale_is_redirected(): void
    {
        $this->get('/catalog/midi-wrap-dress')->assertRedirect('/ro/catalog/midi-wrap-dress');
        $this->get('/catalog')->assertRedirect('/ro/catalog');

        // Несуществующий адрес не должен уводить на 404 лишним переходом
        $this->get('/no-such-path')->assertNotFound();
    }

    public function test_missing_page_renders_branded_error(): void
    {
        $response = $this->get('/ro/no-such-page');

        $response->assertNotFound();
        $response->assertSee('"component":"Error"', false);
        $response->assertSee('"locale":"ro"', false);
    }

    public function test_missing_api_endpoint_stays_json(): void
    {
        $this->getJson('/api/v1/no-such-endpoint')
            ->assertNotFound()
            ->assertHeader('content-type', 'application/json');
    }
}
