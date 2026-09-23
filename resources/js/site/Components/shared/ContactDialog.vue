<script setup lang="ts">
import { computed, nextTick, ref, watch } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { onKeyStroke, useScrollLock } from '@vueuse/core'
import { X, Send, Mail, Phone, MapPin, Clock, Check } from 'lucide-vue-next'
import { useContactDialog } from '@site/Composables/contactDialog'
import type { SiteProps } from '@site/Types'

/**
 * Модальное окно обратной связи.
 *
 * Живёт в макете в единственном экземпляре: открывают его «Связаться с нами»
 * в шапке и меню и призыв к действию на обложке. Слева — форма с перечнем
 * услуг, справа — прямые контакты для тех, кому проще написать напрямую.
 */
const { open, preselected, closeDialog } = useContactDialog()

const page = usePage()

const locale = computed(() => page.props.locale as string)
const site = computed(() => page.props.site as SiteProps | null)
const contacts = computed(() => site.value?.contacts ?? {})
const services = computed(() => site.value?.services ?? [])

const dialog = ref<HTMLElement | null>(null)
const nameField = ref<HTMLInputElement | null>(null)
const sent = ref(false)

const locked = useScrollLock(typeof document !== 'undefined' ? document.body : null)

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    country: '',
    subject: '',
    message: '',
    consent: false,
    source: 'dialog',
    related_type: null as string | null,
    related_id: null as number | null,
    company_website: '',
    form_started_at: 0,
})

/** Отмеченные услуги уезжают в тему обращения — отдельного поля в заявке нет. */
const chosen = ref<string[]>([])

function toggle(slug: string): void {
    chosen.value = chosen.value.includes(slug)
        ? chosen.value.filter((item) => item !== slug)
        : [...chosen.value, slug]
}

watch(chosen, (value) => {
    form.subject = services.value
        .filter((service) => value.includes(service.slug))
        .map((service) => service.name)
        .join(', ')
})

watch(open, async (value) => {
    locked.value = value

    if (!value) return

    sent.value = false
    form.clearErrors()
    form.form_started_at = Date.now()
    chosen.value = [...preselected.value]

    await nextTick()
    nameField.value?.focus()
})

onKeyStroke('Escape', () => {
    if (open.value) closeDialog()
})

function submit(): void {
    form.post(route('contacts.store', { locale: locale.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            sent.value = true
            chosen.value = []
            form.reset('name', 'email', 'phone', 'company', 'country', 'subject', 'message', 'consent')
        },
    })
}

const fieldClass =
    'h-12 w-full border border-bone-200 bg-white px-4 text-sm text-bone-800 transition-colors placeholder:text-bone-400 focus:border-accent-500 focus:outline-none'

const labelClass = 'mb-1.5 block text-xs font-semibold text-bone-500'
</script>

