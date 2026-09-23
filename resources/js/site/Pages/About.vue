<script setup lang="ts">
import { computed } from 'vue'
import { FileDown, ArrowUpRight } from 'lucide-vue-next'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import SectionHeading from '@site/Components/shared/SectionHeading.vue'
import VideoBanner from '@site/Components/sections/VideoBanner.vue'
import LucideIcon from '@site/Components/ui/LucideIcon.vue'
import CountUp from '@site/Components/ui/CountUp.vue'
import type { Breadcrumb, SeoProps } from '@site/Types'

interface Milestone {
    id: number
    year: string
    title: string
    description: string | null
}

interface Facility {
    id: number
    slug: string
    icon: string
    name: string
    summary: string | null
    description: string | null
    specs: { label: string; value: string }[]
    capacity: number | null
    employees: number | null
    cover: string | null
}

interface Certificate {
    id: number
    name: string
    issuer: string | null
    description: string | null
    number: string | null
    valid_until: string | null
    image: string | null
    document: string | null
}

interface Partner {
    id: number
    name: string
    description: string | null
    website: string | null
    country: string | null
    logo: string | null
}

const props = defineProps<{
    seo: SeoProps
    breadcrumbs: Breadcrumb[]
    page: { title: string; subtitle: string | null; body: string | null; cover: string | null } | null
    milestones: Milestone[]
    facilities: Facility[]
    certificates: Certificate[]
    partners: Partner[]
}>()

