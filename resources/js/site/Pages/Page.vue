<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { FileText, ChevronRight } from 'lucide-vue-next'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import { formatDate } from '@shared/format'
import type { Breadcrumb, MenuItem, SeoProps, SiteProps } from '@site/Types'

/**
 * Контентная страница: политика, условия и любые другие тексты из админки.
 *
 * Правовые документы длинные, поэтому рядом с текстом строится оглавление
 * по заголовкам второго уровня и список остальных документов. Оглавление
 * собирается из готовой разметки — в админке редактор вводит обычный текст
 * и про якоря ничего не знает.
 */
const props = defineProps<{
    seo: SeoProps
    breadcrumbs: Breadcrumb[]
    page: {
        slug: string
        template: string
        title: string
        subtitle: string | null
        body: string | null
        blocks: Record<string, unknown>[]
        cover: string | null
        updated_at: string | null
    }
}>()

const inertiaPage = usePage()
const locale = computed(() => inertiaPage.props.locale as string)
const site = computed(() => inertiaPage.props.site as SiteProps | null)
const updated = computed(() => formatDate(props.page.updated_at, locale.value))

const isLegal = computed(() => props.page.template === 'legal')
const documents = computed<MenuItem[]>(() => site.value?.menus?.legal ?? [])

const body = ref<HTMLElement | null>(null)
const headings = ref<{ id: string; label: string }[]>([])
const active = ref<string | null>(null)

let observer: IntersectionObserver | null = null

function slugify(value: string, index: number): string {
    const base = value
        .toLowerCase()
        .replace(/[^\p{L}\p{N}]+/gu, '-')
        .replace(/^-+|-+$/g, '')

    return base ? `${base}-${index + 1}` : `section-${index + 1}`
}

function buildToc(): void {
    observer?.disconnect()
    headings.value = []
    active.value = null

    if (!isLegal.value || !body.value) return

    const found = [...body.value.querySelectorAll<HTMLHeadingElement>('h2')]

    headings.value = found.map((element, index) => {
        element.id ||= slugify(element.textContent ?? '', index)
        element.classList.add('scroll-mt-[calc(var(--header-height)+2rem)]')

        return { id: element.id, label: (element.textContent ?? '').trim() }
    })

    if (!headings.value.length) return

    active.value = headings.value[0].id

    observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)[0]

            if (visible) active.value = (visible.target as HTMLElement).id
        },
        { rootMargin: '-20% 0px -70% 0px', threshold: 0 },
    )

    for (const element of found) observer.observe(element)
}

onMounted(buildToc)
onBeforeUnmount(() => observer?.disconnect())

watch(() => props.page.slug, () => nextTick(buildToc))
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero
      :title="props.page.title"
      :subtitle="props.page.subtitle"
      :image="props.page.cover"
      :breadcrumbs="props.breadcrumbs"
    />

    <section class="section">
      <div class="container-site">
        <!-- Правовые документы: текст слева, оглавление и список документов справа -->
        <div v-if="isLegal" class="grid gap-10 lg:grid-cols-12 lg:gap-12">
          <div class="lg:col-span-8">
            <p v-if="updated" class="mb-7 text-xs uppercase tracking-[0.16em] text-bone-400">
              {{ $t('common.published') }}: {{ updated }}
            </p>

            <div ref="body" class="prose-site prose-legal" v-html="props.page.body" />
          </div>

          <aside class="lg:col-span-4">
            <div class="flex flex-col gap-4 lg:sticky lg:top-[calc(var(--header-height)+2rem)]">
              <nav v-if="headings.length" class="bg-bone-50 p-6" :aria-label="$t('page.contents')">
                <p class="text-[0.6875rem] font-semibold uppercase tracking-[0.18em] text-bone-500">
                  {{ $t('page.contents') }}
                </p>

                <ul class="mt-4 space-y-1">
                  <li v-for="item in headings" :key="item.id">
                    <a
                      :href="`#${item.id}`"
                      :class="[
                        'block border-l-2 py-1.5 pl-3 text-sm transition-colors',
                        active === item.id
                          ? 'border-primary-700 font-medium text-primary-800'
                          : 'border-transparent text-bone-600 hover:border-bone-300 hover:text-primary-700',
                      ]"
                    >
                      {{ item.label }}
                    </a>
                  </li>
                </ul>
              </nav>

              <nav v-if="documents.length" class="bg-bone-50 p-6" :aria-label="$t('page.documents')">
                <p class="text-[0.6875rem] font-semibold uppercase tracking-[0.18em] text-bone-500">
                  {{ $t('page.documents') }}
                </p>

                <ul class="mt-4">
                  <li v-for="item in documents" :key="item.id">
                    <Link
                      :href="item.href"
                      :class="[
                        'group flex items-center gap-3 border-b border-bone-200 py-2.5 text-sm transition-colors last:border-b-0',
                        item.href.endsWith(`/${props.page.slug}`)
                          ? 'font-medium text-primary-800'
                          : 'text-bone-600 hover:text-primary-700',
                      ]"
                    >
                      <FileText :size="15" class="shrink-0 text-primary-500" />
                      <span class="flex-1">{{ item.label }}</span>
                      <ChevronRight :size="14" class="shrink-0 text-bone-400 transition-transform group-hover:translate-x-0.5" />
                    </Link>
                  </li>
                </ul>
              </nav>
            </div>
          </aside>
        </div>

        <div v-else class="prose-site max-w-4xl" v-html="props.page.body" />
      </div>
    </section>
  </SiteLayout>
</template>

<style scoped>
/*
| Заголовки документа получают синюю засечку слева — в правовых текстах
| она заменяет крупный кегль и держит длинную простыню читаемой.
*/
.prose-legal :deep(h2) {
    position: relative;
    padding-left: 1rem;
    margin-top: 2.5rem;
    font-size: 1.375rem;
}

.prose-legal :deep(h2)::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.15em;
    bottom: 0.15em;
    width: 3px;
    background: var(--color-primary-700);
}

.prose-legal :deep(h3) {
    margin-top: 1.75rem;
    font-size: 1.0625rem;
}

.prose-legal :deep(li)::marker {
    color: var(--color-primary-500);
}
</style>
