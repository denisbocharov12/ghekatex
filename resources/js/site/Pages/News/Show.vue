<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import SectionHeading from '@site/Components/shared/SectionHeading.vue'
import PostCard from '@site/Components/ui/PostCard.vue'
import { formatDate } from '@shared/format'
import type { Breadcrumb, Post, SeoProps } from '@site/Types'

const props = defineProps<{ seo: SeoProps; breadcrumbs: Breadcrumb[]; post: Post; related: Post[] }>()

const page = usePage()
const locale = computed(() => page.props.locale as string)
const date = computed(() => formatDate(props.post.published_at, locale.value))
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero
      :eyebrow="props.post.category?.name || $t('nav.news')"
      :title="props.post.title"
      :subtitle="props.post.excerpt"
      :image="props.post.wide || props.post.cover"
      :breadcrumbs="props.breadcrumbs"
    />

    <article class="section">
      <div class="container-site">
        <div class="mx-auto max-w-3xl">
          <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-bone-200 pb-5 text-xs text-bone-400">
            <time v-if="date" :datetime="props.post.published_at || undefined">{{ date }}</time>
            <span v-if="props.post.reading">{{ props.post.reading }} {{ $t('common.minutes') }}</span>
            <span v-if="props.post.author">{{ props.post.author }}</span>
          </div>

          <div v-if="props.post.body" class="prose-site mt-6" v-html="props.post.body" />
        </div>

        <div v-if="props.post.gallery?.length" class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <figure
            v-for="(item, index) in props.post.gallery"
            :key="index"
            class="media-frame aspect-4/3 border border-bone-200"
          >
            <img :src="item.url" :alt="item.alt || props.post.title" loading="lazy" decoding="async" class="h-full w-full object-cover">
          </figure>
        </div>
      </div>
    </article>

    <section v-if="props.related.length" class="section bg-bone-50">
      <div class="container-site">
        <SectionHeading :title="$t('news.related')" />

        <div class="mt-7 grid gap-6 md:grid-cols-3">
          <PostCard v-for="item in props.related" :key="item.id" :post="item" />
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
