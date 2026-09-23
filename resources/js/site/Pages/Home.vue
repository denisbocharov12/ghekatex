<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ArrowUpRight, Play } from 'lucide-vue-next'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import SectionHeading from '@site/Components/shared/SectionHeading.vue'
import ContactForm from '@site/Components/shared/ContactForm.vue'
import VideoHero from '@site/Components/sections/VideoHero.vue'
import VideoBanner from '@site/Components/sections/VideoBanner.vue'
import AdvantagesSection from '@site/Components/sections/AdvantagesSection.vue'
import FaqSection from '@site/Components/sections/FaqSection.vue'
import ProductCard from '@site/Components/ui/ProductCard.vue'
import ServiceCard from '@site/Components/ui/ServiceCard.vue'
import PostCard from '@site/Components/ui/PostCard.vue'
import BrandMark from '@site/Components/ui/BrandMark.vue'
import type { Advantage, FaqEntry, HeroSlide, Office, Post, Product, ProductCategory, SeoProps, Service } from '@site/Types'

const props = defineProps<{
    seo: SeoProps
    slides: HeroSlide[]
    advantages: Advantage[]
    categories: ProductCategory[]
    featured: Product[]
    services: Service[]
    posts: Post[]
    faqs: FaqEntry[]
    partners: { id: number; name: string; logo: string | null; website: string | null }[]
    office: Office | null
    intro: {
        eyebrow: string | null
        title: string | null
        text: string | null
        video_url: string | null
        video_title: string | null
    }
}>()

const page = usePage()
const locale = computed(() => page.props.locale as string)

