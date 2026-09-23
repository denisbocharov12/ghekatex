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
            key: 'capacity_per_month',
            label: 'Мощность'
        },
        {
            key: 'employees_count',
            label: 'Сотрудников'
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
    title="Производственные мощности"
    route-prefix="facilities"
    permission="facilities"
    title-key="name"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
