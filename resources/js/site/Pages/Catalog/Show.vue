<script setup lang="ts">
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import BreadcrumbTrail from '@site/Components/shared/BreadcrumbTrail.vue'
import SectionHeading from '@site/Components/shared/SectionHeading.vue'
import ContactForm from '@site/Components/shared/ContactForm.vue'
import ProductCard from '@site/Components/ui/ProductCard.vue'
import LucideIcon from '@site/Components/ui/LucideIcon.vue'
import type { Breadcrumb, Product, SeoProps } from '@site/Types'

const props = defineProps<{
    seo: SeoProps
    breadcrumbs: Breadcrumb[]
    product: Product
    related: Product[]
}>()

const page = usePage()
const locale = computed(() => page.props.locale as string)

const gallery = computed(() => {
    const items = props.product.gallery ?? []

    return props.product.cover
        ? [{ url: props.product.cover, thumb: props.product.thumb ?? props.product.cover, alt: props.product.name }, ...items]
        : items
})

const activeIndex = ref(0)
const active = computed(() => gallery.value[activeIndex.value] ?? null)
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <div class="pt-20 lg:pt-32">
      <div class="container-site">
        <BreadcrumbTrail :items="props.breadcrumbs" />
      </div>
    </div>

    <section class="pb-12 pt-8 lg:pb-24">
      <div class="container-site grid gap-10 lg:grid-cols-12 lg:gap-9">
        <div class="lg:col-span-7">
          <div class="media-frame aspect-3/4 border border-bone-200 bg-bone-50">
            <img
              v-if="active"
              :src="active.url"
              :alt="active.alt || props.product.name"
              class="h-full w-full object-cover"
              width="1200"
              height="1600"
              fetchpriority="high"
              decoding="async"
            >
            <span v-else class="pattern-veil block h-full w-full" aria-hidden="true" />
          </div>

          <div v-if="gallery.length > 1" class="scroll-x mt-4 gap-3">
            <button
              v-for="(item, index) in gallery"
              :key="index"
              type="button"
              :class="[
                'w-24 shrink-0 border transition-colors',
                index === activeIndex ? 'border-accent-500' : 'border-bone-200 hover:border-accent-300',
              ]"
              :aria-label="`${props.product.name} ${index + 1}`"
              @click="activeIndex = index"
            >
              <img :src="item.thumb" :alt="item.alt || ''" loading="lazy" decoding="async" class="aspect-3/4 w-full object-cover">
            </button>
          </div>
        </div>

        <div class="lg:col-span-5">
          <p v-if="props.product.category" class="eyebrow">{{ props.product.category.name }}</p>

          <h1 class="heading-1 mt-4 text-4xl lg:text-5xl">{{ props.product.name }}</h1>

          <p v-if="props.product.article" class="mt-3 text-sm uppercase tracking-wider text-bone-400">
            {{ $t('catalog.article') }} {{ props.product.article }}
          </p>

          <p v-if="props.product.summary" class="mt-6 text-base leading-relaxed text-bone-600">
            {{ props.product.summary }}
          </p>

          <dl class="mt-6 divide-y divide-bone-200 border-y border-bone-300">
            <div v-if="props.product.composition" class="flex justify-between gap-6 py-3">
              <dt class="text-sm text-bone-500">{{ $t('catalog.composition') }}</dt>
              <dd class="text-sm font-medium text-bone-800">{{ props.product.composition }}</dd>
            </div>
            <div v-if="props.product.min_order" class="flex justify-between gap-6 py-3">
              <dt class="text-sm text-bone-500">{{ $t('catalog.min_order') }}</dt>
              <dd class="text-sm font-medium text-bone-800">{{ props.product.min_order }} {{ $t('catalog.pieces') }}</dd>
            </div>
            <div v-if="props.product.lead_time" class="flex justify-between gap-6 py-3">
              <dt class="text-sm text-bone-500">{{ $t('catalog.lead_time') }}</dt>
              <dd class="text-sm font-medium text-bone-800">{{ props.product.lead_time }} {{ $t('catalog.days') }}</dd>
            </div>
            <div v-for="attribute in props.product.attributes ?? []" :key="attribute.label" class="flex justify-between gap-6 py-3">
              <dt class="text-sm text-bone-500">{{ attribute.label }}</dt>
              <dd class="text-sm font-medium text-bone-800">{{ attribute.value }}</dd>
            </div>
          </dl>

          <div v-if="props.product.fabrics?.length" class="mt-6">
            <h2 class="eyebrow">{{ $t('catalog.fabrics') }}</h2>
            <ul class="mt-4 flex flex-wrap gap-3">
              <li
                v-for="fabric in props.product.fabrics"
                :key="fabric.slug"
                class="flex items-center gap-2.5 border border-bone-200 px-3 py-2"
              >
                <span
                  v-if="fabric.swatch || fabric.color"
                  class="h-6 w-6 shrink-0 border border-black/10 bg-cover bg-center"
                  :style="fabric.swatch ? { backgroundImage: `url(${fabric.swatch})` } : { backgroundColor: fabric.color || undefined }"
                />
                <span>
                  <span class="block text-sm text-bone-800">{{ fabric.name }}</span>
                  <span v-if="fabric.weight" class="block text-xs text-bone-400">{{ fabric.weight }} {{ $t('catalog.gsm') }}</span>
                </span>
              </li>
            </ul>
          </div>

          <div v-if="props.product.treatments?.length" class="mt-6">
            <h2 class="eyebrow">{{ $t('catalog.treatments') }}</h2>
            <ul class="mt-4 flex flex-wrap gap-2">
              <li
                v-for="treatment in props.product.treatments"
                :key="treatment.slug"
                class="inline-flex items-center gap-2 border border-bone-200 px-3 py-1.5 text-xs text-bone-600"
              >
                <LucideIcon :name="treatment.icon" :size="14" class="text-accent-600" />
                {{ treatment.name }}
              </li>
            </ul>
          </div>

          <a href="#request" class="btn btn-accent mt-6">{{ $t('catalog.request') }}</a>
        </div>
      </div>
    </section>

    <section v-if="props.product.description" class="section bg-bone-50">
      <div class="container-site">
        <div class="prose-site max-w-3xl" v-html="props.product.description" />
      </div>
    </section>

    <section id="request" class="on-ink section section-malachite">
      <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-10">
        <div class="lg:col-span-5">
          <SectionHeading
            :eyebrow="$t('home.contact_eyebrow')"
            :title="$t('catalog.request')"
            :text="$t('home.contact_text')"
          />
        </div>
        <div class="lg:col-span-7">
          <ContactForm source="product" related-type="product" :related-id="props.product.id" dark compact />
        </div>
      </div>
    </section>

    <section v-if="props.related.length" class="section">
      <div class="container-site">
        <SectionHeading :title="$t('catalog.related')" />

        <div class="mt-7 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <ProductCard v-for="item in props.related" :key="item.id" :product="item" />
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
