<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'

/**
 * Секция с видеофоном и затемнением.
 *
 * Видео грузится только когда секция подходит к экрану: на главной их
 * несколько, и тянуть все мегабайты сразу незачем.
 */
const props = withDefaults(
    defineProps<{
        src: string
        poster?: string | null
        height?: 'tall' | 'medium'
        /** Тон затемнения: синий как у остальных сцен или зелёный второй цвет. */
        tone?: 'ink' | 'malachite'
    }>(),
    { poster: null, height: 'medium', tone: 'ink' },
)

const root = ref<HTMLElement | null>(null)
const video = ref<HTMLVideoElement | null>(null)
const active = ref(false)

let observer: IntersectionObserver | null = null

onMounted(() => {
    if (!root.value) return

    observer = new IntersectionObserver(
        (entries) => {
            const visible = entries[0]?.isIntersecting ?? false

            if (visible && !active.value) {
                active.value = true
            }

            // За кадром видео ставим на паузу — незачем греть процессор
            if (active.value && video.value) {
                visible ? video.value.play().catch(() => {}) : video.value.pause()
            }
        },
        { rootMargin: '200px 0px', threshold: 0.05 },
    )

    observer.observe(root.value)
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
  <section
    ref="root"
    class="grain on-ink relative overflow-hidden text-white"
    :class="[
      props.height === 'tall' ? 'min-h-[85svh]' : 'min-h-[60svh]',
      props.tone === 'malachite' ? 'bg-malachite-900' : 'bg-ink-950',
    ]"
  >
    <video
      v-if="active"
      ref="video"
      class="absolute inset-0 h-full w-full object-cover"
      :poster="props.poster || undefined"
      autoplay
      muted
      loop
      playsinline
      preload="none"
    >
      <source :src="props.src" type="video/mp4">
    </video>

    <img
      v-else-if="props.poster"
      :src="props.poster"
      alt=""
      class="absolute inset-0 h-full w-full object-cover"
      loading="lazy"
      decoding="async"
    >

    <span :class="props.tone === 'malachite' ? 'cinema-veil-malachite' : 'cinema-veil'" />

    <div
      class="relative flex items-center"
      :class="props.height === 'tall' ? 'min-h-[85svh]' : 'min-h-[60svh]'"
    >
      <div class="container-site py-14">
        <slot />
      </div>
    </div>
  </section>
</template>
