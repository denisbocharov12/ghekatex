<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null; categories?: Record<string, any>[]; fabrics?: Record<string, any>[]; treatments?: Record<string, any>[] }>()

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
            key: 'article',
            label: 'Артикул',
            type: 'text',
            half: true
        },
        {
            key: 'category_id',
            label: 'Категория',
            type: 'select',
            half: true,
            options: optionsFrom(props.categories, 'name')
        },
        {
            key: 'min_order_quantity',
            label: 'Минимальная партия',
            type: 'number',
            half: true
        },
        {
            key: 'lead_time_days',
            label: 'Срок производства, дней',
            type: 'number',
            half: true
        },
        {
            key: 'short_description',
            label: 'Краткое описание',
            type: 'textarea',
            translated: true
        },
        {
            key: 'composition',
            label: 'Состав',
            type: 'text',
            translated: true
        },
        {
            key: 'fabrics',
            label: 'Ткани',
            type: 'multiselect',
            options: optionsFrom(props.fabrics, 'name')
        },
        {
            key: 'treatments',
            label: 'Обработка',
            type: 'multiselect',
            options: optionsFrom(props.treatments, 'name')
        },
        {
            key: 'is_featured',
            label: 'Избранное',
            type: 'checkbox'
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
            key: 'description',
            label: 'Полное описание',
            type: 'html',
            translated: true
        },
        {
            key: 'attributes',
            label: 'Характеристики',
            type: 'rows',
            rowFields: [
                {
                    key: 'label',
                    label: 'Параметр',
                    translated: true
                },
                {
                    key: 'value',
                    label: 'Значение',
                    translated: true
                }
            ]
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

const title = props.item?.id ? 'Изделия — редактирование' : 'Изделия — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="products"
    permission="products"
    :fields="fields"
    :item="props.item"
    :with-seo="true"
  />
</template>
