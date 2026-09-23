<script setup lang="ts">
import { computed } from 'vue'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null }>()

const fields = computed<FormField[]>(() => ([
        {
            key: 'name',
            label: 'Название',
            type: 'text',
            translated: true,
            required: true
        },
        {
            key: 'issuer',
            label: 'Кем выдан',
            type: 'text',
            translated: true
        },
        {
            key: 'description',
            label: 'Описание',
            type: 'textarea',
            translated: true
        },
        {
            key: 'number',
            label: 'Номер',
            type: 'text',
            half: true
        },
        {
            key: 'issued_at',
            label: 'Дата выдачи',
            type: 'date',
            half: true
        },
        {
            key: 'valid_until',
            label: 'Действует до',
            type: 'date',
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
            key: 'image',
            label: 'Изображение',
            type: 'media',
            accept: 'image/*'
        },
        {
            key: 'document',
            label: 'Документ PDF',
            type: 'media',
            accept: 'application/pdf'
        }
    ]))

const title = props.item?.id ? 'Сертификаты — редактирование' : 'Сертификаты — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="certificates"
    permission="certificates"
    :fields="fields"
    :item="props.item"
    :with-seo="false"
  />
</template>
