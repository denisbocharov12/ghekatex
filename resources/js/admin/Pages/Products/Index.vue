<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'cover_url',
            label: 'Фото',
            type: 'image',
            width: '80px'
        },
        {
            key: 'name',
            label: 'Название',
            type: 'translated'
        },
        {
            key: 'article',
            label: 'Артикул'
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
    title="Изделия"
    route-prefix="products"
    permission="products"
    title-key="name"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
