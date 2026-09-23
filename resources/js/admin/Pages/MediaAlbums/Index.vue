<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'title',
            label: 'Название',
            type: 'translated'
        },
        {
            key: 'slug',
            label: 'Слаг'
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
    title="Альбомы галереи"
    route-prefix="media-albums"
    permission="media"
    title-key="title"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
