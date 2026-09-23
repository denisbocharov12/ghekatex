---
name: site-section
description: Сверстать секцию или страницу публичного сайта GHEKATEX в фирменном стиле (навы + латунь, Cormorant + Inter, паттерн знака, AOS). Использовать при создании любого блока витрины.
---

# Секция публичного сайта GHEKATEX

## Каркас

```vue
<section class="section" data-aos="fade-up">
  <div class="container">
    <p class="eyebrow">{{ t('section.eyebrow') }}</p>
    <h2 class="heading-2">{{ title }}</h2>
    ...
  </div>
</section>
```

Утилиты объявлены в `resources/css/site.css`:
`.section` (py-24 lg:py-32) · `.section--dark` (фон `--gradient-hero`, светлый текст) ·
`.container` (max-w-[1280px], px-6) · `.eyebrow` · `.heading-1..4` · `.btn`, `.btn--primary`,
`.btn--accent`, `.btn--ghost` · `.card` · `.pattern-veil` (подложка `icon_pattern.svg`, opacity .05).

## Правила

- Ритм: тёмная секция не идёт подряд с тёмной — чередовать светлую `fabric-50` и тёмную.
- Фото — главный герой: минимум текста поверх, `gradient-veil` под подписью.
- Акцент `accent-500` — тонкая линия под надзаголовком, иконки, hover-рамки. Не заливать им блоки.
- Углы прямые (radius 2px). Тени — только на hover карточек, мягкие и холодные.
- Анимации: `data-aos="fade-up"`, задержка `:data-aos-delay="i * 80"` до 240 мс.
  Всё отключается при `prefers-reduced-motion`.
- Изображения — `loading="lazy"`, `decoding="async"`, явные `width`/`height`, `srcset`
  из конверсий medialibrary.
- Каждая страница: `<Head>` с title/description/canonical/hreflang из пропса `seo`
  и JSON-LD из пропса `schema`.
- Тексты — только из пропсов (контент из БД) или `t()` (интерфейс). Хардкод запрещён.
- Секция без данных не рендерится: `v-if="items.length"`.

## Адаптив

Брейкпоинты Tailwind: `sm 640 · md 768 · lg 1024 · xl 1280`.
Сетки: 1 → 2 (`md`) → 3 или 4 (`lg`). Горизонтальные скроллы только для лукбука,
со скрытым скроллбаром и snap.

## Доступность

`aria-label` на иконочных кнопках, `alt` на всех изображениях (из `media.alt` или названия),
фокус видим, порядок табуляции естественный, слайдеры управляются стрелками.
