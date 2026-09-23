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
            key: 'title',
            label: 'Заголовок',
            type: 'translated'
        },
        {
            key: 'type',
            label: 'Тип'
        },
        {
            key: 'published_at',
            label: 'Публикация',
            type: 'date'
        }
    ]
</script>

<template>
  <ResourceIndex
    title="Новости и статьи"
    route-prefix="posts"
    permission="posts"
    title-key="title"
    :columns="columns"
    :items="items"
    :reorderable="false"
  />
</template>
