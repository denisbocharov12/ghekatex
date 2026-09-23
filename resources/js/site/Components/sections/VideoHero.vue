<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useContactDialog } from '@site/Composables/contactDialog'
import { ArrowDown } from 'lucide-vue-next'
import type { HeroSlide } from '@site/Types'

/**
 * Обложка главной: видео во всю высоту экрана, поверх — заголовок.
 *
 * Это фон, а не ролик: звука нет, управления нет, зацикливается. Пока
 * видео не готово, на его месте стоит постер — экран не должен моргать
 * чёрным на медленном соединении.
 */
const props = defineProps<{ slide: HeroSlide }>()

const video = ref<HTMLVideoElement | null>(null)
const playing = ref(false)

/** Заголовок выезжает построчно, поэтому разбиваем его по словам. */
const titleWords = computed(() => (props.slide.title ?? '').split(/\s+/).filter(Boolean))

onMounted(() => {
    const element = video.value

    if (!element) return

    element.addEventListener('playing', () => (playing.value = true), { once: true })

    const start = (): void => {
        element.play().catch(() => {})
    }

    start()

    // Часть браузеров запускает автовоспроизведение только после того, как
    // посетитель что-то сделал на странице. Пробуем ещё раз при первом
    // касании или прокрутке — до тех пор виден постер.
    const retry = (): void => {
        if (element.paused) start()
    }

    const events: Array<keyof WindowEventMap> = ['pointerdown', 'touchstart', 'scroll', 'keydown']

    events.forEach((event) => window.addEventListener(event, retry, { once: true, passive: true }))

    onBeforeUnmount(() => {
        events.forEach((event) => window.removeEventListener(event, retry))
    })
})

const { openDialog } = useContactDialog()

/** Призыв, ведущий на контакты, открывает модалку вместо перехода. */
function isContactLink(url: string | null | undefined): boolean {
    return Boolean(url && (url.includes('/contacts') || url.endsWith('#request')))
}
</script>

<template>
  <section class="grain on-ink relative h-svh min-h-[34rem] w-full overflow-hidden bg-ink-950 text-white">
    <img
      v-if="props.slide.image"
      :src="props.slide.image"
      :alt="props.slide.title"
      class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000"
      :class="playing ? 'opacity-0' : 'opacity-100'"
      fetchpriority="high"
      decoding="async"
    >

    <video
      v-if="props.slide.video"
      ref="video"
      class="absolute inset-0 h-full w-full object-cover"
      :poster="props.slide.image || undefined"
      autoplay
      muted
      loop
      playsinline
      preload="auto"
    >
      <source :src="props.slide.video" type="video/mp4">
    </video>

    <span class="hero-veil" />

    <div class="relative z-[2] flex h-full flex-col pb-10 pt-[var(--header-height)]">
      <!-- Обложка держит текст по оптическому центру экрана на любой высоте -->
      <div class="container-site flex flex-1 flex-col justify-center py-10">
        <p v-if="props.slide.eyebrow" class="eyebrow animate-[line-rise_0.9s_cubic-bezier(0.22,1,0.36,1)_both]">
          {{ props.slide.eyebrow }}
        </p>

        <h1 class="display-1 mt-6 max-w-[15ch] text-balance text-white">
          <span
            v-for="(word, position) in titleWords"
            :key="position"
            class="line-rise"
          >
            <span :style="{ animationDelay: `${120 + position * 70}ms` }">{{ word }}&nbsp;</span>
          </span>
        </h1>

        <p
          v-if="props.slide.description"
          class="mt-6 max-w-lg text-base leading-relaxed text-white/75 lg:text-lg"
        >
          {{ props.slide.description }}
        </p>

        <div v-if="props.slide.cta || props.slide.secondary_cta" class="mt-7 flex flex-wrap gap-3">
          <button
            v-if="props.slide.cta && isContactLink(props.slide.cta.url)"
            type="button"
            class="btn btn-accent"
            @click="openDialog()"
          >
            {{ props.slide.cta.label }}
          </button>
          <a v-else-if="props.slide.cta" :href="props.slide.cta.url" class="btn btn-accent">
            {{ props.slide.cta.label }}
          </a>
          <a v-if="props.slide.secondary_cta" :href="props.slide.secondary_cta.url" class="btn btn-outline">
            {{ props.slide.secondary_cta.label }}
          </a>
        </div>
      </div>

      <!-- Служебная строка остаётся прижатой к низу кадра -->
      <div class="container-site flex shrink-0 items-end justify-between gap-6 border-t border-white/15 pt-6">
        <a href="#intro" class="link-arrow">
          {{ $t('home.hero_scroll') }}
          <ArrowDown :size="15" class="animate-bounce" />
        </a>
      </div>
    </div>
  </section>
</template>
