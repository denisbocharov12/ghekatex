<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{ tone?: string; label: string }>(), { tone: 'muted' })

const classes = computed(() => {
    const map: Record<string, string> = {
        success: 'bg-emerald-50 text-emerald-700',
        danger: 'bg-red-50 text-red-700',
        warning: 'bg-amber-50 text-amber-700',
        info: 'bg-sky-50 text-sky-700',
        accent: 'bg-accent-50 text-accent-700',
        muted: 'bg-steel-100 text-steel-600',
    }

    return map[props.tone] ?? map.muted
})
</script>

<template>
  <span class="badge" :class="classes">{{ label }}</span>
</template>
