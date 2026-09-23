---
name: domain-module
description: Создать или изменить доменный модуль GHEKATEX (модель, миграция, репозиторий, менеджер, DTO, маппер, права). Использовать при добавлении новой сущности контента или изменении существующей на бэкенде.
---

# Доменный модуль GHEKATEX

Порядок и состав файлов для сущности `Foo` (`app/`):

1. `Models/Foo.php` — `HasTranslations`, `InteractsWithMedia`, `SerializesTranslations`,
   скоупы `active()` и `ordered()`, `$translatable`, `$fillable`, `$casts`,
   `registerMediaCollections()`, `registerMediaConversions()`.
2. `database/migrations/*_create_foos_table.php` — переводимые поля `json`,
   `sort_order` `unsignedInteger` default 0, `is_active` boolean default true, индексы на `slug`, `is_active`.
3. `Repositories/Contracts/FooRepositoryInterface.php` — только методы, которые реально нужны.
4. `Repositories/Eloquent/FooRepository.php` — единственное место с Eloquent/QueryBuilder.
   Списки для админки — через `Spatie\QueryBuilder\QueryBuilder` с `allowedFilters`/`allowedSorts`.
5. `Data/FooData.php` — DTO на `Spatie\LaravelData\Data`, метод `toModelAttributes(): array`.
6. `Data/Mappers/FooDataMapper.php` — `public static function fromRequest(FormRequest $r): FooData`.
   Переводимые поля прогонять через `TranslationNormalizer::pairs()`, чтобы всегда были все локали.
7. `Managers/FooManager.php` — `create/update/delete`, `DB::transaction`, слаг, медиа, порядок.
8. `Http/Requests/Admin/FooRequest.php` — правила по локалям: `title.ro => required|string|max:255`,
   `title.en`/`title.ru` => `nullable`.
9. `Http/Controllers/Admin/FoosController.php` — тонкий, `Inertia::render('Foos/Index'|'Foos/Form')`.
10. Биндинг интерфейса в `Providers/RepositoryServiceProvider.php`.
11. Права в `Enums/Permission.php` + сидер `RolePermissionSeeder`.
12. Маршруты в `routes/admin.php` внутри группы `can:foos.view` и т.д.

## Жёсткие правила

- Контроллер не обращается к Eloquent и не вызывает репозиторий на запись.
- Менеджер пишет только через репозиторий.
- DTO не читает `$request`; это делает маппер.
- Списки-структуры (`highlights`, `process_steps`, `specs`, `attributes`) — JSON-колонки,
  переводы **внутри элементов**, такие поля не входят в `$translatable`.
- Никаких `Str::slug()` в контроллере — только в менеджере.
- Комментарии на русском, по делу, без пересказа кода.

## Локали

`config('app.available_locales')` → `['ro', 'en', 'ru']`, основная `ro`.
Слаг генерируется из `ro`, при отсутствии — из `en`.
