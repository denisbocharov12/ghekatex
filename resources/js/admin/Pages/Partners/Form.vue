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
            required: true,
            half: true
        },
        {
            key: 'website_url',
            label: 'Сайт',
            type: 'text',
            half: true
        },
        {
            key: 'country_code',
            label: 'Код страны',
            type: 'text',
            half: true,
            hint: 'Две буквы: IT, PL, ES'
        },
        {
            key: 'description',
            label: 'Описание',
            type: 'textarea',
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
            key: 'logo',
            label: 'Логотип',
            type: 'media',
            accept: 'image/*'
        }
    ]))

const title = props.item?.id ? 'Партнёры — редактирование' : 'Партнёры — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="partners"
    permission="partners"
    :fields="fields"
    :item="props.item"
    :with-seo="false"
  />
</template>
