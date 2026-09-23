<script setup lang="ts">
/**
 * Фирменный знак как графический элемент.
 *
 * Статичная подпись без обводки и анимации: знак работает как акцент
 * в тёмных блоках, а не как отдельный движущийся объект.
 */
withDefaults(defineProps<{ size?: number; tone?: 'light' | 'dark' }>(), {
    size: 96,
    tone: 'light',
})
</script>

<template>
  <span
    class="relative inline-flex items-center justify-center"
    :style="{ width: `${size}px`, height: `${size}px` }"
    aria-hidden="true"
  >
    <img
      src="/brand/icon_pattern.svg"
      alt=""
      class="opacity-50"
      :style="{ width: `${size * 0.45}px` }"
      :class="tone === 'light' ? 'brightness-0 invert' : ''"
    >
  </span>
</template>
