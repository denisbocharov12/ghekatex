# GHEKATEX — корпоративный сайт

Сайт производителя женской одежды «Ghekatex Group» SRL (Республика Молдова):
витрина на трёх языках и панель управления, из которой редактируется каждая
страница, каждый блок и каждая строка интерфейса.

**Стек:** Laravel 12 · PHP 8.2 · Inertia 2 · Vue 3 (TypeScript) · Tailwind CSS 4 · Vite 7.

---

## Запуск

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed
php artisan storage:link

# демонстрационные фотографии (необязательно, ~14 МБ)
node database/seed-media/download.mjs
php artisan db:seed --class=MediaSeeder
```

Разработка — два процесса:

```bash
npm run dev
```

```bash
php artisan serve
```

Витрина: `http://127.0.0.1:8000/ro` · Панель: `http://127.0.0.1:8000/admin`

Учётные записи из сидера (**сменить на боевом сервере**):

| Роль | E-mail | Пароль |
|---|---|---|
| Супер-администратор | `admin@ghekatex.md` | `password` |
| Контент-менеджер | `editor@ghekatex.md` | `password` |

## Проверки

```bash
php artisan test
```

```bash
./vendor/bin/pint
```

```bash
npm run build
```

---

## Структура

```
app/
├── Data/            DTO и мапперы (spatie/laravel-data)
│   ├── Mappers/     Request → DTO, нормализация переводов
│   └── Schemas/     описание полей раздела для формы админки
├── Enums/           роли, права, статусы, типы
├── Http/
│   ├── Controllers/{Admin,Site,Api/V1}
│   ├── Middleware/  SetLocale, HandleInertiaRequests
│   └── Requests/{Admin,Site}
├── Managers/        бизнес-логика записи: транзакции, слаги, медиа
├── Models/          Eloquent + переводы + медиа + журнал действий
├── Policies/
├── Providers/       App, Repository
├── Repositories/    Contracts + Eloquent
├── Services/        сборка страниц, SEO, sitemap, настройки, переводы
└── Support/         Presenters, Seo, Translation

resources/js/
├── shared/          общее для двух SPA
├── site/            витрина
└── admin/           панель управления
```

Поток данных: `FormRequest → DataMapper → Data → Manager → Repository → Model`.
Контроллер не обращается к Eloquent напрямую; менеджер пишет только через репозиторий.

Подробности — в [`docs/knowledge-base`](docs/knowledge-base):

| Документ | О чём |
|---|---|
| [01-brief.md](docs/knowledge-base/01-brief.md) | техническое задание заказчика |
| [02-architecture.md](docs/knowledge-base/02-architecture.md) | слои, правила, пакеты |
| [03-brand.md](docs/knowledge-base/03-brand.md) | логотип, палитра, типографика |
| [04-content-model.md](docs/knowledge-base/04-content-model.md) | таблицы и поля |
| [05-roadmap.md](docs/knowledge-base/05-roadmap.md) | план работ и чек-лист сдачи |

Рабочие инструкции для повторяющихся задач лежат в `.claude/skills/`:
`domain-module`, `admin-crud`, `site-section`.

---

## Локализация

Контент — румынский (основной), английский и русский. Язык задаётся первым
сегментом пути: `/ro/...`, `/en/...`, `/ru/...`. Корень отдаёт язык, определённый
по cookie и заголовку `Accept-Language`.

- Переводимые поля моделей хранятся JSON-колонкой (`spatie/laravel-translatable`).
- Строки интерфейса лежат в `resources/js/{site,admin}/i18n/*.json`.
- Раздел «Переводы интерфейса» в панели правит строки поверх файлов, не меняя их:
  релиз с новыми текстами не затирает работу редактора.

## SEO

- `seo_meta` — полиморфная карточка на каждую сущность, все поля по локалям.
- `/sitemap.xml` собирается из контента, каждая ссылка с `hreflang` на три языка.
- `/robots.txt` закрывает `/admin` и `/api`.
- JSON-LD: Organization, BreadcrumbList, Product, Service, NewsArticle, LocalBusiness.
- Раздел «Редиректы» перехватывает 404 и отправляет на новый адрес.

## Приватность

- Баннер cookie с категориями (необходимые / аналитика / маркетинг).
- Счётчики GA4 и Яндекс.Метрики подключаются **только** после согласия.
- Журнал согласий `consent_logs` хранит анонимный идентификатор и хеш IP.
- Страницы политики конфиденциальности, cookie и условий создаются сидером
  и редактируются из панели.

## Медиа

- Изображения и документы — `spatie/laravel-medialibrary`, конверсии под каждый блок.
- Видео обложки и фоновых секций лежат в `public/media/video`.
- Ссылки на файлы строятся от корня сайта (`/storage/...`), поэтому одинаково
  работают локально, на стенде и в проде.

## API

Только чтение, префикс `/api/v1`: категории, изделия, услуги, публикации.
Язык ответа — из `Accept-Language`. Ограничение: 60 запросов в минуту.
