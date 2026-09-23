<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null; types?: string[]; categories?: Record<string, any>[]; authors?: Record<string, any>[] }>()

/** Справочник в список опций: подпись берём из перевода основной локали. */
function optionsFrom(items: Record<string, any>[] | undefined, key: string) {
    const primary = (usePage().props.locales as { default: string })?.default ?? 'ro'

    return (items ?? []).map((item) => ({
        value: item.id as number,
        label: typeof item[key] === 'object' ? item[key]?.[primary] ?? '—' : String(item[key] ?? '—'),
    }))
}

const fields = computed<FormField[]>(() => ([
        {
            key: 'title',
            label: 'Заголовок',
            type: 'text',
            translated: true,
            required: true
        },
        {
            key: 'slug',
            label: 'Слаг',
            type: 'text',
            half: true,
            hint: 'Оставьте пустым — построим из названия'
        },
        {
            key: 'type',
            label: 'Тип материала',
            type: 'select',
            half: true,
            required: true,
            options: props.types?.map((value) => ({ value, label: value })) ?? []
        },
        {
            key: 'category_id',
            label: 'Рубрика',
            type: 'select',
            half: true,
            options: optionsFrom(props.categories, 'name')
        },
        {
            key: 'author_id',
            label: 'Автор',
            type: 'select',
            half: true,
            options: optionsFrom(props.authors, 'name')
        },
        {
            key: 'published_at',
            label: 'Дата публикации',
            type: 'date',
            half: true
        },
        {
            key: 'reading_minutes',
            label: 'Время чтения, мин',
            type: 'number',
            half: true
        },
        {
            key: 'excerpt',
            label: 'Анонс',
            type: 'textarea',
            translated: true
        },
        {
            key: 'is_featured',
            label: 'Избранное',
            type: 'checkbox'
        },
        {
            key: 'is_active',
            label: 'Опубликовано',
            type: 'checkbox'
        },
        {
            key: 'body',
            label: 'Текст материала',
            type: 'html',
            translated: true
        },
        {
            key: 'cover',
            label: 'Обложка',
            type: 'media',
            accept: 'image/*'
        },
        {
            key: 'gallery',
            label: 'Галерея',
            type: 'gallery'
        }
    ]))

const title = props.item?.id ? 'Новости и статьи — редактирование' : 'Новости и статьи — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="posts"
    permission="posts"
    :fields="fields"
    :item="props.item"
    :with-seo="true"
  />
</template>