<template>
  <Transition
    enter-active-class="transition-opacity duration-300"
    enter-from-class="opacity-0"
    leave-active-class="transition-opacity duration-200"
    leave-to-class="opacity-0"
  >
    <div
      v-if="open"
      ref="dialog"
      class="fixed inset-0 z-[80] flex items-start justify-center overflow-y-auto bg-ink-950/80 p-4 backdrop-blur-sm sm:p-8"
      role="dialog"
      aria-modal="true"
      :aria-label="$t('dialog.title')"
      @click.self="closeDialog"
    >
      <div class="relative my-auto grid w-full max-w-5xl bg-white shadow-2xl lg:grid-cols-12">
        <!-- Кнопка лежит поверх тёмной колонки контактов, поэтому она светлая -->
        <button
          type="button"
          class="absolute right-3 top-3 z-10 inline-flex h-10 w-10 items-center justify-center text-bone-500 transition-colors hover:bg-bone-50 hover:text-ink-900 lg:text-white/65 lg:hover:bg-white/12 lg:hover:text-white"
          :aria-label="$t('nav.close')"
          @click="closeDialog"
        >
          <X :size="20" />
        </button>

        <!-- Форма -->
        <div class="px-6 py-8 sm:px-9 sm:py-10 lg:col-span-7">
          <p class="eyebrow">{{ $t('nav.contact_us') }}</p>

          <h2 class="heading-2 mt-4 text-2xl lg:text-3xl">{{ $t('dialog.title') }}</h2>

          <p class="mt-3 text-sm leading-relaxed text-bone-600">{{ $t('dialog.text') }}</p>

          <p v-if="sent" class="mt-6 flex items-start gap-2.5 border border-accent-200 bg-accent-50 p-4 text-sm text-accent-700">
            <Check :size="16" class="mt-0.5 shrink-0" />
            {{ $t('form.success') }}
          </p>

          <form class="mt-6 flex flex-col gap-5" novalidate @submit.prevent="submit">
            <div v-if="services.length">
              <p :class="labelClass">
                {{ $t('dialog.services_title') }}
                <span class="font-normal text-bone-400">— {{ $t('dialog.services_hint') }}</span>
              </p>

              <ul class="flex flex-wrap gap-2">
                <li v-for="service in services" :key="service.slug">
                  <button
                    type="button"
                    :aria-pressed="chosen.includes(service.slug)"
                    :class="[
                      'border px-3.5 py-2 text-xs transition-colors',
                      chosen.includes(service.slug)
                        ? 'border-accent-500 bg-accent-50 text-accent-700'
                        : 'border-bone-300 text-bone-600 hover:border-accent-400 hover:text-accent-700',
                    ]"
                    @click="toggle(service.slug)"
                  >
                    {{ service.name }}
                  </button>
                </li>
              </ul>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
              <div>
                <label :class="labelClass" for="cd-name">{{ $t('form.name') }} *</label>
                <input id="cd-name" ref="nameField" v-model="form.name" type="text" required :class="fieldClass" autocomplete="name">
                <p v-if="form.errors.name" class="mt-1 text-xs text-[color:var(--color-danger)]">{{ form.errors.name }}</p>
              </div>

              <div>
                <label :class="labelClass" for="cd-email">{{ $t('form.email') }} *</label>
                <input id="cd-email" v-model="form.email" type="email" required :class="fieldClass" autocomplete="email">
                <p v-if="form.errors.email" class="mt-1 text-xs text-[color:var(--color-danger)]">{{ form.errors.email }}</p>
              </div>

              <div>
                <label :class="labelClass" for="cd-phone">{{ $t('form.phone') }}</label>
                <input id="cd-phone" v-model="form.phone" type="tel" :class="fieldClass" autocomplete="tel">
              </div>

              <div>
                <label :class="labelClass" for="cd-company">{{ $t('form.company') }}</label>
                <input id="cd-company" v-model="form.company" type="text" :class="fieldClass" autocomplete="organization">
              </div>
            </div>

            <div>
              <label :class="labelClass" for="cd-message">{{ $t('form.message') }} *</label>
              <textarea
                id="cd-message"
                v-model="form.message"
                rows="4"
                required
                :placeholder="$t('form.message_hint')"
                :class="[fieldClass, 'h-auto resize-y py-3']"
              />
              <p v-if="form.errors.message" class="mt-1 text-xs text-[color:var(--color-danger)]">{{ form.errors.message }}</p>
            </div>

            <!-- Поле-ловушка: человек его не видит и не заполняет -->
            <div class="hidden" aria-hidden="true">
              <label for="cd-website">Website</label>
              <input id="cd-website" v-model="form.company_website" type="text" tabindex="-1" autocomplete="off">
            </div>

            <label class="flex items-start gap-2.5 text-xs text-bone-500">
              <input v-model="form.consent" type="checkbox" required class="mt-0.5 accent-[var(--color-accent-500)]">
              <span>
                {{ $t('form.consent') }} —
                <Link
                  :href="route('pages.show', { locale, page: 'privacy-policy' })"
                  class="text-primary-700 underline underline-offset-2 hover:text-accent-600"
                  @click="closeDialog"
                >
                  {{ $t('form.consent_link') }}
                </Link>
              </span>
            </label>
            <p v-if="form.errors.consent" class="-mt-3 text-xs text-[color:var(--color-danger)]">{{ form.errors.consent }}</p>

            <button type="submit" class="btn btn-accent self-start" :disabled="form.processing">
              <Send :size="16" />
              {{ form.processing ? $t('form.sending') : $t('form.submit') }}
            </button>
          </form>
        </div>

        <!-- Прямые контакты -->
        <aside
          class="on-ink relative flex flex-col justify-between gap-8 px-6 py-8 text-white sm:px-9 sm:py-10 lg:col-span-5"
          style="background: var(--gradient-ink)"
        >
          <div>
            <p class="eyebrow">{{ $t('dialog.contacts_title') }}</p>

            <ul class="mt-7 space-y-5 text-sm">
              <li v-if="contacts.contact_email">
                <a :href="`mailto:${contacts.contact_email}`" class="group flex gap-3 text-white/80 hover:text-white">
                  <Mail :size="17" class="mt-0.5 shrink-0 text-accent-400" />
                  <span class="font-[family-name:var(--font-display)] text-lg">{{ contacts.contact_email }}</span>
                </a>
              </li>

              <li v-if="contacts.contact_phone">
                <a
                  :href="`tel:${String(contacts.contact_phone).replace(/\s/g, '')}`"
                  class="group flex gap-3 text-white/80 hover:text-white"
                >
                  <Phone :size="17" class="mt-0.5 shrink-0 text-accent-400" />
                  <span class="font-[family-name:var(--font-display)] text-lg">{{ contacts.contact_phone }}</span>
                </a>
              </li>

              <li v-if="contacts.contact_address" class="flex gap-3 text-white/70">
                <MapPin :size="17" class="mt-0.5 shrink-0 text-accent-400" />
                <span>{{ contacts.contact_address }}</span>
              </li>

              <li v-if="contacts.contact_hours" class="flex gap-3 text-white/70">
                <Clock :size="17" class="mt-0.5 shrink-0 text-accent-400" />
                <span>{{ contacts.contact_hours }}</span>
              </li>
            </ul>
          </div>

          <div class="border-t border-white/15 pt-6">
            <Link
              :href="route('contacts.index', { locale })"
              class="link-arrow mt-4"
              @click="closeDialog"
            >
              {{ $t('nav.contacts') }}
            </Link>
          </div>
        </aside>
      </div>
    </div>
  </Transition>
</template>
