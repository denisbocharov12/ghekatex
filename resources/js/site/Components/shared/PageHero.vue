<script setup lang="ts">
import BreadcrumbTrail from './BreadcrumbTrail.vue'
import BrandMark from '@site/Components/ui/BrandMark.vue'
import type { Breadcrumb } from '@site/Types'

/** Верх внутренней страницы: кадр во всю ширину, крупный заголовок поверх. */
withDefaults(
    defineProps<{
        eyebrow?: string | null
        title: string
        subtitle?: string | null
        image?: string | null
        breadcrumbs?: Breadcrumb[]
    }>(),
    { eyebrow: null, subtitle: null, image: null, breadcrumbs: () => [] },
)
</script>

<template>
  <section class="grain on-ink relative overflow-hidden bg-ink-950 text-white">
    <img
      v-if="image"
      :src="image"
      :alt="title"
      class="ken-burns absolute inset-0 h-full w-full object-cover"
      fetchpriority="high"
      decoding="async"
    >
    <span v-else class="pattern-veil on-ink absolute inset-0" style="background: var(--gradient-ink)" aria-hidden="true" />

    <span class="cinema-veil" />

    <div class="container-site relative pb-12 pt-[calc(var(--header-height)+3.5rem)] lg:pb-24 lg:pt-[calc(var(--header-height)+6rem)]">
      <BreadcrumbTrail v-if="breadcrumbs?.length" :items="breadcrumbs" dark />

      <p v-if="eyebrow" class="eyebrow mt-6">{{ eyebrow }}</p>

      <h1 class="heading-1 mt-5 max-w-5xl text-balance text-white">{{ title }}</h1>

      <p v-if="subtitle" class="lead mt-6">{{ subtitle }}</p>
    </div>

    <BrandMark :size="96" class="pointer-events-none absolute bottom-8 right-8 hidden opacity-70 lg:block" />
  </section>
</template>
