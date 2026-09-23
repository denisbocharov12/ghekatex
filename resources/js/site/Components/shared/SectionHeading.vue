<script setup lang="ts">
/** Шапка секции: надзаголовок, крупный заголовок и лид. */
withDefaults(
    defineProps<{
        eyebrow?: string | null
        title: string
        text?: string | null
        align?: 'start' | 'center'
    }>(),
    { eyebrow: null, text: null, align: 'start' },
)
</script>

<template>
  <div :class="['flex flex-col gap-5', align === 'center' ? 'items-center text-center' : 'items-start']">
    <p v-if="eyebrow" class="eyebrow">{{ eyebrow }}</p>

    <h2 class="heading-2 max-w-4xl text-balance">{{ title }}</h2>

    <p v-if="text" class="lead">{{ text }}</p>
  </div>
</template>
