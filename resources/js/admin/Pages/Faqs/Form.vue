<script setup lang="ts">
import { computed } from 'vue'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{
    item?: Record<string, any> | null
    groups?: { value: string; label: string }[]
}>()

const fields = computed<FormField[]>(() => [
    { key: 'question', label: 'Вопрос', type: 'text', translated: true, required: true },
    { key: 'group', label: 'Подборка', type: 'select', half: true, required: true, options: props.groups ?? [] },
    { key: 'sort_order', label: 'Порядок', type: 'number', half: true },
    { key: 'is_active', label: 'Опубликовано', type: 'checkbox' },
    { key: 'answer', label: 'Ответ', type: 'html', translated: true, required: true },
])

const title = props.item?.id ? 'Частые вопросы — редактирование' : 'Частые вопросы — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="faqs"
    permission="faqs"
    :fields="fields"
    :item="props.item"
  />
</template>
