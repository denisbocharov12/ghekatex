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
            key: 'summary',
            label: 'Кратко',
            type: 'textarea',
            translated: true
        },
        {
            key: 'capacity_per_month',
            label: 'Мощность в месяц',
            type: 'number',
            half: true
        },
        {
            key: 'employees_count',
            label: 'Сотрудников',
            type: 'number',
            half: true
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
            label: 'Описание',
            type: 'html',
            translated: true
        },
        {
            key: 'specs',
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

const title = props.item?.id ? 'Производственные мощности — редактирование' : 'Производственные мощности — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="facilities"
    permission="facilities"
    :fields="fields"
    :item="props.item"
    :with-seo="true"
  />
</template>
