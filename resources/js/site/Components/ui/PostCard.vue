<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ArrowUpRight } from 'lucide-vue-next'
import { formatDate } from '@shared/format'
import type { Post } from '@site/Types'

const props = defineProps<{ post: Post }>()

const page = usePage()
const locale = computed(() => page.props.locale as string)
const date = computed(() => formatDate(props.post.published_at, locale.value))
</script>

<template>
  <article class="group h-full">
    <Link :href="route('news.show', { locale, post: props.post.slug })" class="media-frame block aspect-16/10">
      <img
        v-if="props.post.cover"
        :src="props.post.cover"
        :alt="props.post.title"
        loading="lazy"
        decoding="async"
        width="960"
        height="600"
      >
      <span v-else class="pattern-veil block h-full w-full" aria-hidden="true" />
    </Link>

    <div class="mt-5">
      <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[0.6875rem] uppercase tracking-[0.16em] text-bone-500">
        <time v-if="date" :datetime="props.post.published_at || undefined">{{ date }}</time>
        <span v-if="props.post.category" class="text-accent-600">{{ props.post.category.name }}</span>
      </div>

      <h3 class="heading-3 mt-3 text-xl">
        <Link :href="route('news.show', { locale, post: props.post.slug })" class="transition-colors group-hover:text-accent-700">
          {{ props.post.title }}
        </Link>
      </h3>

      <p v-if="props.post.excerpt" class="mt-2.5 line-clamp-3 text-sm leading-relaxed text-bone-600">
        {{ props.post.excerpt }}
      </p>

      <span class="link-arrow mt-5">
        {{ $t('common.more') }}
        <ArrowUpRight :size="14" />
      </span>
    </div>
  </article>
</template>
