<script setup lang="ts">
import { computed } from 'vue'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null; types?: { value: string; label: string }[] }>()

const fields = computed<FormField[]>(() => ([
        {
            key: 'name',
            label: 'Название',
            type: 'text',
            translated: true,
            required: true
        },
        {
            key: 'type',
            label: 'Тип',
            type: 'select',
            half: true,
            required: true,
            options: props.types ?? []
        },
        {
            key: 'country_code',
            label: 'Код страны',
            type: 'text',
            half: true
        },
        {
            key: 'address',
            label: 'Адрес',
            type: 'text',
            translated: true,
            required: true
        },
        {
            key: 'city',
            label: 'Город',
            type: 'text',
            translated: true
        },
        {
            key: 'postal_code',
            label: 'Индекс',
            type: 'text',
            half: true
        },
        {
            key: 'working_hours',
            label: 'Часы работы',
            type: 'text',
            translated: true
        },
        {
            key: 'phones',
            label: 'Телефоны',
            type: 'list'
        },
        {
            key: 'emails',
            label: 'E-mail',
            type: 'list'
        },
        {
            key: 'latitude',
            label: 'Широта',
            type: 'text',
            half: true
        },
        {
            key: 'longitude',
            label: 'Долгота',
            type: 'text',
            half: true
        },
        {
            key: 'is_primary',
            label: 'Главный адрес',
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
            key: 'photo',
            label: 'Фотография',
            type: 'media',
            accept: 'image/*'
        }
    ]))

const title = props.item?.id ? 'Офисы и производства — редактирование' : 'Офисы и производства — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="offices"
    permission="offices"
    :fields="fields"
    :item="props.item"
    :with-seo="false"
  />
</template>
