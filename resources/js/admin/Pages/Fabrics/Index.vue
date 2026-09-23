<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'swatch_url',
            label: 'Образец',
            type: 'image',
            width: '80px'
        },
        {
            key: 'name',
            label: 'Название',
            type: 'translated'
        },
        {
            key: 'weight_gsm',
            label: 'Плотность, г/м²'
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
    title="Ткани"
    route-prefix="fabrics"
    permission="fabrics"
    title-key="name"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
