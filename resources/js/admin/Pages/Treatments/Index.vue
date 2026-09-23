<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'name',
            label: 'Название',
            type: 'translated'
        },
        {
            key: 'icon',
            label: 'Иконка'
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
    title="Виды обработки"
    route-prefix="treatments"
    permission="treatments"
    title-key="name"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