/** Сводка по мощностям: цифры рядом с текстом о компании, а не только внутри блоков. */
const totals = computed(() => ({
    capacity: props.facilities.reduce((sum, item) => sum + (item.capacity ?? 0), 0),
    employees: props.facilities.reduce((sum, item) => sum + (item.employees ?? 0), 0),
}))
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero
      :title="props.page?.title || $t('nav.about')"
      :subtitle="props.page?.subtitle"
      :image="props.page?.cover"
      :breadcrumbs="props.breadcrumbs"
    />

    <!-- Текст о компании: разворот с закреплённой сводкой вместо узкой колонки -->
    <section v-if="props.page?.body" class="section">
      <div class="container-site grid gap-10 lg:grid-cols-12 lg:gap-10">
        <div v-reveal class="lg:col-span-4">
          <div class="lg:sticky lg:top-[calc(var(--header-height)+2rem)]">
            <p class="eyebrow">{{ $t('nav.about') }}</p>

            <p v-if="props.page.subtitle" class="heading-3 mt-5 text-balance text-ink-900">
              {{ props.page.subtitle }}
            </p>

            <dl v-if="totals.capacity || totals.employees" class="mt-8 grid grid-cols-2 gap-6 lg:grid-cols-1 lg:gap-7">
              <div v-if="totals.capacity" class="border-t border-bone-200 pt-4">
                <dt class="text-[0.6875rem] uppercase tracking-[0.18em] text-bone-500">{{ $t('about.capacity') }}</dt>
                <dd class="mt-2 font-[family-name:var(--font-display)] text-3xl text-ink-900">
                  <CountUp :value="String(totals.capacity)" />
                </dd>
              </div>

              <div v-if="totals.employees" class="border-t border-bone-200 pt-4">
                <dt class="text-[0.6875rem] uppercase tracking-[0.18em] text-bone-500">{{ $t('about.employees') }}</dt>
                <dd class="mt-2 font-[family-name:var(--font-display)] text-3xl text-ink-900">
                  <CountUp :value="String(totals.employees)" />
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <div v-reveal="100" class="prose-site lg:col-span-8" v-html="props.page.body" />
      </div>
    </section>

    <!-- История: шапка едет вдоль ленты лет -->
    <section v-if="props.milestones.length" v-sheen class="section section-sheen">
      <div class="container-site grid gap-10 lg:grid-cols-12 lg:gap-10">
        <div v-reveal class="lg:col-span-4 lg:sticky lg:top-[calc(var(--header-height)+2rem)] lg:self-start">
          <SectionHeading
            :eyebrow="$t('about.history_eyebrow')"
            :title="$t('about.history_title')"
          />
        </div>

        <ol class="lg:col-span-8">
          <li
            v-for="(item, position) in props.milestones"
            :key="item.id"
            v-reveal="Math.min(position * 60, 240)"
            class="group grid gap-x-8 gap-y-2 border-t border-bone-300 py-6 last:border-b sm:grid-cols-[7rem_1fr]"
          >
            <p class="flex items-baseline gap-3 font-[family-name:var(--font-display)] text-3xl leading-none text-ink-900 lg:text-4xl">
              {{ item.year }}
              <span
                class="hidden h-px w-0 bg-accent-500 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:w-6 sm:block"
                aria-hidden="true"
              />
            </p>

            <div>
              <h3 class="heading-3 text-lg transition-colors group-hover:text-accent-700">{{ item.title }}</h3>
              <p v-if="item.description" class="mt-2 max-w-2xl text-sm leading-relaxed text-bone-600">
                {{ item.description }}
              </p>
            </div>
          </li>
        </ol>
      </div>
    </section>

    <!-- Мощности: чередующиеся развороты кадр / текст -->
    <section v-if="props.facilities.length" class="section">
      <div class="container-site">
        <SectionHeading
          :eyebrow="$t('about.facilities_eyebrow')"
          :title="$t('about.facilities_title')"
        />

        <div class="mt-9 flex flex-col gap-12 lg:gap-14">
          <article
            v-for="(facility, position) in props.facilities"
            :key="facility.id"
            class="grid gap-8 lg:grid-cols-12 lg:gap-10"
          >
            <div :class="['lg:col-span-7', position % 2 === 1 ? 'lg:order-2' : '']">
              <div v-reveal class="reveal-wipe media-frame aspect-4/3">
                <img
                  v-if="facility.cover"
                  :src="facility.cover"
                  :alt="facility.name"
                  loading="lazy"
                  decoding="async"
                  width="960"
                  height="720"
                >
                <span v-else class="pattern-veil flex h-full w-full items-center justify-center bg-bone-50" aria-hidden="true">
                  <LucideIcon :name="facility.icon" :size="44" class="text-bone-300" />
                </span>
              </div>
            </div>

            <div v-reveal="120" class="flex flex-col justify-center lg:col-span-5">
              <div class="flex items-center gap-4">
                <LucideIcon :name="facility.icon" :size="20" class="text-accent-600" />
                <span class="h-px w-8 bg-accent-500" aria-hidden="true" />
              </div>

              <h3 class="heading-2 mt-4 text-2xl lg:text-3xl">{{ facility.name }}</h3>

              <p v-if="facility.summary" class="lead mt-4">{{ facility.summary }}</p>

              <dl v-if="facility.capacity || facility.employees" class="mt-6 grid grid-cols-2 gap-8">
                <div v-if="facility.capacity">
                  <dt class="text-[0.6875rem] uppercase tracking-[0.18em] text-bone-500">{{ $t('about.capacity') }}</dt>
                  <dd class="mt-2 font-[family-name:var(--font-display)] text-3xl text-ink-900">
                    <CountUp :value="String(facility.capacity)" />
                  </dd>
                </div>

                <div v-if="facility.employees">
                  <dt class="text-[0.6875rem] uppercase tracking-[0.18em] text-bone-500">{{ $t('about.employees') }}</dt>
                  <dd class="mt-2 font-[family-name:var(--font-display)] text-3xl text-ink-900">
                    <CountUp :value="String(facility.employees)" />
                  </dd>
                </div>
              </dl>

              <dl v-if="facility.specs.length" class="mt-6">
                <div
                  v-for="spec in facility.specs"
                  :key="spec.label"
                  class="flex justify-between gap-6 border-t border-bone-200 py-3 last:border-b"
                >
                  <dt class="text-sm text-bone-500">{{ spec.label }}</dt>
                  <dd class="text-right text-sm font-medium text-ink-800">{{ spec.value }}</dd>
                </div>
              </dl>
            </div>
          </article>
        </div>
      </div>
    </section>

    <VideoBanner src="/media/video/production_video.mp4" poster="/media/preview/menu-1.jpg">
      <div v-reveal class="max-w-2xl">
        <p class="eyebrow">{{ $t('about.facilities_eyebrow') }}</p>
        <p class="heading-1 mt-6 text-white">{{ props.page?.subtitle || $t('about.facilities_title') }}</p>
      </div>
    </VideoBanner>

    <!-- Сертификаты: карточки с рамкой, ссылка на документ прижата к низу -->
    <section v-if="props.certificates.length" class="section">
      <div class="container-site">
        <SectionHeading
          :eyebrow="$t('about.certificates_eyebrow')"
          :title="$t('about.certificates_title')"
        />

        <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <article
            v-for="(certificate, position) in props.certificates"
            :key="certificate.id"
            v-reveal="(position % 4) * 80"
            class="group flex h-full flex-col border border-bone-200 transition-colors hover:border-accent-400"
          >
            <div class="media-frame aspect-3/4 overflow-hidden">
              <img
                v-if="certificate.image"
                :src="certificate.image"
                :alt="certificate.name"
                loading="lazy"
                decoding="async"
                class="h-full w-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.03]"
              >
              <span v-else class="pattern-veil block h-full w-full bg-bone-50" aria-hidden="true" />
            </div>

            <div class="flex flex-1 flex-col p-5">
              <p v-if="certificate.issuer" class="text-[0.6875rem] uppercase tracking-[0.16em] text-accent-600">
                {{ certificate.issuer }}
              </p>

              <h3 class="heading-3 mt-2 text-base">{{ certificate.name }}</h3>

              <p v-if="certificate.description" class="mt-2.5 text-sm leading-relaxed text-bone-600">
                {{ certificate.description }}
              </p>

              <dl v-if="certificate.number || certificate.valid_until" class="mt-4 space-y-1 text-xs text-bone-400">
                <div v-if="certificate.number" class="flex gap-2">
                  <dt>{{ $t('about.certificate_number') }}:</dt>
                  <dd>{{ certificate.number }}</dd>
                </div>
                <div v-if="certificate.valid_until" class="flex gap-2">
                  <dt>{{ $t('about.valid_until') }}:</dt>
                  <dd>{{ certificate.valid_until }}</dd>
                </div>
              </dl>

              <a
                v-if="certificate.document"
                :href="certificate.document"
                target="_blank"
                rel="noopener noreferrer"
                class="link-arrow mt-auto pt-5"
              >
                <FileDown :size="14" />
                {{ $t('common.download') }}
              </a>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Партнёры: плитки с логотипом, страной и переходом на сайт -->
    <section v-if="props.partners.length" class="section section-steel">
      <div class="container-site">
        <SectionHeading :title="$t('about.partners_title')" />

        <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <component
            :is="partner.website ? 'a' : 'div'"
            v-for="(partner, position) in props.partners"
            :key="partner.id"
            v-reveal="(position % 3) * 70"
            :href="partner.website || undefined"
            :target="partner.website ? '_blank' : undefined"
            :rel="partner.website ? 'noopener noreferrer' : undefined"
            class="group flex h-full flex-col gap-5 border border-white/70 bg-white/85 p-6 transition-colors hover:border-primary-700"
          >
            <div class="flex items-start justify-between gap-4">
              <img
                v-if="partner.logo"
                :src="partner.logo"
                :alt="partner.name"
                loading="lazy"
                decoding="async"
                class="h-7 w-auto opacity-80 transition-opacity group-hover:opacity-100"
              >

              <ArrowUpRight
                v-if="partner.website"
                :size="16"
                class="ml-auto shrink-0 text-accent-600 opacity-0 transition-all duration-300 group-hover:translate-x-0.5 group-hover:opacity-100"
              />
            </div>

            <div class="mt-auto">
              <h3 class="text-base font-semibold text-ink-900">{{ partner.name }}</h3>

              <p v-if="partner.country" class="mt-1 text-[0.6875rem] uppercase tracking-[0.16em] text-bone-400">
                {{ partner.country }}
              </p>

              <p v-if="partner.description" class="mt-2.5 text-sm leading-relaxed text-bone-600">
                {{ partner.description }}
              </p>
            </div>
          </component>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
