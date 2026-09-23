# 05 — План работ

| Фаза | Содержание | Статус |
|---|---|---|
| 0 | Анализ ТЗ, knowledge base, скилы | ✅ |
| 1 | Laravel 12 + Inertia + Vue 3 + Tailwind 4, пакеты, Vite с двумя входами | ✅ |
| 2 | Каркас слоёв: Enums, Support, Concerns, Repository/Manager/Data-базовые классы, провайдеры | ✅ |
| 3 | Миграции, модели, фабрики, сидеры (роли, права, настройки, демо-контент) | ✅ |
| 4 | Локализация: SetLocale, маршруты с локалью, vue-i18n, редактор переводов | ✅ |
| 5 | SEO: `seo_meta`, `HasSeo`, `SeoService`, sitemap, robots, hreflang, JSON-LD, редиректы | ✅ |
| 6 | Админ-панель: layout, навигация, CRUD всех сущностей, медиа, дашборд | ✅ |
| 7 | Публичный сайт: layout, дизайн-система, все страницы из ТЗ | ✅ |
| 8 | Формы обратной связи, письма, антиспам, cookie-баннер, юридические страницы | ✅ |
| 9 | Аналитика GA4 + Яндекс.Метрика с учётом согласия, соцсети | ✅ |
| 10 | Тесты, Pint, сборка, README и инструкция по деплою | ✅ |
| 11 | Редизайн: видеообложка, полноэкранное меню, FAQ, наполнение фотографиями | ✅ |

## Команды

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run dev          # разработка
npm run build        # прод-сборка
php artisan serve
```

Вход в админку после сидера: `admin@ghekatex.md` / `password` (сменить на проде).

## Проверки перед сдачей

- `php artisan test`
- `./vendor/bin/pint --test`
- `npm run build`
- Lighthouse ≥ 90 по всем осям на главной
- Валидные `sitemap.xml`, `robots.txt`, hreflang на всех локалях
- Формы: honeypot + rate limit + запись в `contact_requests` + письмо
- Cookie-баннер: аналитика не грузится без согласия
- 88 тестов: 48 экранов панели, 27 страниц витрины на трёх языках, формы, редиректы, API
