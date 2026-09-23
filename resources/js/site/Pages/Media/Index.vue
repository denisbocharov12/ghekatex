<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Images, ArrowUpRight } from 'lucide-vue-next'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import type { Breadcrumb, MediaAlbum, SeoProps } from '@site/Types'

/**
 * Медиа-галерея.
 *
 * Первый альбом идёт широким кадром — он же самый свежий, остальные ложатся
 * в две колонки с лентой превью. Плоская сетка одинаковых плиток оставляла
 * страницу пустой и не давала понять, что внутри альбома.
 */
const props = defineProps<{ seo: SeoProps; breadcrumbs: Breadcrumb[]; albums: MediaAlbum[] }>()

const page = usePage()
const locale = computed(() => page.props.locale as string)

const featured = computed<MediaAlbum | null>(() => props.albums[0] ?? null)
const rest = computed<MediaAlbum[]>(() => props.albums.slice(1))

const total = computed(() => props.albums.reduce((sum, album) => sum + (album.count ?? 0), 0))

function href(album: MediaAlbum): string {
    return route('media.show', { locale: locale.value, album: album.slug })
}
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero :title="$t('nav.media')" :subtitle="$t('media.lead')" :breadcrumbs="props.breadcrumbs" />

    <section class="section">
      <div class="container-site">
        <template v-if="props.albums.length">
          <!-- Счётчики вместо пустой полосы между шапкой и сеткой -->
          <div class="flex flex-wrap items-center gap-x-10 gap-y-3 border-b border-bone-200 pb-6">
            <p class="font-[family-name:var(--font-display)] text-2xl text-ink-900">
              {{ props.albums.length }}
              <span class="ml-1.5 text-xs uppercase tracking-[0.16em] text-bone-500">{{ $t('media.albums') }}</span>
            </p>

            <p class="font-[family-name:var(--font-display)] text-2xl text-ink-900">
              {{ total }}
              <span class="ml-1.5 text-xs uppercase tracking-[0.16em] text-bone-500">{{ $t('media.items') }}</span>
            </p>
          </div>

          <!-- Свежий альбом: широкий кадр с подписью поверх -->
          <Link
            v-if="featured"
            v-reveal
            :href="href(featured)"
            class="group media-frame media-veil relative mt-8 block aspect-4/3 overflow-hidden sm:aspect-[2/1] lg:aspect-[21/9]"
          >
            <img
              v-if="featured.cover"
              :src="featured.cover"
              :alt="featured.title"
              class="h-full w-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.03]"
              decoding="async"
            >
            <span v-else class="pattern-veil flex h-full w-full items-center justify-center bg-bone-50" aria-hidden="true">
              <Images :size="40" class="text-bone-300" />
            </span>

            <div class="absolute inset-x-0 bottom-0 z-10 flex flex-wrap items-end justify-between gap-5 p-6 lg:p-9">
              <div class="max-w-2xl">
                <p class="text-[0.6875rem] uppercase tracking-[0.18em] text-accent-400">{{ $t('media.featured') }}</p>

                <h2 class="heading-2 mt-3 text-2xl text-white lg:text-4xl">{{ featured.title }}</h2>

                <p v-if="featured.description" class="mt-3 max-w-xl text-sm leading-relaxed text-white/70">
                  {{ featured.description }}
                </p>
              </div>

              <p class="inline-flex items-center gap-2 text-[0.6875rem] uppercase tracking-[0.16em] text-white">
                {{ featured.count }} {{ $t('media.items') }}
                <ArrowUpRight :size="15" class="text-accent-400 transition-transform duration-300 group-hover:translate-x-0.5" />
              </p>
            </div>
          </Link>

          <!-- Остальные альбомы: обложка, лента превью и подпись под кадром -->
          <div v-if="rest.length" class="mt-8 grid gap-x-6 gap-y-10 md:grid-cols-2">
            <article
              v-for="(album, index) in rest"
              :key="album.id"
              v-reveal="(index % 2) * 80"
            >
              <Link :href="href(album)" class="group block">
                <div class="media-frame aspect-4/3 overflow-hidden">
                  <img
                    v-if="album.cover"
                    :src="album.cover"
                    :alt="album.title"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.03]"
                  >
                  <span v-else class="pattern-veil flex h-full w-full items-center justify-center bg-bone-50" aria-hidden="true">
                    <Images :size="34" class="text-bone-300" />
                  </span>
                </div>

                <div class="mt-5 flex items-start justify-between gap-5">
                  <div class="min-w-0">
                    <h2 class="heading-3 text-xl transition-colors group-hover:text-accent-600">{{ album.title }}</h2>

                    <p v-if="album.description" class="mt-2 line-clamp-2 text-sm leading-relaxed text-bone-600">
                      {{ album.description }}
                    </p>
                  </div>

                  <p v-if="album.count" class="shrink-0 text-[0.6875rem] uppercase tracking-[0.16em] text-bone-400">
                    {{ album.count }} {{ $t('media.items') }}
                  </p>
                </div>

                <ul v-if="album.previews?.length" class="mt-4 flex gap-2">
                  <li v-for="(preview, position) in album.previews.slice(0, 3)" :key="position" class="h-16 w-20 shrink-0 overflow-hidden">
                    <img :src="preview" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover">
                  </li>
                </ul>
              </Link>
            </article>
          </div>
        </template>

        <p v-else class="py-14 text-center text-sm text-bone-400">{{ $t('common.nothing') }}</p>
      </div>
    </section>
  </SiteLayout>
</template>
