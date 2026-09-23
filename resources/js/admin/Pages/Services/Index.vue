<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'cover_url',
            label: 'Обложка',
            type: 'image',
            width: '80px'
        },
        {
            key: 'name',
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
    title="Услуги"
    route-prefix="services"
    permission="services"
    title-key="name"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
