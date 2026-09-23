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
            key: 'cta_url',
            label: 'Ссылка кнопки'
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
    title="Слайдер главной"
    route-prefix="hero-slides"
    permission="hero-slides"
    title-key="title"
    :columns="columns"
    :items="items"
    :reorderable="true"
  />
</template>
