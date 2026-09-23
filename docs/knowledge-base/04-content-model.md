# 04 — Контентная модель

Локали контента: `ro` (основная), `en`, `ru`. Переводимые поля помечены **(tr)** и
хранятся JSON-колонкой через `spatie/laravel-translatable`.

## Ядро

### `settings`
`key` · `group` (general|contacts|social|analytics|seo|home) · `value` json **(tr при `translatable=true`)** · `type` (string|text|html|image|json|bool) · `translatable` bool.
Хранит: название компании, слоган, телефоны, e-mail, реквизиты, GA4 ID, Яндекс.Метрика ID,
ссылки на соцсети, тексты футера, URL видео на главной.

### `navigation_items`
`menu` (header|footer_1|footer_2|legal) · `parent_id` · `label` **(tr)** · `route_name` · `url` · `sort_order` · `is_active` · `open_in_new_tab`.

### `seo_meta` (полиморфная)
`seoable_type` · `seoable_id` · `title` **(tr)** · `description` **(tr)** · `keywords` **(tr)** ·
`og_title` **(tr)** · `og_description` **(tr)** · `og_image` · `canonical_url` · `robots` · `schema_type`.

### `redirects`
`from_path` · `to_path` · `status_code` · `hits_count` · `is_active`.

### `translation_overrides`
`locale` · `group` · `key` · `value` — правки UI-строк из админки поверх JSON-файлов.

### `consent_logs`
`anonymous_id` · `ip_hash` · `categories` json · `policy_version` · `user_agent` · `created_at`.

## Главная

### `hero_slides`
`title` **(tr)** · `subtitle` **(tr)** · `description` **(tr)** · `cta_label` **(tr)** · `cta_url` ·
`media` (medialibrary: `image`, `video`) · `overlay_opacity` · `sort_order` · `is_active`.

### `advantages`
`icon` (lucide) · `title` **(tr)** · `description` **(tr)** · `value` · `value_suffix` · `sort_order` · `is_active`.
Закрывает «ключевые преимущества» и счётчики мощностей.

## О компании

### `company_milestones` — история
`year` · `title` **(tr)** · `description` **(tr)** · `sort_order` · `is_active`.

### `facilities` — производственные мощности и технологии
`name` **(tr)** · `slug` · `summary` **(tr)** · `description` **(tr)** · `specs` json
(`[{label:{ro,en,ru}, value:{ro,en,ru}}]`) · `capacity_per_month` · `employees_count` ·
`icon` · медиа `cover`/`gallery` · `sort_order` · `is_active`.

### `certificates` — сертификаты и стандарты качества
`name` **(tr)** · `issuer` **(tr)** · `description` **(tr)** · `number` · `issued_at` · `valid_until` ·
медиа `image`, `document` · `sort_order` · `is_active`.

### `partners`
`name` · `description` **(tr)** · `website_url` · `country` · `is_featured` · медиа `logo` · `sort_order` · `is_active`.

## Каталог продукции

### `product_categories` — типы изделий
`name` **(tr)** · `slug` **(tr)** · `description` **(tr)** · `parent_id` · медиа `cover` · `sort_order` · `is_active`.

### `products`

> **Витрина — не магазин.** Цен и корзины нет: изделия показывают, что компания
> умеет шить. Карточка ведёт к форме заявки, партия запускается после
> согласования образца.

`category_id` · `name` **(tr)** · `slug` **(tr)** · `article` · `short_description` **(tr)** ·
`description` **(tr)** · `composition` **(tr)** · `attributes` json (`[{label,value}]` с переводами внутри) ·
`min_order_quantity` · `lead_time_days` · `is_featured` · медиа `cover`/`gallery` · `sort_order` · `is_active`.
Связи: `product_fabric`, `product_treatment` (many-to-many).

### `fabrics` — типы тканей
`name` **(tr)** · `slug` · `description` **(tr)** · `composition` **(tr)** · `weight_gsm` · `color_hex` ·
медиа `swatch` · `sort_order` · `is_active`.

### `treatments` — виды обработки
`name` **(tr)** · `slug` · `description` **(tr)** · `icon` · `sort_order` · `is_active`.

## Вопросы и ответы

### `faqs`
`group` (general|services|production) · `question` **(tr)** · `answer` **(tr)**, html · `sort_order` · `is_active`.

Подборка `general` выводится на главной, `services` — в разделе услуг.

## Услуги

### `services`
`name` **(tr)** · `slug` **(tr)** · `short_description` **(tr)** · `description` **(tr)** · `icon` ·
`highlights` json · `process_steps` json (`[{title,text}]` с переводами внутри) ·
`lead_time` **(tr)** · `is_featured` · медиа `cover`/`gallery` · `sort_order` · `is_active`.
Покрывает производство под заказ, индивидуальную разработку моделей и контроль качества.

## Новости и медиа

### `post_categories`
`name` **(tr)** · `slug` **(tr)** · `sort_order` · `is_active`.

### `posts`
`category_id` · `author_id` · `type` (news|article|review) · `title` **(tr)** · `slug` **(tr)** ·
`excerpt` **(tr)** · `body` **(tr)**, html · `published_at` · `is_featured` · `views_count` ·
`reading_minutes` · медиа `cover`/`gallery` · `is_active`.

### `media_albums`
`title` **(tr)** · `slug` · `description` **(tr)** · `sort_order` · `is_active`.

### `media_items`
`album_id` · `type` (image|video) · `title` **(tr)** · `caption` **(tr)** ·
`video_provider` (youtube|vimeo|file) · `video_url` · медиа `file`/`poster` · `sort_order` · `is_active`.

## Контакты

### `offices`
`type` (office|factory|warehouse) · `name` **(tr)** · `address` **(tr)** · `city` **(tr)** ·
`country_code` · `postal_code` · `phones` json · `emails` json · `working_hours` **(tr)** ·
`latitude` · `longitude` · `is_primary` · `sort_order` · `is_active`.

### `contact_requests`
`name` · `email` · `phone` · `company` · `country` · `subject` · `message` · `source`
(home_quick|contacts|service|product) · `related_type`/`related_id` · `locale` · `status`
(new|in_progress|answered|spam|archived) · `ip_hash` · `user_agent` · `handled_by` · `handled_at` · `admin_note`.

### `subscribers`
`email` · `locale` · `confirmed_at` · `unsubscribed_at` · `token`.

## Статические страницы

### `pages`
`slug` **(tr)** · `template` (default|about|legal|contacts) · `title` **(tr)** · `subtitle` **(tr)** ·
`body` **(tr)**, html · `blocks` json (конструктор секций) · медиа `cover` ·
`is_system` (нельзя удалить) · `is_active`.

Системные записи сидера: `about`, `privacy-policy`, `cookie-policy`, `terms-of-use`.

## Доступ

Роли: `super-admin`, `admin`, `content-manager`, `sales`, `viewer`.

Права по схеме `<область>.<действие>`: `view|create|update|delete` для
`pages · posts · media · products · categories · fabrics · treatments · services ·
certificates · partners · facilities · milestones · offices · requests · settings ·
navigation · seo · translations · users · roles`.

`super-admin` проходит любую проверку (`Gate::before`).
