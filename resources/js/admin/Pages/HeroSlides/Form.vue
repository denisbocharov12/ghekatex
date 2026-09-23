<script setup lang="ts">
import { computed } from 'vue'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null }>()

const fields = computed<FormField[]>(() => ([
        {
            key: 'eyebrow',
            label: 'Надзаголовок',
            type: 'text',
            translated: true
        },
        {
            key: 'title',
            label: 'Заголовок',
            type: 'text',
            translated: true,
            required: true
        },
        {
            key: 'description',
            label: 'Описание',
            type: 'textarea',
            translated: true
        },
        {
            key: 'cta_label',
            label: 'Текст кнопки',
            type: 'text',
            translated: true
        },
        {
            key: 'cta_url',
            label: 'Ссылка кнопки',
            type: 'text',
            half: true
        },
        {
            key: 'secondary_cta_label',
            label: 'Текст второй кнопки',
            type: 'text',
            translated: true
        },
        {
            key: 'secondary_cta_url',
            label: 'Ссылка второй кнопки',
            type: 'text',
            half: true
        },
        {
            key: 'overlay_opacity',
            label: 'Затемнение фона, %',
            type: 'number',
            half: true,
            hint: 'От 0 до 100'
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
            key: 'image',
            label: 'Фоновое изображение',
            type: 'media',
            accept: 'image/*'
        },
        {
            key: 'video',
            label: 'Фоновое видео',
            type: 'media',
            accept: 'video/mp4,video/webm',
            hint: 'Без звука, зацикленное'
        }
    ]))

const title = props.item?.id ? 'Слайдер главной — редактирование' : 'Слайдер главной — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="hero-slides"
    permission="hero-slides"
    :fields="fields"
    :item="props.item"
    :with-seo="false"
  />
</template>
