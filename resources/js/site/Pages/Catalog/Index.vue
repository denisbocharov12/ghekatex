<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { Search, X } from 'lucide-vue-next'
import { useDebounceFn } from '@vueuse/core'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import ProductCard from '@site/Components/ui/ProductCard.vue'
import type { Breadcrumb, Paginated, Product, ProductCategory, SeoProps } from '@site/Types'

const props = defineProps<{
    seo: SeoProps
    breadcrumbs: Breadcrumb[]
    category: ProductCategory | null
    categories: ProductCategory[]
    fabrics: { slug: string; name: string; color: string | null }[]
    treatments: { slug: string; name: string; icon: string }[]
    products: Paginated<Product>
    filters: { category_id: number | null; fabric: string | null; treatment: string | null; search: string | null }
}>()

const page = usePage()
const locale = computed(() => page.props.locale as string)

const search = ref(props.filters.search ?? '')

/** Базовый маршрут списка: внутри категории фильтры остаются на её странице. */
const listRoute = computed(() =>
    props.category
        ? route('catalog.category', { locale: locale.value, category: props.category.slug })
        : route('catalog.index', { locale: locale.value }),
)

function apply(patch: Record<string, string | null>): void {
    const query: Record<string, string> = {}
    const current = { fabric: props.filters.fabric, treatment: props.filters.treatment, q: search.value, ...patch }

    for (const [key, value] of Object.entries(current)) {
        if (value) query[key] = String(value)
    }

    router.get(listRoute.value, query, { preserveScroll: true, preserveState: true, replace: true })
}

const applySearch = useDebounceFn(() => apply({}), 400)

watch(search, () => applySearch())

const hasFilters = computed(() => Boolean(props.filters.fabric || props.filters.treatment || props.filters.search))

/** Вид кнопки-фильтра: отличается только состоянием. */
function chipClass(active: boolean): string[] {
    return [
        'inline-flex items-center gap-2 border px-3.5 py-2 text-xs transition-colors',
        active
            ? 'border-accent-500 bg-accent-50 text-accent-700'
            : 'border-bone-300 text-bone-600 hover:border-accent-400 hover:text-accent-700',
    ]
}
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero
      :eyebrow="props.category ? $t('nav.catalog') : null"
      :title="props.category?.name || $t('nav.catalog')"
      :subtitle="props.category?.description || $t('home.categories_text')"
      :image="props.category?.cover"
      :breadcrumbs="props.breadcrumbs"
    />

    <section class="section">
      <div class="container-site">
        <!-- Предупреждение о том, что витрина не магазин, стоит до фильтров -->
        <div class="flex flex-wrap items-start justify-between gap-6 border-b border-bone-300 pb-6">
          <p class="max-w-2xl text-sm leading-relaxed text-bone-600">{{ $t('catalog.note') }}</p>
          <p class="text-xs uppercase tracking-[0.16em] text-bone-400">
            {{ $t('common.found', { count: props.products.meta.total }) }}
          </p>
        </div>

        <div class="mt-7 grid gap-10 lg:grid-cols-12 lg:gap-12">
          <aside class="lg:col-span-3">
            <div class="flex flex-col gap-9 lg:sticky lg:top-28">
              <div>
                <label class="sr-only" for="catalog-search">{{ $t('common.search') }}</label>
                <div class="relative">
                  <Search :size="16" class="pointer-events-none absolute left-0 top-1/2 -translate-y-1/2 text-bone-400" />
                  <input
                    id="catalog-search"
                    v-model="search"
                    type="search"
                    :placeholder="$t('common.search')"
                    class="h-11 w-full border-0 border-b border-bone-300 bg-transparent pl-7 text-sm transition-colors focus:border-accent-500 focus:outline-none"
                  >
                </div>
              </div>

              <div>
                <h2 class="eyebrow">{{ $t('catalog.categories') }}</h2>

                <ul class="mt-5">
                  <li>
                    <Link
                      :href="route('catalog.index', { locale })"
                      :class="[
                        'block border-b border-bone-200 py-2.5 text-sm transition-colors',
                        props.category ? 'text-bone-500 hover:text-accent-700' : 'font-semibold text-ink-900',
                      ]"
                    >
                      {{ $t('catalog.all_categories') }}
                    </Link>
                  </li>

                  <li v-for="item in props.categories" :key="item.id">
                    <Link
                      :href="route('catalog.category', { locale, category: item.slug })"
                      :class="[
                        'flex items-center justify-between gap-3 border-b border-bone-200 py-2.5 text-sm transition-colors',
                        props.category?.slug === item.slug ? 'font-semibold text-ink-900' : 'text-bone-500 hover:text-accent-700',
                      ]"
                    >
                      <span>{{ item.name }}</span>
                      <span v-if="item.products_count" class="text-xs text-bone-400">{{ item.products_count }}</span>
                    </Link>
                  </li>
                </ul>
              </div>

              <div v-if="props.fabrics.length">
                <h2 class="eyebrow">{{ $t('catalog.fabrics') }}</h2>

                <ul class="mt-5 flex flex-wrap gap-2">
                  <li v-for="fabric in props.fabrics" :key="fabric.slug">
                    <button
                      type="button"
                      :class="chipClass(props.filters.fabric === fabric.slug)"
                      @click="apply({ fabric: props.filters.fabric === fabric.slug ? null : fabric.slug })"
                    >
                      <span
                        v-if="fabric.color"
                        class="h-3 w-3 border border-black/10"
                        :style="{ backgroundColor: fabric.color }"
                      />
                      {{ fabric.name }}
                    </button>
                  </li>
                </ul>
              </div>

              <div v-if="props.treatments.length">
                <h2 class="eyebrow">{{ $t('catalog.treatments') }}</h2>

                <ul class="mt-5 flex flex-wrap gap-2">
                  <li v-for="treatment in props.treatments" :key="treatment.slug">
                    <button
                      type="button"
                      :class="chipClass(props.filters.treatment === treatment.slug)"
                      @click="apply({ treatment: props.filters.treatment === treatment.slug ? null : treatment.slug })"
                    >
                      {{ treatment.name }}
                    </button>
                  </li>
                </ul>
              </div>

              <button
                v-if="hasFilters"
                type="button"
                class="link-arrow self-start"
                @click="search = ''; apply({ fabric: null, treatment: null, q: null })"
              >
                <X :size="13" />
                {{ $t('common.reset') }}
              </button>
            </div>
          </aside>

          <div class="lg:col-span-9">
            <div v-if="props.products.data.length" class="grid gap-x-5 gap-y-9 sm:grid-cols-2 xl:grid-cols-3">
              <div
                v-for="(product, position) in props.products.data"
                :key="product.id"
                v-reveal="(position % 3) * 70"
              >
                <ProductCard :product="product" />
              </div>
            </div>

            <p v-else class="py-14 text-center text-sm text-bone-400">{{ $t('catalog.empty') }}</p>

            <nav v-if="props.products.meta.last_page > 1" class="mt-7 flex items-center justify-center gap-6">
              <Link
                v-if="props.products.links.prev"
                :href="props.products.links.prev"
                preserve-scroll
                class="btn btn-outline"
              >
                {{ $t('common.prev') }}
              </Link>

              <span class="text-xs uppercase tracking-[0.16em] text-bone-500">
                {{ $t('common.page_of', { current: props.products.meta.current_page, last: props.products.meta.last_page }) }}
              </span>

              <Link
                v-if="props.products.links.next"
                :href="props.products.links.next"
                preserve-scroll
                class="btn btn-outline"
              >
                {{ $t('common.next') }}
              </Link>
            </nav>
          </div>
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
