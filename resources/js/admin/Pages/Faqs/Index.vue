<script setup lang="ts">
import ResourceIndex from '@admin/Components/data/ResourceIndex.vue'
import type { Column, Paginator } from '@admin/Types/forms'

defineProps<{ items: Paginator }>()

const columns: Column[] = [
    { key: 'question', label: 'Вопрос', type: 'translated' },
    { key: 'group', label: 'Подборка', width: '200px' },
    { key: 'sort_order', label: 'Порядок', width: '90px' },
]
</script>

<template>
  <ResourceIndex
    title="Частые вопросы"
    route-prefix="faqs"
    permission="faqs"
    title-key="question"
    :columns="columns"
    :items="items"
  />
</template>
