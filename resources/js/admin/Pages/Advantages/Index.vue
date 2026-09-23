<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'title',
            label: 'Заголовок',
            type: 'translated'
        },
        {
            key: 'icon',
            label: 'Иконка'
        },
        {
            key: 'value',
            label: 'Значение'
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
    title="Преимущества"
    route-prefix="advantages"
    permission="advantages"
    title-key="title"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
