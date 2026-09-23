<script setup lang="ts">
import { computed } from 'vue'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null }>()

/** Набор иконок витрины — тот же список, что понимает компонент LucideIcon. */
const iconOptions = [
    'award', 'badge-check', 'box', 'calendar', 'check-circle', 'clock', 'droplet',
    'factory', 'gem', 'globe', 'handshake', 'layers', 'leaf', 'map-pin', 'package',
    'palette', 'printer', 'recycle', 'ruler', 'scissors', 'shield-check', 'shirt',
    'sparkles', 'star', 'target', 'truck', 'users', 'wrench',
].map((value) => ({ value, label: value }))

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
            key: 'icon',
            label: 'Иконка',
            type: 'select',
            half: true,
            options: iconOptions
        },
        {
            key: 'short_description',
            label: 'Краткое описание',
            type: 'textarea',
            translated: true
        },
        {
            key: 'lead_time',
            label: 'Срок выполнения',
            type: 'text',
            translated: true
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
            key: 'highlights',
            label: 'Ключевые условия',
            type: 'rows',
            rowFields: [
                {
                    key: 'icon',
                    label: 'Иконка',
                    type: 'select',
                    options: iconOptions
                },
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
            key: 'process_steps',
            label: 'Этапы работы',
            type: 'rows',
            rowFields: [
                {
                    key: 'title',
                    label: 'Этап',
                    translated: true
                },
                {
                    key: 'text',
                    label: 'Описание',
                    translated: true,
                    type: 'textarea'
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

const title = props.item?.id ? 'Услуги — редактирование' : 'Услуги — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="services"
    permission="services"
    :fields="fields"
    :item="props.item"
    :with-seo="true"
  />
</template>
