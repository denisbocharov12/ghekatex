<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'file_url',
            label: 'Файл',
            type: 'image',
            width: '80px'
        },
        {
            key: 'title',
            label: 'Название',
            type: 'translated'
        },
        {
            key: 'type',
            label: 'Тип'
        },
        {
            key: 'sort_order',
            label: 'Порядок',
            width: '90px'
        }
    ]
</script>

<template>
  <ResourceIndex
    title="Материалы галереи"
    route-prefix="media-items"
    permission="media"
    title-key="title"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
