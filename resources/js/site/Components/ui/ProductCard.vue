<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ArrowUpRight } from 'lucide-vue-next'
import type { Product } from '@site/Types'

const props = defineProps<{ product: Product }>()

const page = usePage()
const locale = computed(() => page.props.locale as string)
</script>

<template>
  <article class="group h-full">
    <Link
      :href="route('catalog.show', { locale, product: props.product.slug })"
      class="media-frame media-veil block aspect-3/4"
    >
      <img
        v-if="props.product.cover"
        :src="props.product.cover"
        :alt="props.product.name"
        loading="lazy"
        decoding="async"
        width="900"
        height="1200"
      >
      <span v-else class="pattern-veil block h-full w-full" aria-hidden="true" />

      <span
        class="absolute right-4 top-4 z-10 inline-flex h-10 w-10 translate-y-2 items-center justify-center bg-bone-50 text-ink-900 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100"
      >
        <ArrowUpRight :size="17" />
      </span>

      <span v-if="props.product.article" class="absolute bottom-4 left-4 z-10 text-[0.6875rem] uppercase tracking-[0.16em] text-white/75">
        {{ props.product.article }}
      </span>
    </Link>

    <div class="mt-5">
      <p v-if="props.product.category" class="text-[0.6875rem] uppercase tracking-[0.18em] text-accent-600">
        {{ props.product.category.name }}
      </p>

      <h3 class="heading-3 mt-2 text-xl">
        <Link
          :href="route('catalog.show', { locale, product: props.product.slug })"
          class="transition-colors group-hover:text-accent-700"
        >
          {{ props.product.name }}
        </Link>
      </h3>

      <p v-if="props.product.summary" class="mt-2 line-clamp-2 text-sm text-bone-600">
        {{ props.product.summary }}
      </p>
    </div>
  </article>
</template>
