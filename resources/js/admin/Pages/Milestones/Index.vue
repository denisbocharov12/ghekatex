<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'year',
            label: 'Год',
            width: '100px'
        },
        {
            key: 'title',
            label: 'Событие',
            type: 'translated'
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
    title="История компании"
    route-prefix="milestones"
    permission="milestones"
    title-key="title"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
