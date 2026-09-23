<script setup lang="ts">
import { computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import PostCard from '@site/Components/ui/PostCard.vue'
import type { Breadcrumb, Paginated, Post, SeoProps } from '@site/Types'

const props = defineProps<{
    seo: SeoProps
    breadcrumbs: Breadcrumb[]
    posts: Paginated<Post>
    categories: { slug: string; name: string }[]
    filters: { type: string | null; category: string | null; search: string | null }
}>()

const page = usePage()
const locale = computed(() => page.props.locale as string)

const types = computed(() => [
    { value: null, label: 'news.all_types' },
    { value: 'news', label: 'news.type_news' },
    { value: 'article', label: 'news.type_article' },
    { value: 'review', label: 'news.type_review' },
])

function apply(patch: Record<string, string | null>): void {
    const current = { type: props.filters.type, category: props.filters.category, ...patch }
    const query: Record<string, string> = {}

    for (const [key, value] of Object.entries(current)) {
        if (value) query[key] = String(value)
    }

    router.get(route('news.index', { locale: locale.value }), query, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    })
}
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero :title="$t('nav.news')" :breadcrumbs="props.breadcrumbs" />

    <section class="section">
      <div class="container-site">
        <div class="flex flex-wrap items-center gap-x-8 gap-y-4 border-b border-bone-200 pb-6">
          <ul class="flex flex-wrap gap-x-5 gap-y-2">
            <li v-for="item in types" :key="item.label">
              <button
                type="button"
                :class="[
                  'text-sm transition-colors',
                  props.filters.type === item.value ? 'font-semibold text-primary-900' : 'text-bone-500 hover:text-accent-600',
                ]"
                @click="apply({ type: item.value })"
              >
                {{ $t(item.label) }}
              </button>
            </li>
          </ul>

          <ul v-if="props.categories.length" class="flex flex-wrap gap-2 md:ms-auto">
            <li>
              <button
                type="button"
                :class="[
                  'border px-3 py-1.5 text-xs transition-colors',
                  props.filters.category ? 'border-bone-200 text-bone-600 hover:border-accent-300' : 'border-accent-500 bg-accent-50 text-accent-700',
                ]"
                @click="apply({ category: null })"
              >
                {{ $t('news.all_categories') }}
              </button>
            </li>
            <li v-for="category in props.categories" :key="category.slug">
              <button
                type="button"
                :class="[
                  'border px-3 py-1.5 text-xs transition-colors',
                  props.filters.category === category.slug
                    ? 'border-accent-500 bg-accent-50 text-accent-700'
                    : 'border-bone-200 text-bone-600 hover:border-accent-300',
                ]"
                @click="apply({ category: category.slug })"
              >
                {{ category.name }}
              </button>
            </li>
          </ul>
        </div>

        <div v-if="props.posts.data.length" class="mt-7 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="(post, index) in props.posts.data"
            :key="post.id" v-reveal="(index % 3) * 70"
          >
            <PostCard :post="post" />
          </div>
        </div>

        <p v-else class="mt-7 text-center text-sm text-bone-400">{{ $t('news.empty') }}</p>

        <nav v-if="props.posts.meta.last_page > 1" class="mt-6 flex items-center justify-center gap-4">
          <Link v-if="props.posts.links.prev" :href="props.posts.links.prev" preserve-scroll class="btn btn-outline">
            {{ $t('common.prev') }}
          </Link>

          <span class="text-sm text-bone-500">
            {{ $t('common.page_of', { current: props.posts.meta.current_page, last: props.posts.meta.last_page }) }}
          </span>

          <Link v-if="props.posts.links.next" :href="props.posts.links.next" preserve-scroll class="btn btn-outline">
            {{ $t('common.next') }}
          </Link>
        </nav>
      </div>
    </section>
  </SiteLayout>
</template>
