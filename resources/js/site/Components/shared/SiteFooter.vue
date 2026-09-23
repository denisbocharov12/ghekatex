<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { Mail, MapPin, Phone, Clock, ArrowUpRight, ArrowUp } from 'lucide-vue-next'
import type { SiteProps } from '@site/Types'

const page = usePage()

const site = computed(() => page.props.site as SiteProps | null)
const locale = computed(() => page.props.locale as string)
const general = computed(() => site.value?.general ?? {})
const contacts = computed(() => site.value?.contacts ?? {})
const social = computed(() => site.value?.social ?? {})

const networks = computed(() =>
    [
        { key: 'social_facebook', label: 'Facebook' },
        { key: 'social_instagram', label: 'Instagram' },
        { key: 'social_linkedin', label: 'LinkedIn' },
    ].filter((item) => Boolean(social.value[item.key])),
)

const email = ref('')
const consent = ref(false)
const sending = ref(false)

function subscribe(): void {
    if (!email.value || !consent.value || sending.value) return

    sending.value = true

    router.post(
        route('subscribe.store', { locale: locale.value }),
        { email: email.value, consent: consent.value },
        {
            preserveScroll: true,
            onFinish: () => {
                sending.value = false
                email.value = ''
                consent.value = false
            },
        },
    )
}

function toTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const year = new Date().getFullYear()
</script>

<template>
  <footer class="on-ink relative overflow-hidden bg-ink-950 text-white" style="background: var(--gradient-ink)">
    <div class="container-site relative pb-10 pt-14 lg:pt-20">
      <!-- Крупная подпись бренда вместо мелкого логотипа в углу -->
      <div class="border-b border-white/12 pb-12">
        <img src="/brand/logo_horizontal_white.svg" alt="GHEKATEX" class="h-8 w-auto lg:h-10" width="240" height="40">

        <p class="mt-6 max-w-md text-sm leading-relaxed text-white/60">
          {{ general.company_short_about }}
        </p>
      </div>

      <div class="grid gap-12 pt-12 lg:grid-cols-12 lg:gap-10">
        <nav class="lg:col-span-5" :aria-label="$t('footer.nav_title')">
          <p class="eyebrow">{{ $t('footer.nav_title') }}</p>

          <ul class="mt-6 grid gap-x-10 gap-y-2 sm:grid-cols-2">
            <li v-for="item in [...(site?.menus?.footer_primary ?? []), ...(site?.menus?.footer_secondary ?? [])]" :key="item.id">
              <Link
                :href="item.href"
                class="group inline-flex items-center gap-2 py-1.5 font-[family-name:var(--font-display)] text-lg text-white/75 transition-colors hover:text-white"
              >
                {{ item.label }}
                <ArrowUpRight
                  :size="15"
                  class="text-accent-400 opacity-0 transition-all duration-300 group-hover:translate-x-0.5 group-hover:opacity-100"
                />
              </Link>
            </li>
          </ul>
        </nav>

        <div class="lg:col-span-3">
          <p class="eyebrow">{{ $t('footer.contacts_title') }}</p>

          <ul class="mt-6 space-y-4 text-sm text-white/70">
            <li v-if="contacts.contact_address" class="flex gap-3">
              <MapPin :size="16" class="mt-0.5 shrink-0 text-accent-400" />
              <span>{{ contacts.contact_address }}</span>
            </li>
            <li v-if="contacts.contact_phone" class="flex gap-3">
              <Phone :size="16" class="mt-0.5 shrink-0 text-accent-400" />
              <a :href="`tel:${String(contacts.contact_phone).replace(/\s/g, '')}`" class="hover:text-white">
                {{ contacts.contact_phone }}
              </a>
            </li>
            <li v-if="contacts.contact_email" class="flex gap-3">
              <Mail :size="16" class="mt-0.5 shrink-0 text-accent-400" />
              <a :href="`mailto:${contacts.contact_email}`" class="hover:text-white">{{ contacts.contact_email }}</a>
            </li>
            <li v-if="contacts.contact_hours" class="flex gap-3">
              <Clock :size="16" class="mt-0.5 shrink-0 text-accent-400" />
              <span>{{ contacts.contact_hours }}</span>
            </li>
          </ul>

          <ul v-if="networks.length" class="mt-7 flex flex-wrap gap-2">
            <li v-for="network in networks" :key="network.key">
              <a
                :href="String(social[network.key])"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 border border-white/20 px-3 py-1.5 text-[0.6875rem] uppercase tracking-[0.14em] text-white/70 transition-colors hover:border-accent-400 hover:text-white"
              >
                {{ network.label }}
                <ArrowUpRight :size="12" />
              </a>
            </li>
          </ul>
        </div>

        <div class="lg:col-span-4">
          <p class="eyebrow">{{ $t('footer.subscribe_title') }}</p>

          <p class="mt-6 text-sm leading-relaxed text-white/60">{{ $t('footer.subscribe_text') }}</p>

          <form class="mt-6" @submit.prevent="subscribe">
            <label class="sr-only" for="footer-email">{{ $t('form.email') }}</label>

            <div class="flex border-b border-white/25 transition-colors focus-within:border-accent-400">
              <input
                id="footer-email"
                v-model="email"
                type="email"
                required
                :placeholder="$t('form.subscribe_placeholder')"
                class="min-w-0 flex-1 bg-transparent py-3 text-sm text-white placeholder:text-white/35 focus:outline-none"
              >

              <button
                type="submit"
                class="link-arrow shrink-0 px-2"
                :disabled="sending"
                :aria-label="$t('form.subscribe_submit')"
              >
                {{ $t('form.subscribe_submit') }}
                <ArrowUpRight :size="14" />
              </button>
            </div>

            <label class="mt-4 flex items-start gap-2.5 text-xs text-white/50">
              <input v-model="consent" type="checkbox" required class="mt-0.5 accent-[var(--color-accent-500)]">
              <span>{{ $t('form.consent') }}</span>
            </label>
          </form>
        </div>
      </div>

      <div class="mt-7 flex flex-col gap-5 border-t border-white/12 pt-6 md:flex-row md:items-center md:justify-between">
        <p class="text-xs text-white/40">
          © {{ year }} {{ general.company_legal_name || 'Ghekatex Group SRL' }}. {{ $t('footer.rights') }}
          <span class="ml-1 text-white/30">Powered by BeYond Solutions</span>
        </p>

        <ul class="flex flex-wrap items-center gap-x-6 gap-y-2">
          <li v-for="item in site?.menus?.legal ?? []" :key="item.id">
            <Link :href="item.href" class="text-xs text-white/40 transition-colors hover:text-white/80">
              {{ item.label }}
            </Link>
          </li>
          <li>
            <button type="button" class="text-xs text-white/40 transition-colors hover:text-white/80" @click="$emit('manage-cookies')">
              {{ $t('cookie.manage') }}
            </button>
          </li>
          <li>
            <button type="button" class="link-arrow text-[0.6875rem]" @click="toTop">
              {{ $t('common.back') }}
              <ArrowUp :size="13" />
            </button>
          </li>
        </ul>
      </div>
    </div>
  </footer>
</template>
