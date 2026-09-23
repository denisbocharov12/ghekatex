<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
        {
            key: 'logo_url',
            label: 'Логотип',
            type: 'image',
            width: '80px'
        },
        {
            key: 'name',
            label: 'Название'
        },
        {
            key: 'country_code',
            label: 'Страна'
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
    title="Партнёры"
    route-prefix="partners"
    permission="partners"
    title-key="name"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
