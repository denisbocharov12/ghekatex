<?php

namespace App\Services;

use App\Repositories\Contracts\CertificateRepositoryInterface;
use App\Repositories\Contracts\CompanyMilestoneRepositoryInterface;
use App\Repositories\Contracts\FacilityRepositoryInterface;
use App\Repositories\Contracts\PartnerRepositoryInterface;
use App\Support\Presenters\SitePresenter;

/**
 * Данные страницы «О компании»: история, мощности, сертификаты, партнёры.
 * Вступительный текст редактируется как контентная страница со слагом `about`.
 */
class AboutPageService
{
    public function __construct(
        private readonly CompanyMilestoneRepositoryInterface $milestones,
        private readonly FacilityRepositoryInterface $facilities,
        private readonly CertificateRepositoryInterface $certificates,
        private readonly PartnerRepositoryInterface $partners,
    ) {}

    /** @return array<string, mixed> */
    public function build(): array
    {
        return [
            'milestones' => SitePresenter::collect($this->milestones->activeOrdered(), SitePresenter::milestone(...)),
            'facilities' => SitePresenter::collect($this->facilities->activeOrdered(), SitePresenter::facility(...)),
            'certificates' => SitePresenter::collect($this->certificates->activeOrdered(), SitePresenter::certificate(...)),
            'partners' => SitePresenter::collect($this->partners->activeOrdered(), SitePresenter::partner(...)),
        ];
    }
}
