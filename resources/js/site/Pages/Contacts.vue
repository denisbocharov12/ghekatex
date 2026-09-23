<script setup lang="ts">
import { computed } from 'vue'
import { Mail, MapPin, Phone, Clock, ArrowDown } from 'lucide-vue-next'
import SiteLayout from '@site/Layouts/SiteLayout.vue'
import SeoHead from '@site/Components/shared/SeoHead.vue'
import PageHero from '@site/Components/shared/PageHero.vue'
import SectionHeading from '@site/Components/shared/SectionHeading.vue'
import ContactForm from '@site/Components/shared/ContactForm.vue'
import OfficeMap from '@site/Components/shared/OfficeMap.vue'
import { useContactDialog } from '@site/Composables/contactDialog'
import type { Breadcrumb, Office, SeoProps } from '@site/Types'

/**
 * Контакты.
 *
 * Страница начинается с короткой строки прямых контактов: большинству
 * посетителей нужен телефон или почта, а не список площадок. Дальше —
 * карточки офисов, карта и форма заявки.
 */
const props = defineProps<{
    seo: SeoProps
    breadcrumbs: Breadcrumb[]
    offices: Office[]
    contacts: Record<string, string | null>
}>()

const { openDialog } = useContactDialog()

const typeLabels: Record<string, string> = {
    office: 'contacts.type_office',
    factory: 'contacts.type_factory',
    warehouse: 'contacts.type_warehouse',
}

const mappable = computed(() => props.offices.filter((office) => office.latitude && office.longitude))

/** Прямые контакты из настроек: то, что нужно в первую очередь. */
const quick = computed(() =>
    [
        {
            key: 'phone',
            icon: Phone,
            label: 'contacts.phone',
            value: props.contacts.contact_phone,
            href: `tel:${String(props.contacts.contact_phone ?? '').replace(/\s/g, '')}`,
        },
        {
            key: 'email',
            icon: Mail,
            label: 'contacts.email',
            value: props.contacts.contact_email,
            href: `mailto:${props.contacts.contact_email ?? ''}`,
        },
        {
            key: 'hours',
            icon: Clock,
            label: 'contacts.hours',
            value: props.contacts.contact_hours,
            href: null,
        },
    ].filter((item) => Boolean(item.value)),
)
</script>

<template>
  <SeoHead :seo="props.seo" />

  <SiteLayout>
    <PageHero :title="$t('nav.contacts')" :subtitle="props.contacts.contact_address" :breadcrumbs="props.breadcrumbs" />

    <!-- Прямые контакты: крупно и сразу под шапкой -->
    <section v-if="quick.length" class="section pb-0">
      <div class="container-site">
        <div class="grid gap-px border border-bone-200 bg-bone-200 sm:grid-cols-2 lg:grid-cols-4">
          <component
            :is="item.href ? 'a' : 'div'"
            v-for="item in quick"
            :key="item.key"
            :href="item.href || undefined"
            class="group flex flex-col justify-between gap-6 bg-white p-6 transition-colors hover:bg-bone-50"
          >
            <component :is="item.icon" :size="20" class="text-accent-600" />

            <div>
              <p class="text-[0.6875rem] uppercase tracking-[0.18em] text-bone-500">{{ $t(item.label) }}</p>
              <p class="mt-2 font-[family-name:var(--font-display)] text-lg text-ink-900 transition-colors group-hover:text-accent-700">
                {{ item.value }}
              </p>
            </div>
          </component>

          <div class="on-ink flex flex-col justify-between gap-6 bg-ink-900 p-6 text-white">
            <MapPin :size="20" class="text-accent-400" />

            <div>
              <p class="text-[0.6875rem] uppercase tracking-[0.18em] text-white/50">{{ $t('nav.contact_us') }}</p>

              <button
                type="button"
                class="link-arrow mt-3 text-white hover:text-accent-300"
                @click="openDialog()"
              >
                {{ $t('form.submit') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container-site">
        <SectionHeading :title="$t('contacts.offices')" />

        <div class="mt-8 grid gap-5 md:grid-cols-2">
          <article
            v-for="(office, index) in props.offices"
            :key="office.id"
            v-reveal="(index % 2) * 80"
            class="flex flex-col border border-bone-200 p-6 transition-colors hover:border-accent-400"
          >
            <div class="flex items-center justify-between gap-4">
              <p class="text-[0.6875rem] uppercase tracking-[0.18em] text-accent-600">
                {{ $t(typeLabels[office.type] ?? 'contacts.type_office') }}
              </p>

              <a
                v-if="office.latitude && office.longitude"
                href="#map"
                class="text-[0.6875rem] uppercase tracking-[0.16em] text-bone-400 transition-colors hover:text-primary-700"
              >
                {{ $t('contacts.map') }}
              </a>
            </div>

            <h3 class="heading-3 mt-3 text-xl">{{ office.name }}</h3>

            <ul class="mt-5 space-y-3.5 text-sm">
              <li class="flex gap-3">
                <MapPin :size="17" class="mt-0.5 shrink-0 text-accent-600" />
                <span class="text-bone-600">
                  {{ office.address }}<span v-if="office.city">, {{ office.city }}</span>
                  <span v-if="office.postal">, {{ office.postal }}</span>
                </span>
              </li>

              <li v-if="office.phones.length" class="flex gap-3">
                <Phone :size="17" class="mt-0.5 shrink-0 text-accent-600" />
                <span class="flex flex-col gap-1">
                  <a
                    v-for="phone in office.phones"
                    :key="phone"
                    :href="`tel:${phone.replace(/\s/g, '')}`"
                    class="text-bone-700 hover:text-accent-600"
                  >
                    {{ phone }}
                  </a>
                </span>
              </li>

              <li v-if="office.emails.length" class="flex gap-3">
                <Mail :size="17" class="mt-0.5 shrink-0 text-accent-600" />
                <span class="flex flex-col gap-1">
                  <a
                    v-for="email in office.emails"
                    :key="email"
                    :href="`mailto:${email}`"
                    class="text-bone-700 hover:text-accent-600"
                  >
                    {{ email }}
                  </a>
                </span>
              </li>

              <li v-if="office.hours" class="flex gap-3">
                <Clock :size="17" class="mt-0.5 shrink-0 text-accent-600" />
                <span class="text-bone-600">{{ office.hours }}</span>
              </li>
            </ul>
          </article>
        </div>
      </div>
    </section>

    <!-- Карта во всю ширину: площадки видно без прокрутки колонок -->
    <section v-if="mappable.length" id="map" class="scroll-mt-[calc(var(--header-height)+1rem)]">
      <div v-reveal class="border-y border-bone-200">
        <OfficeMap :offices="mappable" />
      </div>
    </section>

    <section class="on-ink section section-malachite">
      <div class="container-site grid gap-10 lg:grid-cols-12 lg:gap-10">
        <div class="lg:col-span-5">
          <SectionHeading
            :eyebrow="$t('home.contact_eyebrow')"
            :title="$t('contacts.form_title')"
            :text="$t('home.contact_text')"
          />

          <a
            href="#map"
            class="link-arrow mt-8 hidden lg:inline-flex"
          >
            {{ $t('contacts.map') }}
            <ArrowDown :size="14" class="rotate-180" />
          </a>
        </div>

        <div class="lg:col-span-7">
          <ContactForm source="contacts" dark />
        </div>
      </div>
    </section>
  </SiteLayout>
</template>
