<?php

namespace App\Enums;

/**
 * Области прав. Итоговое право — `<область>.<действие>`, например `posts.update`.
 *
 * @see PermissionAction
 */
enum PermissionArea: string
{
    case Dashboard = 'dashboard';
    case Pages = 'pages';
    case Posts = 'posts';
    case PostCategories = 'post-categories';
    case Media = 'media';
    case Products = 'products';
    case ProductCategories = 'product-categories';
    case Fabrics = 'fabrics';
    case Treatments = 'treatments';
    case Services = 'services';
    case Faqs = 'faqs';
    case Certificates = 'certificates';
    case Partners = 'partners';
    case Facilities = 'facilities';
    case Milestones = 'milestones';
    case Advantages = 'advantages';
    case HeroSlides = 'hero-slides';
    case Offices = 'offices';
    case Requests = 'requests';
    case Subscribers = 'subscribers';
    case Settings = 'settings';
    case Navigation = 'navigation';
    case Seo = 'seo';
    case Redirects = 'redirects';
    case Translations = 'translations';
    case Users = 'users';
    case Roles = 'roles';
    case ActivityLog = 'activity-log';

    public function label(): string
    {
        return match ($this) {
            self::Dashboard => 'Дашборд',
            self::Pages => 'Страницы',
            self::Posts => 'Новости и статьи',
            self::PostCategories => 'Рубрики новостей',
            self::Media => 'Медиа-галерея',
            self::Products => 'Продукция',
            self::ProductCategories => 'Категории продукции',
            self::Fabrics => 'Ткани',
            self::Treatments => 'Обработки',
            self::Services => 'Услуги',
            self::Faqs => 'Частые вопросы',
            self::Certificates => 'Сертификаты',
            self::Partners => 'Партнёры',
            self::Facilities => 'Производственные мощности',
            self::Milestones => 'История компании',
            self::Advantages => 'Преимущества',
            self::HeroSlides => 'Слайдер главной',
            self::Offices => 'Офисы и производства',
            self::Requests => 'Заявки',
            self::Subscribers => 'Подписчики',
            self::Settings => 'Настройки',
            self::Navigation => 'Меню',
            self::Seo => 'SEO',
            self::Redirects => 'Редиректы',
            self::Translations => 'Переводы интерфейса',
            self::Users => 'Пользователи',
            self::Roles => 'Роли и права',
            self::ActivityLog => 'Журнал действий',
        };
    }

    /**
     * Действия, доступные в этой области. Дашборд и журнал — только просмотр.
     *
     * @return array<int, PermissionAction>
     */
    public function actions(): array
    {
        return match ($this) {
            self::Dashboard, self::ActivityLog => [PermissionAction::View],
            self::Requests, self::Subscribers => [PermissionAction::View, PermissionAction::Update, PermissionAction::Delete],
            self::Settings, self::Seo => [PermissionAction::View, PermissionAction::Update],
            default => PermissionAction::cases(),
        };
    }

    /** @return array<int, string> Все права области, например `posts.view`. */
    public function permissions(): array
    {
        return array_map(fn (PermissionAction $action) => $this->value.'.'.$action->value, $this->actions());
    }

    /** @return array<int, string> Полный список прав системы. */
    public static function allPermissions(): array
    {
        return array_merge(...array_map(static fn (self $area) => $area->permissions(), self::cases()));
    }
}
