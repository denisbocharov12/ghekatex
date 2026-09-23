# 02 — Архитектура

Стек: **Laravel 12 · PHP 8.2 · Inertia 2 · Vue 3 (TS) · Tailwind CSS 4 · Vite 7 · MySQL/SQLite**.

## Слои и поток данных

```
HTTP Request
  └─ Http/Requests/*          валидация (FormRequest)
      └─ Data/Mappers/*       Request → DTO (нормализация, переводы, дефолты)
          └─ Data/*           DTO (spatie/laravel-data), иммутабельный
              └─ Managers/*   бизнес-инварианты, транзакции, медиа, слаги
                  └─ Repositories/Contracts + Repositories/Eloquent
                      └─ Models/*  Eloquent + HasTranslations + InteractsWithMedia
  └─ Http/Controllers/*       тонкий: получить → отдать Inertia::render / redirect
      └─ Services/*           чтение и сборка страниц (page services), кросс-доменная логика
```

### Правила слоёв
1. **Контроллер не знает про Eloquent.** Только Manager (запись) и Repository-интерфейс (чтение).
2. **Manager владеет инвариантами**: генерация слага, транзакции, загрузка медиа, порядок
   элементов, каскады. Любая запись в БД идёт через репозиторий, а не напрямую.
3. **Repository — единственное место с `QueryBuilder`/Eloquent-запросами.** Возвращает модели
   или пагинаторы, не массивы.
4. **DTO не читает `$request` сам.** За это отвечает соответствующий `*DataMapper`.
5. **Переводимые поля** хранятся как JSON через `spatie/laravel-translatable`;
   мапперы всегда нормализуют полный набор локалей.
6. **Списки-структуры** (преимущества, шаги процесса, характеристики) — JSON-колонки,
   переводы лежат **внутри элементов** (`{ro: "", en: "", ru: ""}`), такие поля не входят
   в `$translatable`.
7. **Интерфейсы биндятся** в `App\Providers\RepositoryServiceProvider`.
8. **Роли и права** — `spatie/laravel-permission`; роль `super-admin` обходит проверки через
   `Gate::before`.
9. **Фильтрация/сортировка списков** — `spatie/laravel-query-builder` внутри репозитория.

## Дерево `app/`

```
app/
├── Console/Commands/
├── Data/                     DTO (spatie/laravel-data)
│   └── Mappers/              Request → DTO
├── Enums/                    статусы, типы, роли, права
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/            панель управления (Inertia)
│   │   ├── Api/V1/           публичный JSON API
│   │   └── Site/             публичный сайт (Inertia)
│   ├── Middleware/           HandleInertiaRequests, SetLocale, TrackVisit
│   ├── Requests/{Admin,Api,Site}/
│   └── Resources/            API-ресурсы
├── Managers/                 бизнес-логика записи
├── Models/
│   └── Concerns/             SerializesTranslations, HasSeo, Sortable
├── Notifications/ Mail/
├── Policies/
├── Providers/                App, Repository, Inertia, Route
├── Repositories/
│   ├── Concerns/             SearchesLikeColumns, ...
│   ├── Contracts/            *RepositoryInterface
│   └── Eloquent/             *Repository
├── Services/                 HomePageService, SeoService, SitemapBuilder, ...
└── Support/                  Translation/, Seo/, Media/
```

## Дерево `resources/js/`

```
resources/js/
├── shared/         бренд-токены, i18n-оверрайды, утилиты общие для двух SPA
├── admin/          app.ts, Layouts, Pages, Components/{ui,forms,data}, Composables, Stores, Types, i18n
└── site/           app.ts, Layouts, Pages, Components/{sections,shared,ui}, Composables, Stores, Types, i18n
```

Две точки входа Vite: `resources/js/site/app.ts` и `resources/js/admin/app.ts`,
два CSS-бандла: `resources/css/site.css`, `resources/css/admin.css`.
Алиасы: `@site`, `@admin`, `@shared`, `@`.

## Маршруты

| Файл | Префикс | Назначение |
|---|---|---|
| `routes/web.php` | `/{locale?}` | публичный сайт |
| `routes/admin.php` | `/admin` | панель, `auth` + `can:*` |
| `routes/api.php` | `/api/v1` | JSON API (sanctum для приватных) |

Локаль — сегмент пути: `/ro/...`, `/en/...`, `/ru/...`. Дефолт `ro` (Молдова) без редиректа
с корня — корень отдаёт локаль из `Accept-Language`/cookie.

## Пакеты

| Пакет | Зачем |
|---|---|
| `inertiajs/inertia-laravel` + `@inertiajs/vue3` | SPA без отдельного API |
| `tightenco/ziggy` | маршруты Laravel во Vue |
| `spatie/laravel-data` | DTO |
| `spatie/laravel-permission` | роли и права |
| `spatie/laravel-query-builder` | фильтры/сортировки/инклюды |
| `spatie/laravel-translatable` | переводимые поля моделей |
| `spatie/laravel-medialibrary` | медиа, конверсии, галереи |
| `spatie/laravel-sluggable` | слаги |
| `spatie/laravel-sitemap` | sitemap.xml |
| `spatie/laravel-activitylog` | аудит действий в админке |
| `spatie/schema-org` | JSON-LD микроразметка |
| `intervention/image-laravel` | обработка изображений |
| `laravel/sanctum` | токены API |
| `vue-i18n` | переводы интерфейса |
| `swiper`, `aos`, `leaflet`, `lucide-vue-next`, `@vueuse/core` | слайдеры, анимации, карта, иконки |

**Cookie-согласие** реализовано своим компонентом, а не `spatie/laravel-cookie-consent`:
пакет даёт один Blade-баннер с одной кнопкой и не поддерживает категории согласия
(necessary / analytics / marketing), обязательные по GDPR, и не дружит с Inertia.
Наш `CookieConsent.vue` + `consent_logs` закрывают требование корректно.

**SEO** — свой слой (`seo_meta` + `SeoService` + `HasSeo`) вместо Blade-ориентированных
пакетов: мета-теги уезжают в Inertia-пропсах и рендерятся в `<Head>`, что даёт
редактируемость из админки по каждой сущности и по каждой локали.
