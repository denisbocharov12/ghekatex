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
            key: 'number',
            label: 'Номер'
        },
        {
            key: 'valid_until',
            label: 'Действует до',
            type: 'date'
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
    title="Сертификаты"
    route-prefix="certificates"
    permission="certificates"
    title-key="name"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
