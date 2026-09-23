<script setup lang="ts">
import { computed } from 'vue'
import { useWindowScroll, useWindowSize } from '@vueuse/core'

/** Тонкая линия прогресса чтения — единственный элемент поверх шапки. */
const { y } = useWindowScroll()
const { height } = useWindowSize()

const progress = computed(() => {
    if (typeof document === 'undefined') return 0

    const total = document.documentElement.scrollHeight - height.value

    return total > 0 ? Math.min(1, Math.max(0, y.value / total)) : 0
})
</script>

<template>
  <span class="scroll-progress" :style="{ transform: `scaleX(${progress})` }" aria-hidden="true" />
</template>
