<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { Send } from 'lucide-vue-next'

/**
 * Форма заявки. Одна и та же на главной, в контактах и на страницах услуг
 * и изделий — источник и связанная сущность приходят пропсами.
 */
const props = withDefaults(
    defineProps<{
        source?: string
        relatedType?: 'product' | 'service' | null
        relatedId?: number | null
        compact?: boolean
        dark?: boolean
    }>(),
    { source: 'contacts', relatedType: null, relatedId: null, compact: false, dark: false },
)

const page = usePage()
const locale = computed(() => page.props.locale as string)

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    country: '',
    subject: '',
    message: '',
    consent: false,
    source: props.source,
    related_type: props.relatedType,
    related_id: props.relatedId,
    company_website: '',
    form_started_at: 0,
})

onMounted(() => {
    // Метка времени открытия формы: боты отправляют её мгновенно
    form.form_started_at = Date.now()
})

function submit(): void {
    form.post(route('contacts.store', { locale: locale.value }), {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'email', 'phone', 'company', 'country', 'subject', 'message', 'consent'),
    })
}

const fieldClass = computed(() =>
    props.dark
        ? 'w-full h-12 px-4 bg-white/10 border border-white/20 text-sm text-white placeholder:text-white/40 focus:outline-none focus:border-accent-400'
        : 'w-full h-12 px-4 bg-white border border-bone-200 text-sm text-bone-800 placeholder:text-bone-400 focus:outline-none focus:border-accent-400',
)

const labelClass = computed(() =>
    props.dark ? 'block mb-1.5 text-xs font-semibold text-white/70' : 'block mb-1.5 text-xs font-semibold text-bone-500',
)
</script>

<template>
  <form class="flex flex-col gap-5" novalidate @submit.prevent="submit">
    <div class="grid gap-5 sm:grid-cols-2">
      <div>
        <label :class="labelClass" for="cf-name">{{ $t('form.name') }} *</label>
        <input id="cf-name" v-model="form.name" type="text" required :class="fieldClass" autocomplete="name">
        <p v-if="form.errors.name" class="mt-1 text-xs text-[color:var(--color-danger)]">{{ form.errors.name }}</p>
      </div>

      <div>
        <label :class="labelClass" for="cf-email">{{ $t('form.email') }} *</label>
        <input id="cf-email" v-model="form.email" type="email" required :class="fieldClass" autocomplete="email">
        <p v-if="form.errors.email" class="mt-1 text-xs text-[color:var(--color-danger)]">{{ form.errors.email }}</p>
      </div>

      <div>
        <label :class="labelClass" for="cf-phone">{{ $t('form.phone') }}</label>
        <input id="cf-phone" v-model="form.phone" type="tel" :class="fieldClass" autocomplete="tel">
      </div>

      <div>
        <label :class="labelClass" for="cf-company">{{ $t('form.company') }}</label>
        <input id="cf-company" v-model="form.company" type="text" :class="fieldClass" autocomplete="organization">
      </div>

      <div v-if="!props.compact">
        <label :class="labelClass" for="cf-country">{{ $t('form.country') }}</label>
        <input id="cf-country" v-model="form.country" type="text" :class="fieldClass" autocomplete="country-name">
      </div>

      <div v-if="!props.compact">
        <label :class="labelClass" for="cf-subject">{{ $t('form.subject') }}</label>
        <input id="cf-subject" v-model="form.subject" type="text" :class="fieldClass">
      </div>
    </div>

    <div>
      <label :class="labelClass" for="cf-message">{{ $t('form.message') }} *</label>
      <textarea
        id="cf-message"
        v-model="form.message"
        rows="5"
        required
        :placeholder="$t('form.message_hint')"
        :class="[fieldClass, 'h-auto py-3 resize-y']"
      />
      <p v-if="form.errors.message" class="mt-1 text-xs text-[color:var(--color-danger)]">{{ form.errors.message }}</p>
    </div>

    <!-- Поле-ловушка: человек его не видит и не заполняет -->
    <div class="hidden" aria-hidden="true">
      <label for="cf-website">Website</label>
      <input id="cf-website" v-model="form.company_website" type="text" tabindex="-1" autocomplete="off">
    </div>

    <label class="flex items-start gap-2.5 text-xs" :class="props.dark ? 'text-white/65' : 'text-bone-500'">
      <input v-model="form.consent" type="checkbox" required class="mt-0.5 accent-[var(--color-accent-500)]">
      <span>
        {{ $t('form.consent') }} —
        <Link
          :href="route('pages.show', { locale, page: 'privacy-policy' })"
          class="underline underline-offset-2"
          :class="props.dark ? 'text-accent-300' : 'text-primary-700 hover:text-accent-600'"
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
</template>
