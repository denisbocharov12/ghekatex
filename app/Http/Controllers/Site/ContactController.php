<?php

namespace App\Http\Controllers\Site;

use App\Data\Mappers\ContactRequestDataMapper;
use App\Http\Requests\Site\ContactRequestForm;
use App\Managers\ContactRequestManager;
use App\Repositories\Contracts\OfficeRepositoryInterface;
use App\Services\SeoService;
use App\Services\SettingService;
use App\Support\Presenters\SitePresenter;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\SchemaOrg\Schema;

class ContactController extends SiteController
{
    public function __construct(
        SeoService $seo,
        private readonly OfficeRepositoryInterface $offices,
        private readonly ContactRequestManager $requests,
        private readonly SettingService $settings,
    ) {
        parent::__construct($seo);
    }

    public function index(string $locale): Response
    {
        $offices = SitePresenter::collect($this->offices->activeOrdered(), SitePresenter::office(...));
        $title = __('Контакты');
        $crumbs = $this->crumbs([['label' => $title]]);

        return Inertia::render('Contacts', [
            'offices' => $offices,
            'contacts' => $this->settings->group('contacts'),
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forPage(
                title: $title,
                breadcrumbs: $crumbs,
                schema: array_map(
                    static fn (array $office) => Schema::localBusiness()
                        ->name($office['name'])
                        ->streetAddress($office['address'])
                        ->addressLocality($office['city'])
                        ->addressCountry($office['country'])
                        ->postalCode($office['postal'])
                        ->telephone($office['phones'][0] ?? null)
                        ->email($office['emails'][0] ?? null)
                        ->latitude($office['latitude'])
                        ->longitude($office['longitude'])
                        ->toArray(),
                    $offices,
                ),
            )->toArray(),
        ]);
    }

    public function store(ContactRequestForm $request): RedirectResponse
    {
        $this->requests->submit(ContactRequestDataMapper::fromRequest($request), $request);

        return back()->with('success', __('Спасибо! Мы свяжемся с вами в ближайшее время.'));
    }
}
