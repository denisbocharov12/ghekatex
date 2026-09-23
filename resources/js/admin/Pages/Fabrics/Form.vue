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
            key: 'slug',
            label: 'Слаг',
            type: 'text',
            half: true,
            hint: 'Оставьте пустым — построим из названия'
        },
        {
            key: 'weight_gsm',
            label: 'Плотность, г/м²',
            type: 'number',
            half: true
        },
        {
            key: 'color_hex',
            label: 'Цвет образца',
            type: 'color',
            half: true
        },
        {
            key: 'composition',
            label: 'Состав',
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
            key: 'swatch',
            label: 'Фото выкраски',
            type: 'media',
            accept: 'image/*'
        }
    ]))

const title = props.item?.id ? 'Ткани — редактирование' : 'Ткани — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="fabrics"
    permission="fabrics"
    :fields="fields"
    :item="props.item"
    :with-seo="false"
  />
</template>
