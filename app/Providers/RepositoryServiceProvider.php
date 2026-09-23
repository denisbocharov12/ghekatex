<?php

namespace App\Providers;

use App\Repositories\Contracts\AdvantageRepositoryInterface;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use App\Repositories\Contracts\CompanyMilestoneRepositoryInterface;
use App\Repositories\Contracts\ConsentLogRepositoryInterface;
use App\Repositories\Contracts\ContactRequestRepositoryInterface;
use App\Repositories\Contracts\FabricRepositoryInterface;
use App\Repositories\Contracts\FacilityRepositoryInterface;
use App\Repositories\Contracts\FaqRepositoryInterface;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use App\Repositories\Contracts\MediaAlbumRepositoryInterface;
use App\Repositories\Contracts\MediaItemRepositoryInterface;
use App\Repositories\Contracts\NavigationItemRepositoryInterface;
use App\Repositories\Contracts\OfficeRepositoryInterface;
use App\Repositories\Contracts\PageRepositoryInterface;
use App\Repositories\Contracts\PartnerRepositoryInterface;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\RedirectRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\SubscriberRepositoryInterface;
use App\Repositories\Contracts\TranslationOverrideRepositoryInterface;
use App\Repositories\Contracts\TreatmentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\AdvantageRepository;
use App\Repositories\Eloquent\CertificateRepository;
use App\Repositories\Eloquent\CompanyMilestoneRepository;
use App\Repositories\Eloquent\ConsentLogRepository;
use App\Repositories\Eloquent\ContactRequestRepository;
use App\Repositories\Eloquent\FabricRepository;
use App\Repositories\Eloquent\FacilityRepository;
use App\Repositories\Eloquent\FaqRepository;
use App\Repositories\Eloquent\HeroSlideRepository;
use App\Repositories\Eloquent\MediaAlbumRepository;
use App\Repositories\Eloquent\MediaItemRepository;
use App\Repositories\Eloquent\NavigationItemRepository;
use App\Repositories\Eloquent\OfficeRepository;
use App\Repositories\Eloquent\PageRepository;
use App\Repositories\Eloquent\PartnerRepository;
use App\Repositories\Eloquent\PostCategoryRepository;
use App\Repositories\Eloquent\PostRepository;
use App\Repositories\Eloquent\ProductCategoryRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\RedirectRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\SettingRepository;
use App\Repositories\Eloquent\SubscriberRepository;
use App\Repositories\Eloquent\TranslationOverrideRepository;
use App\Repositories\Eloquent\TreatmentRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Связывает контракты репозиториев с реализациями на Eloquent.
 * Контроллеры и менеджеры зависят только от интерфейсов.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdvantageRepositoryInterface::class, AdvantageRepository::class);
        $this->app->bind(CertificateRepositoryInterface::class, CertificateRepository::class);
        $this->app->bind(CompanyMilestoneRepositoryInterface::class, CompanyMilestoneRepository::class);
        $this->app->bind(ConsentLogRepositoryInterface::class, ConsentLogRepository::class);
        $this->app->bind(ContactRequestRepositoryInterface::class, ContactRequestRepository::class);
        $this->app->bind(FabricRepositoryInterface::class, FabricRepository::class);
        $this->app->bind(FacilityRepositoryInterface::class, FacilityRepository::class);
        $this->app->bind(FaqRepositoryInterface::class, FaqRepository::class);
        $this->app->bind(HeroSlideRepositoryInterface::class, HeroSlideRepository::class);
        $this->app->bind(MediaAlbumRepositoryInterface::class, MediaAlbumRepository::class);
        $this->app->bind(MediaItemRepositoryInterface::class, MediaItemRepository::class);
        $this->app->bind(NavigationItemRepositoryInterface::class, NavigationItemRepository::class);
        $this->app->bind(OfficeRepositoryInterface::class, OfficeRepository::class);
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
        $this->app->bind(PartnerRepositoryInterface::class, PartnerRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PostCategoryRepositoryInterface::class, PostCategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(RedirectRepositoryInterface::class, RedirectRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->bind(SubscriberRepositoryInterface::class, SubscriberRepository::class);
        $this->app->bind(TranslationOverrideRepositoryInterface::class, TranslationOverrideRepository::class);
        $this->app->bind(TreatmentRepositoryInterface::class, TreatmentRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
