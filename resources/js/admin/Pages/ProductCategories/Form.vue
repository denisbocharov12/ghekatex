<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null; parents?: Record<string, any>[] }>()

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
            key: 'name',
            label: 'Название',
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
            key: 'parent_id',
            label: 'Родительская категория',
            type: 'select',
            half: true,
            options: optionsFrom(props.parents, 'name')
        },
        {
            key: 'description',
            label: 'Описание',
            type: 'textarea',
            translated: true
        },
        {
            key: 'sort_order',
            label: 'Порядок',
            type: 'number',
            half: true
        },
        {
            key: 'is_active',
            label: 'Опубликовано',
            type: 'checkbox'
        },
        {
            key: 'cover',
            label: 'Обложка',
            type: 'media',
            accept: 'image/*'
        }
    ]))

const title = props.item?.id ? 'Категории продукции — редактирование' : 'Категории продукции — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="product-categories"
    permission="product-categories"
    :fields="fields"
    :item="props.item"
    :with-seo="true"
  />
</template>
