<script setup lang="ts">
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import SectionHeading from '@site/Components/shared/SectionHeading.vue'
import ContactForm from '@site/Components/shared/ContactForm.vue'
import ServiceCard from '@site/Components/ui/ServiceCard.vue'
import LucideIcon from '@site/Components/ui/LucideIcon.vue'
import type { Breadcrumb, SeoProps, Service } from '@site/Types'

const props = defineProps<{
    seo: SeoProps
    breadcrumbs: Breadcrumb[]
    service: Service
    others: Service[]
}>()
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero
      :eyebrow="$t('nav.services')"
      :title="props.service.name"
      :subtitle="props.service.summary"
      :image="props.service.cover"
      :breadcrumbs="props.breadcrumbs"
    />

    <section class="section">
      <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-10">
        <div class="lg:col-span-7">
          <div v-if="props.service.description" class="prose-site" v-html="props.service.description" />
        </div>

        <aside class="lg:col-span-5">
          <div class="border-t-2 border-accent-500 pt-7 lg:sticky lg:top-28">
            <h2 class="eyebrow">{{ $t('services.highlights') }}</h2>

            <dl class="mt-5 divide-y divide-bone-200">
              <div v-if="props.service.lead_time" class="flex items-start justify-between gap-6 py-3">
                <dt class="text-sm text-bone-500">{{ $t('services.lead_time') }}</dt>
                <dd class="text-sm font-semibold text-primary-900">{{ props.service.lead_time }}</dd>
              </div>

              <div
                v-for="highlight in props.service.highlights ?? []"
                :key="highlight.label"
                class="flex items-start justify-between gap-6 py-3"
              >
                <dt class="flex items-center gap-2 text-sm text-bone-500">
                  <LucideIcon :name="highlight.icon" :size="15" class="text-accent-600" />
                  {{ highlight.label }}
                </dt>
                <dd class="text-sm font-semibold text-primary-900">{{ highlight.value }}</dd>
              </div>
            </dl>

            <a href="#request" class="btn btn-accent mt-6 w-full">{{ $t('services.request') }}</a>
          </div>
        </aside>
      </div>
    </section>

    <section v-if="props.service.process_steps?.length" class="section bg-bone-50">
      <div class="container-site">
        <SectionHeading :title="$t('services.process')" />

        <ol class="mt-6 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
          <li
            v-for="(step, index) in props.service.process_steps"
            :key="index"
            class="relative border-t-2 border-accent-500/30 pt-6" v-reveal="index * 80"
          >
            <span class="font-[family-name:var(--font-display)] text-4xl font-light text-accent-500">
              {{ String(index + 1).padStart(2, '0') }}
            </span>
            <h3 class="mt-3 text-base font-semibold text-primary-900">{{ step.title }}</h3>
            <p v-if="step.text" class="mt-2 text-sm leading-relaxed text-bone-500">{{ step.text }}</p>
          </li>
        </ol>
      </div>
    </section>

    <section v-if="props.service.gallery?.length" class="section">
      <div class="container-site">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <figure
            v-for="(item, index) in props.service.gallery"
            :key="index"
            class="media-frame aspect-4/3 border border-bone-200" v-reveal="(index % 3) * 70"
          >
            <img :src="item.url" :alt="item.alt || props.service.name" loading="lazy" decoding="async" class="h-full w-full object-cover">
          </figure>
        </div>
      </div>
    </section>

    <section id="request" class="on-ink section section-malachite">
      <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-10">
        <div class="lg:col-span-5">
          <SectionHeading :eyebrow="$t('home.contact_eyebrow')" :title="$t('services.request')" :text="$t('home.contact_text')" />
        </div>
        <div class="lg:col-span-7">
          <ContactForm source="service" related-type="service" :related-id="props.service.id" dark compact />
        </div>
      </div>
    </section>

    <section v-if="props.others.length" class="section">
      <div class="container-site">
        <SectionHeading :title="$t('services.others')" />

        <div class="mt-7 grid gap-6 md:grid-cols-3">
          <ServiceCard v-for="item in props.others" :key="item.id" :service="item" />
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
