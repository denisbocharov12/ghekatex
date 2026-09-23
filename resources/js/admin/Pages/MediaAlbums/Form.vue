<script setup lang="ts">
import { computed } from 'vue'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null }>()

const fields = computed<FormField[]>(() => ([
        {
            key: 'title',
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
            key: 'description',
            label: 'Описание',
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
        }
    ]))

const title = props.item?.id ? 'Альбомы галереи — редактирование' : 'Альбомы галереи — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="media-albums"
    permission="media"
    :fields="fields"
    :item="props.item"
    :with-seo="true"
  />
</template>
