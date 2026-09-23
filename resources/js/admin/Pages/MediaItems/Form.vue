<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null; albums?: Record<string, any>[]; providers?: string[] }>()

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
            label: 'Название',
            type: 'text',
            translated: true
        },
        {
            key: 'album_id',
            label: 'Альбом',
            type: 'select',
            half: true,
            options: optionsFrom(props.albums, 'title')
        },
        {
            key: 'type',
            label: 'Тип',
            type: 'select',
            half: true,
            required: true,
            options: [{ value: 'image', label: 'Фото' }, { value: 'video', label: 'Видео' }]
        },
        {
            key: 'video_provider',
            label: 'Хостинг видео',
            type: 'select',
            half: true,
            options: props.providers?.map((value) => ({ value, label: value })) ?? []
        },
        {
            key: 'video_url',
            label: 'Ссылка на видео',
            type: 'text',
            half: true
        },
        {
            key: 'caption',
            label: 'Подпись',
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
            key: 'file',
            label: 'Файл',
            type: 'media',
            accept: 'image/*,video/mp4,video/webm'
        },
        {
            key: 'poster',
            label: 'Постер видео',
            type: 'media',
            accept: 'image/*'
        }
    ]))

const title = props.item?.id ? 'Материалы галереи — редактирование' : 'Материалы галереи — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="media-items"
    permission="media"
    :fields="fields"
    :item="props.item"
    :with-seo="false"
  />
</template>
