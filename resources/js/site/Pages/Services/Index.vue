<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ArrowRight } from 'lucide-vue-next'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import LucideIcon from '@site/Components/ui/LucideIcon.vue'
import FaqSection from '@site/Components/sections/FaqSection.vue'
import { useContactDialog } from '@site/Composables/contactDialog'
import type { Breadcrumb, FaqEntry, SeoProps, Service } from '@site/Types'

/**
 * Список услуг.
 *
 * Услуг немного, но каждая занимает экран, поэтому над ними закреплена
 * горизонтальная лента якорей: она прилипает под шапкой и подсвечивает
 * раздел, до которого дошла прокрутка.
 */
const props = defineProps<{ seo: SeoProps; breadcrumbs: Breadcrumb[]; services: Service[]; faqs: FaqEntry[] }>()

const page = usePage()
const locale = computed(() => page.props.locale as string)

const { openDialog } = useContactDialog()

const active = ref<string | null>(null)
const tabs = ref<HTMLElement | null>(null)

let observer: IntersectionObserver | null = null

function anchor(service: Service): string {
    return `service-${service.slug}`
}

onMounted(() => {
    active.value = props.services[0]?.slug ?? null

    observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)[0]

            if (!visible) return

            const slug = (visible.target as HTMLElement).dataset.slug ?? null

            if (!slug || slug === active.value) return

            active.value = slug

            // Активная вкладка не должна уезжать за край на узком экране
            const tab = tabs.value?.querySelector<HTMLElement>(`[data-tab="${slug}"]`)
            tab?.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' })
        },
        { rootMargin: '-30% 0px -55% 0px', threshold: 0 },
    )

    for (const element of document.querySelectorAll<HTMLElement>('[data-service-section]')) {
        observer.observe(element)
    }
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero
      :title="$t('nav.services')"
      :subtitle="$t('home.contact_text')"
      :breadcrumbs="props.breadcrumbs"
    />

    <!-- Лента якорей прилипает под шапкой -->
    <nav
      v-if="props.services.length > 1"
      ref="tabs"
      class="sticky top-[var(--header-height)] z-40 border-b border-bone-200 bg-white/92 backdrop-blur-md"
      :aria-label="$t('nav.services')"
    >
      <ul class="container-site scroll-x flex gap-1 py-2">
        <li v-for="(service, index) in props.services" :key="service.id" class="shrink-0">
          <a
            :href="`#${anchor(service)}`"
            :data-tab="service.slug"
            :class="[
              'inline-flex items-center gap-2.5 whitespace-nowrap border px-4 py-2 text-xs transition-colors',
              active === service.slug
                ? 'border-primary-700 bg-primary-50 text-primary-800'
                : 'border-transparent text-bone-600 hover:text-primary-700',
            ]"
          >
            <span class="font-[family-name:var(--font-display)] text-[0.6875rem] opacity-60">
              {{ String(index + 1).padStart(2, '0') }}
            </span>
            {{ service.name }}
          </a>
        </li>
      </ul>
    </nav>

    <section class="section">
      <div class="container-site flex flex-col gap-14 lg:gap-16">
        <article
          v-for="(service, index) in props.services"
          :id="anchor(service)"
          :key="service.id"
          :data-slug="service.slug"
          data-service-section
          class="grid scroll-mt-[calc(var(--header-height)+4.5rem)] gap-8 lg:grid-cols-12 lg:gap-10"
        >
          <div :class="['lg:col-span-6', index % 2 === 1 ? 'lg:order-2' : '']">
            <div v-reveal class="reveal-wipe media-frame aspect-4/3">
              <img
                v-if="service.cover"
                :src="service.cover"
                :alt="service.name"
                loading="lazy"
                decoding="async"
                width="960"
                height="720"
                class="h-full w-full object-cover"
              >
              <span v-else class="pattern-veil flex h-full w-full items-center justify-center bg-bone-50" aria-hidden="true">
                <LucideIcon :name="service.icon" :size="44" class="text-bone-300" />
              </span>
            </div>
          </div>

          <div v-reveal="120" class="flex flex-col justify-center lg:col-span-6">
            <div class="flex items-center gap-4">
              <span class="font-[family-name:var(--font-display)] text-sm text-accent-600">
                {{ String(index + 1).padStart(2, '0') }}
              </span>
              <span class="h-px w-8 bg-accent-500" aria-hidden="true" />
              <LucideIcon :name="service.icon" :size="20" class="text-accent-600" />
            </div>

            <h2 class="heading-2 mt-5 text-2xl lg:text-4xl">{{ service.name }}</h2>

            <p v-if="service.summary" class="lead mt-4">{{ service.summary }}</p>

            <!-- Параметры услуги: строки-разделители вместо трёх колонок -->
            <dl v-if="service.highlights?.length" class="mt-7">
              <div
                v-for="highlight in service.highlights"
                :key="highlight.label"
                class="flex items-center justify-between gap-6 border-t border-bone-200 py-3 last:border-b"
              >
                <dt class="text-xs uppercase tracking-[0.16em] text-bone-500">{{ highlight.label }}</dt>
                <dd class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                  <LucideIcon :name="highlight.icon" :size="15" class="text-accent-600" />
                  {{ highlight.value }}
                </dd>
              </div>
            </dl>

            <div class="mt-7 flex flex-wrap gap-3">
              <button type="button" class="btn btn-accent" @click="openDialog([service.slug])">
                {{ $t('nav.contact_us') }}
              </button>

              <Link :href="route('services.show', { locale, service: service.slug })" class="btn btn-outline">
                {{ $t('common.more') }}
                <ArrowRight :size="16" />
              </Link>
            </div>
          </div>
        </article>
      </div>
    </section>

    <FaqSection :items="props.faqs" />
  </SiteLayout>
</template>