/** Лента партнёров повторяется дважды — так анимация замыкается без шва. */
const marqueeItems = computed(() => [...props.partners, ...props.partners])
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout transparent-header>
    <VideoHero v-if="props.slides.length" :slide="props.slides[0]" />

    <!-- Кто мы -->
    <section v-if="props.intro.title" id="intro" class="section">
      <div class="container-site">
        <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">
          <div v-reveal class="lg:col-span-5 lg:pt-10">
            <SectionHeading :eyebrow="props.intro.eyebrow" :title="props.intro.title || ''" />

            <div v-if="props.intro.text" class="prose-site mt-6" v-html="props.intro.text" />

            <Link :href="route('about', { locale })" class="btn btn-outline mt-6">
              {{ $t('nav.about') }}
              <ArrowUpRight :size="16" />
            </Link>
          </div>

          <!-- Кадр выходит за колонку вправо: полоса воздуха слева держит ритм разворота -->
          <div class="lg:col-span-7 lg:-mr-[max(0px,calc((100vw-1440px)/2))]">
            <div v-reveal class="reveal-wipe media-frame aspect-4/3">
              <img
                src="/media/preview/menu-3.jpg"
                :alt="props.intro.title || 'GHEKATEX'"
                loading="lazy"
                decoding="async"
                width="1600"
                height="1200"
              >
            </div>
          </div>
        </div>

        <!-- Подпись под разворотом: год, знак и пояснение к видео -->
        <div class="mt-6 flex flex-wrap items-center justify-between gap-6 border-t border-bone-200 pt-6">
          <p class="max-w-md text-sm leading-relaxed text-bone-600">{{ props.intro.video_title }}</p>

          <div class="flex items-center gap-6">
            <span class="font-[family-name:var(--font-display)] text-3xl text-ink-900">2014</span>
            <span class="h-px w-14 bg-accent-500" aria-hidden="true" />
            <BrandMark :size="64" tone="dark" />
          </div>
        </div>
      </div>
    </section>

    <AdvantagesSection :items="props.advantages" />

    <!-- Типы изделий -->
    <section v-if="props.categories.length" class="section section-bone">
      <div class="container-site">
        <div class="flex flex-wrap items-end justify-between gap-6">
          <SectionHeading
            :eyebrow="$t('home.categories_eyebrow')"
            :title="$t('home.categories_title')"
            :text="$t('home.categories_text')"
          />

          <Link :href="route('catalog.index', { locale })" class="link-arrow">
            {{ $t('common.all') }}
            <ArrowUpRight :size="14" />
          </Link>
        </div>

        <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <Link
            v-for="(category, position) in props.categories"
            :key="category.id"
            v-reveal="(position % 3) * 90"
            :href="route('catalog.category', { locale, category: category.slug })"
            class="group media-frame media-veil relative block aspect-4/5"
          >
            <img
              v-if="category.cover"
              :src="category.cover"
              :alt="category.name"
              loading="lazy"
              decoding="async"
              width="800"
              height="1000"
            >
            <span v-else class="pattern-veil block h-full w-full" aria-hidden="true" />

            <div class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-between gap-4 p-6">
              <div>
                <h3 class="font-[family-name:var(--font-display)] text-2xl font-medium text-white lg:text-3xl">
                  {{ category.name }}
                </h3>
                <p v-if="category.products_count" class="mt-1.5 text-[0.6875rem] uppercase tracking-[0.18em] text-accent-300">
                  {{ category.products_count }} {{ $t('catalog.pieces') }}
                </p>
              </div>

              <span
                class="inline-flex h-11 w-11 shrink-0 translate-y-2 items-center justify-center border border-white/40 text-white opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:border-accent-400 group-hover:opacity-100"
              >
                <ArrowUpRight :size="17" />
              </span>
            </div>
          </Link>
        </div>
      </div>
    </section>

    <!-- Видеополоса о производстве -->
    <VideoBanner src="/media/video/production_video.mp4" poster="/media/preview/menu-1.jpg" height="tall">
      <div v-reveal class="max-w-3xl">
        <p class="eyebrow">{{ $t('home.video_eyebrow') }}</p>

        <p class="heading-1 mt-6 text-white">{{ props.intro.video_title }}</p>

        <p class="lead mt-6">{{ $t('home.contact_text') }}</p>

        <Link :href="route('media.index', { locale })" class="btn btn-outline mt-6">
          <span>{{ $t('nav.media') }}</span>
          <Play :size="15" />
        </Link>
      </div>
    </VideoBanner>

    <!-- Витрина изделий -->
    <section v-if="props.featured.length" class="section">
      <div class="container-site">
        <div class="flex flex-wrap items-end justify-between gap-6">
          <SectionHeading :eyebrow="$t('home.featured_eyebrow')" :title="$t('home.featured_title')" />

          <Link :href="route('catalog.index', { locale })" class="link-arrow">
            {{ $t('common.all') }}
            <ArrowUpRight :size="14" />
          </Link>
        </div>

        <div class="mt-7 grid gap-x-5 gap-y-8 sm:grid-cols-2 lg:grid-cols-4">
          <div v-for="(product, position) in props.featured" :key="product.id" v-reveal="(position % 4) * 80">
            <ProductCard :product="product" />
          </div>
        </div>
      </div>
    </section>

    <!-- Услуги -->
    <section v-if="props.services.length" class="section section-bone">
      <div class="container-site">
        <div class="flex flex-wrap items-end justify-between gap-6">
          <SectionHeading :eyebrow="$t('home.services_eyebrow')" :title="$t('home.services_title')" />

          <Link :href="route('services.index', { locale })" class="link-arrow">
            {{ $t('common.all') }}
            <ArrowUpRight :size="14" />
          </Link>
        </div>

        <div class="mt-7 grid gap-x-8 gap-y-8 md:grid-cols-2 lg:grid-cols-4">
          <div v-for="(service, position) in props.services" :key="service.id" v-reveal="(position % 4) * 80">
            <ServiceCard :service="service" />
          </div>
        </div>
      </div>
    </section>

    <!-- Партнёры бегущей строкой -->
    <section v-if="props.partners.length" class="border-y border-bone-200 bg-bone-50 py-12">
      <div class="container-site">
        <p class="eyebrow">{{ $t('home.partners_eyebrow') }}</p>
      </div>

      <div class="marquee mt-6">
        <ul class="marquee__track" aria-hidden="false">
          <li
            v-for="(partner, position) in marqueeItems"
            :key="`${partner.id}-${position}`"
            class="flex shrink-0 items-center gap-4"
          >
            <img
              v-if="partner.logo"
              :src="partner.logo"
              :alt="partner.name"
              loading="lazy"
              decoding="async"
              class="h-8 w-auto opacity-45 grayscale transition duration-300 hover:opacity-100 hover:grayscale-0"
            >
            <span
              v-else
              class="font-[family-name:var(--font-display)] text-2xl text-bone-500 transition-colors duration-300 hover:text-ink-900"
            >
              {{ partner.name }}
            </span>

            <span class="h-1 w-1 rounded-full bg-accent-400" aria-hidden="true" />
          </li>
        </ul>
      </div>
    </section>

    <!-- Новости -->
    <section v-if="props.posts.length" class="section">
      <div class="container-site">
        <div class="flex flex-wrap items-end justify-between gap-6">
          <SectionHeading :eyebrow="$t('home.news_eyebrow')" :title="$t('home.news_title')" />

          <Link :href="route('news.index', { locale })" class="link-arrow">
            {{ $t('common.all') }}
            <ArrowUpRight :size="14" />
          </Link>
        </div>

        <div class="mt-7 grid gap-x-6 gap-y-8 md:grid-cols-3">
          <div v-for="(post, position) in props.posts" :key="post.id" v-reveal="position * 90">
            <PostCard :post="post" />
          </div>
        </div>
      </div>
    </section>

    <FaqSection :items="props.faqs" />

    <!-- Заявка поверх видео -->
    <VideoBanner src="/media/video/additional.mp4" poster="/media/preview/menu-4.jpg" height="tall" tone="malachite">
      <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">
        <div v-reveal class="lg:col-span-5">
          <SectionHeading
            :eyebrow="$t('home.contact_eyebrow')"
            :title="$t('home.contact_title')"
            :text="$t('home.contact_text')"
          />

          <dl v-if="props.office" class="mt-6 space-y-6 text-sm">
            <div v-if="props.office.address">
              <dt class="text-[0.6875rem] uppercase tracking-[0.18em] text-accent-300">{{ $t('contacts.offices') }}</dt>
              <dd class="mt-2 text-white/80">{{ props.office.address }}, {{ props.office.city }}</dd>
            </div>

            <div v-if="props.office.phones.length">
              <dt class="text-[0.6875rem] uppercase tracking-[0.18em] text-accent-300">{{ $t('contacts.phone') }}</dt>
              <dd class="mt-2">
                <a :href="`tel:${props.office.phones[0].replace(/\s/g, '')}`" class="text-white/80 hover:text-white">
                  {{ props.office.phones[0] }}
                </a>
              </dd>
            </div>

            <div v-if="props.office.emails.length">
              <dt class="text-[0.6875rem] uppercase tracking-[0.18em] text-accent-300">{{ $t('contacts.email') }}</dt>
              <dd class="mt-2">
                <a :href="`mailto:${props.office.emails[0]}`" class="text-white/80 hover:text-white">
                  {{ props.office.emails[0] }}
                </a>
              </dd>
            </div>
          </dl>
        </div>

        <div v-reveal="120" class="lg:col-span-7">
          <div class="border border-white/15 bg-ink-950/55 p-6 backdrop-blur-sm lg:p-10">
            <ContactForm source="home_quick" dark compact />
          </div>
        </div>
      </div>
    </VideoBanner>
  </SiteLayout>
</template>
