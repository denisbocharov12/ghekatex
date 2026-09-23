<script setup lang="ts">
import { computed, ref } from 'vue'
import { PlayCircle, X, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { onKeyStroke } from '@vueuse/core'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import type { Breadcrumb, MediaAlbum, MediaEntry, SeoProps } from '@site/Types'

const props = defineProps<{ seo: SeoProps; breadcrumbs: Breadcrumb[]; album: MediaAlbum }>()

const items = computed<MediaEntry[]>(() => props.album.items ?? [])

/** Лайтбокс: null означает, что просмотр закрыт. */
const openIndex = ref<number | null>(null)
const current = computed(() => (openIndex.value === null ? null : items.value[openIndex.value] ?? null))

function step(delta: number): void {
    if (openIndex.value === null || items.value.length === 0) return

    openIndex.value = (openIndex.value + delta + items.value.length) % items.value.length
}

onKeyStroke('Escape', () => (openIndex.value = null))
onKeyStroke('ArrowLeft', () => step(-1))
onKeyStroke('ArrowRight', () => step(1))

function embedUrl(item: MediaEntry): string | null {
    if (!item.video) return null

    if (item.provider === 'youtube') {
        const id = item.video.split(/[/?=]/).filter(Boolean).pop()

        return `https://www.youtube-nocookie.com/embed/${id}`
    }

    if (item.provider === 'vimeo') {
        const id = item.video.split('/').filter(Boolean).pop()

        return `https://player.vimeo.com/video/${id}`
    }

    return item.video
}
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero
      :eyebrow="$t('nav.media')"
      :title="props.album.title"
      :subtitle="props.album.description"
      :breadcrumbs="props.breadcrumbs"
    />

    <section class="section">
      <div class="container-site">
        <div v-if="items.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <button
            v-for="(item, index) in items"
            :key="item.id"
            type="button"
            class="media-frame media-veil group relative block aspect-4/3 border border-bone-200 bg-bone-50"
            :aria-label="item.title || $t('common.open')" v-reveal="(index % 3) * 60"
            @click="openIndex = index"
          >
            <img
              v-if="item.thumb"
              :src="item.thumb"
              :alt="item.title || ''"
              loading="lazy"
              decoding="async"
              class="h-full w-full object-cover"
            >
            <span v-else class="pattern-veil block h-full w-full" aria-hidden="true" />

            <span v-if="item.type === 'video'" class="absolute inset-0 z-10 flex items-center justify-center text-white">
              <PlayCircle :size="48" class="drop-shadow-lg" />
            </span>

            <span v-if="item.title" class="absolute inset-x-0 bottom-0 z-10 p-4 text-start text-sm font-medium text-white">
              {{ item.title }}
            </span>
          </button>
        </div>

        <p v-else class="text-center text-sm text-bone-400">{{ $t('media.empty') }}</p>
      </div>
    </section>

    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        leave-active-class="transition duration-150 ease-in"
        leave-to-class="opacity-0"
      >
        <div
          v-if="current"
          class="fixed inset-0 z-[80] flex items-center justify-center bg-primary-950/95 p-4"
          role="dialog"
          aria-modal="true"
          @click.self="openIndex = null"
        >
          <button
            type="button"
            class="absolute right-4 top-4 inline-flex h-12 w-12 items-center justify-center text-white/70 hover:text-white"
            :aria-label="$t('nav.close')"
            @click="openIndex = null"
          >
            <X :size="24" />
          </button>

          <button
            v-if="items.length > 1"
            type="button"
            class="absolute left-4 inline-flex h-12 w-12 items-center justify-center text-white/70 hover:text-white"
            :aria-label="$t('common.prev')"
            @click="step(-1)"
          >
            <ChevronLeft :size="28" />
          </button>

          <figure class="max-h-full w-full max-w-5xl">
            <div v-if="current.type === 'video' && embedUrl(current)" class="aspect-video w-full bg-black">
              <iframe
                :src="embedUrl(current) || undefined"
                :title="current.title || 'GHEKATEX'"
                class="h-full w-full"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              />
            </div>

            <img
              v-else-if="current.full || current.thumb"
              :src="current.full || current.thumb || ''"
              :alt="current.title || ''"
              class="mx-auto max-h-[80vh] w-auto object-contain"
            >

            <figcaption v-if="current.title || current.caption" class="mt-4 text-center text-sm text-white/70">
              <span class="font-medium text-white">{{ current.title }}</span>
              <span v-if="current.caption"> — {{ current.caption }}</span>
            </figcaption>
          </figure>

          <button
            v-if="items.length > 1"
            type="button"
            class="absolute right-4 inline-flex h-12 w-12 items-center justify-center text-white/70 hover:text-white"
            :aria-label="$t('common.next')"
            @click="step(1)"
          >
            <ChevronRight :size="28" />
          </button>
        </div>
      </Transition>
    </Teleport>
  </SiteLayout>
</template>
