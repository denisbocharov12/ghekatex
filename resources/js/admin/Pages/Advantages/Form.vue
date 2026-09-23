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
            key: 'icon',
            label: 'Иконка',
            type: 'select',
            half: true,
            options: iconOptions,
            required: true
        },
        {
            key: 'value',
            label: 'Число счётчика',
            type: 'text',
            half: true
        },
        {
            key: 'value_suffix',
            label: 'Подпись к числу',
            type: 'text',
            translated: true
        },
        {
            key: 'is_counter',
            label: 'Показывать как счётчик',
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
        }
    ]))

const title = props.item?.id ? 'Преимущества — редактирование' : 'Преимущества — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="advantages"
    permission="advantages"
    :fields="fields"
    :item="props.item"
    :with-seo="false"
  />
</template>
