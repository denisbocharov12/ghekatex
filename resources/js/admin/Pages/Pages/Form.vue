<script setup lang="ts">
import { computed } from 'vue'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null; templates?: { value: string; label: string }[] }>()

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
            key: 'template',
            label: 'Шаблон',
            type: 'select',
            half: true,
            required: true,
            options: props.templates ?? []
        },
        {
            key: 'subtitle',
            label: 'Подзаголовок',
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
            key: 'body',
            label: 'Содержимое',
            type: 'html',
            translated: true
        },
        {
            key: 'cover',
            label: 'Обложка',
            type: 'media',
            accept: 'image/*'
        }
    ]))

const title = props.item?.id ? 'Страницы — редактирование' : 'Страницы — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="pages"
    permission="pages"
    :fields="fields"
    :item="props.item"
    :with-seo="true"
  />
</template>
