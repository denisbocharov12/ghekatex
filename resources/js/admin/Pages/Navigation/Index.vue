<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'label',
            label: 'Пункт',
            type: 'translated'
        },
        {
            key: 'menu',
            label: 'Меню'
        },
        {
            key: 'route_name',
            label: 'Маршрут'
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
    title="Меню сайта"
    route-prefix="navigation"
    permission="navigation"
    title-key="label"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
