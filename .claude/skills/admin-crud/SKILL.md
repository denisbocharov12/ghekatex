---
name: admin-crud
description: Собрать экран CRUD в админ-панели GHEKATEX на Inertia + Vue 3 (список, форма, локали, медиа, подтверждение удаления). Использовать при добавлении или правке любого раздела админки.
---

# Экран админки GHEKATEX

## Файлы

```
resources/js/admin/Pages/<Module>/Index.vue   список
resources/js/admin/Pages/<Module>/Form.vue    создание и редактирование
```

## Index.vue

```vue
<script setup lang="ts">
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import DataTable from '@admin/Components/data/DataTable.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
</script>
```

- `PageHeader` — заголовок, хлебные крошки, кнопка «Создать» (скрыта без права `*.create`).
- `DataTable` — колонки, поиск, сортировка, пагинация, пустое состояние, действия в строке.
- Удаление — только через `ConfirmModal`, метод `router.delete`.
- Переключатель активности — inline `ToggleSwitch` с `router.patch`, без перезагрузки списка.
- Порядок — drag-and-drop через `SortableList`, отправка массива `[{id, sort_order}]`.

## Form.vue

- `useForm` из `@inertiajs/vue3`, объект формы повторяет DTO.
- Переводимые поля обёрнуты в `<LocaleTabs v-model="form.title">` — по вкладке на локаль,
  вкладка `ro` обязательная и помечена звёздочкой.
- Медиа: `MediaUploader` (одиночный `cover`) и `GalleryUploader` (множественная галерея
  с сортировкой и удалением). Файлы отправляются `forceFormData: true`.
- Богатый текст — `RichTextEditor`, значение html, санитизация на бэкенде.
- Список-структура (шаги, характеристики) — `RepeatableRows` с локальными вкладками внутри строки.
- Блок SEO — `SeoFields` (title, description, og_image, canonical, robots) по локалям,
  сворачиваемый, внизу формы.
- Ошибки — под полем, `form.errors['title.ro']`.
- Кнопка сохранения зафиксирована в нижней панели `FormActions`, состояние `form.processing`.

## Обязательное

- Каждая страница получает `<Head :title="...">`.
- Все подписи интерфейса — через `t('...')` из vue-i18n, никаких строк в разметке.
- Права проверяются и на бэкенде (`can:`), и в UI (`usePermissions().can('foos.update')`).
- Flash-сообщения приходят в `page.props.flash` и показываются `FlashToast`.
- Типы пропсов описываются в `resources/js/admin/Types/`.
