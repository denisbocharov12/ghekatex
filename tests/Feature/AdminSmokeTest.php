<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Проверяет, что каждый экран панели открывается и отдаёт свою Vue-страницу.
 *
 * Тест ловит самое частое после большого рефакторинга: несуществующий
 * маршрут, потерянный пропс или опечатку в имени компонента.
 */
class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_login_screen_renders(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function adminScreens(): array
    {
        $resources = [
            'pages' => 'Pages',
            'hero-slides' => 'HeroSlides',
            'advantages' => 'Advantages',
            'milestones' => 'Milestones',
            'facilities' => 'Facilities',
            'certificates' => 'Certificates',
            'partners' => 'Partners',
            'product-categories' => 'ProductCategories',
            'products' => 'Products',
            'fabrics' => 'Fabrics',
            'treatments' => 'Treatments',
            'services' => 'Services',
            'faqs' => 'Faqs',
            'post-categories' => 'PostCategories',
            'posts' => 'Posts',
            'media-albums' => 'MediaAlbums',
            'media-items' => 'MediaItems',
            'offices' => 'Offices',
            'navigation' => 'Navigation',
        ];

        $screens = [
            'dashboard' => ['/admin', 'Dashboard'],
            'profile' => ['/admin/profile', 'Profile'],
            'requests' => ['/admin/requests', 'Requests/Index'],
            'settings' => ['/admin/settings', 'Settings/Index'],
            'translations' => ['/admin/translations', 'Translations/Index'],
            'redirects' => ['/admin/redirects', 'Redirects/Index'],
            'users' => ['/admin/users', 'Users/Index'],
            'users.create' => ['/admin/users/create', 'Users/Form'],
            'roles' => ['/admin/roles', 'Roles/Index'],
            'activity-log' => ['/admin/activity-log', 'ActivityLog/Index'],
        ];

        foreach ($resources as $segment => $page) {
            $screens[$segment] = ["/admin/{$segment}", "{$page}/Index"];
            $screens["{$segment}.create"] = ["/admin/{$segment}/create", "{$page}/Form"];
        }

        return $screens;
    }

    #[DataProvider('adminScreens')]
    public function test_admin_screen_renders(string $url, string $component): void
    {
        $admin = User::query()->where('email', 'admin@ghekatex.md')->firstOrFail();

        $this->assertTrue($admin->hasRole(RoleName::SuperAdmin->value));

        $response = $this->actingAs($admin)->get($url);

        $response->assertOk();
        // В HTML компонент лежит внутри JSON, где слеш экранирован
        $response->assertSee('"component":"'.str_replace('/', '\\/', $component).'"', escape: false);
    }

    public function test_form_screens_keep_shared_locales_object(): void
    {
        /*
        | Контроллеры разделов отдавали свой проп `locales` плоским списком
        | и затирали общий объект: форма падала на `locales.available` и
        | экран оставался белым.
        */
        $admin = User::query()->where('email', 'admin@ghekatex.md')->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin/advantages/1/edit');

        $response->assertOk();
        $response->assertSee('"locales":{"available"', false);
    }
}
