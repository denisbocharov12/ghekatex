<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { onKeyStroke, useScrollLock } from '@vueuse/core'
import { X, Mail, Phone, MapPin } from 'lucide-vue-next'
import { useContactDialog } from '@site/Composables/contactDialog'
import type { MenuItem, SiteProps } from '@site/Types'

/**
 * Навигация во весь экран.
 *
 * Слева — список разделов и служебный блок, справа кадр во всю высоту окна.
 * Содержимое обязано помещаться в экран: прокрутки внутри меню нет, поэтому
 * кегль и отступы здесь заметно меньше, чем на страницах.
 */
const props = defineProps<{ open: boolean }>()
const emit = defineEmits<{ close: [] }>()

const page = usePage()
const site = computed(() => page.props.site as SiteProps | null)
const locale = computed(() => page.props.locale as string)
const locales = computed(() => page.props.locales as { available: string[]; labels: Record<string, string> })

const items = computed<MenuItem[]>(() => site.value?.menus?.header ?? [])
const legal = computed<MenuItem[]>(() => site.value?.menus?.legal ?? [])
const contacts = computed(() => site.value?.contacts ?? {})
const social = computed(() => site.value?.social ?? {})

/** Кадры разделов: индекс пункта → фотография справа. */
const previews = [
    '/media/preview/menu-1.jpg',
    '/media/preview/menu-2.jpg',
    '/media/preview/menu-3.jpg',
    '/media/preview/menu-4.jpg',
    '/media/preview/menu-5.jpg',
    '/media/preview/menu-6.jpg',
]

const hovered = ref(0)
const locked = useScrollLock(typeof document !== 'undefined' ? document.body : null)

watch(
    () => props.open,
    (value) => {
        locked.value = value

        if (value) hovered.value = 0
    },
    { immediate: true },
)

watch(() => page.url, () => emit('close'))

onKeyStroke('Escape', () => {
    if (props.open) emit('close')
})

function hrefForLocale(target: string): string {
    const url = new URL(window.location.href)
    const segments = url.pathname.split('/').filter(Boolean)

    if (segments.length > 0 && locales.value.available.includes(segments[0])) {
        segments[0] = target
    } else {
        segments.unshift(target)
    }

    url.pathname = `/${segments.join('/')}`

    return url.toString()
}

const socialLinks = computed(() =>
    [
        { key: 'social_facebook', label: 'Facebook' },
        { key: 'social_instagram', label: 'Instagram' },
        { key: 'social_linkedin', label: 'LinkedIn' },
    ].filter((item) => Boolean(social.value[item.key])),
)

const { openDialog } = useContactDialog()

/** Меню закрывается, на его месте открывается форма заявки. */
function requestCall(): void {
    emit('close')
    openDialog()
}

const phoneHref = computed(() => `tel:${String(contacts.value.contact_phone ?? '').replace(/\s/g, '')}`)
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-to-class="opacity-0"
    >
      <div
        v-if="props.open"
        class="on-ink fixed inset-0 z-[90] flex text-white"
        style="background: var(--gradient-ink)"
        role="dialog"
        aria-modal="true"
        :aria-label="$t('nav.menu')"
      >
        <!-- Левая колонка: навигация и служебный блок -->
        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
          <div class="container-site flex h-[var(--header-height)] shrink-0 items-center justify-between lg:pr-8">
            <Link :href="route('home', { locale })" aria-label="GHEKATEX">
              <img src="/brand/logo_horizontal_white.svg" alt="GHEKATEX" class="h-5 w-auto md:h-6" width="180" height="30">
            </Link>

            <button
              type="button"
              class="link-arrow -mr-2 h-12 px-2"
              :aria-label="$t('nav.close')"
              @click="emit('close')"
            >
              <span class="hidden sm:inline">{{ $t('nav.close') }}</span>
              <X :size="20" />
            </button>
          </div>

          <div class="container-site flex min-h-0 flex-1 flex-col justify-between gap-6 pb-7 pt-2 lg:pr-8 lg:pt-4">
            <nav :aria-label="$t('nav.menu')">
              <ul>
                <li
                  v-for="(item, index) in items"
                  :key="item.id"
                  class="border-b border-white/10 first:border-t"
                  @mouseenter="hovered = index"
                >
                  <Link :href="item.href" class="group relative flex items-center gap-5 overflow-hidden py-3 lg:py-3.5">
                    <!-- Подсветка наезжает слева, вместо прежней смены цвета -->
                    <span
                      class="absolute inset-y-0 left-0 w-full origin-left scale-x-0 bg-primary-300/12 transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-x-100"
                      aria-hidden="true"
                    />

                    <span
                      class="relative w-6 shrink-0 font-[family-name:var(--font-display)] text-xs text-white/35 transition-colors duration-300 group-hover:text-primary-300"
                    >
                      {{ String(index + 1).padStart(2, '0') }}
                    </span>

                    <span
                      class="relative flex-1 font-[family-name:var(--font-display)] text-xl leading-tight text-white/80 transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:translate-x-1.5 group-hover:text-white lg:text-[1.75rem]"
                    >
                      {{ item.label }}
                    </span>
                  </Link>
                </li>
              </ul>
            </nav>

            <!-- Контакты, язык и правовые ссылки собраны в одну плотную строку -->
            <div class="grid gap-6 border-t border-white/10 pt-5 sm:grid-cols-12 sm:gap-8">
              <div class="sm:col-span-5">
                <p class="eyebrow">{{ $t('nav.contacts') }}</p>

                <ul class="mt-4 space-y-2 text-sm text-white/65">
                  <li v-if="contacts.contact_address" class="flex gap-2.5">
                    <MapPin :size="14" class="mt-0.5 shrink-0 text-primary-300" />
                    <span>{{ contacts.contact_address }}</span>
                  </li>
                  <li v-if="contacts.contact_phone" class="flex gap-2.5">
                    <Phone :size="14" class="mt-0.5 shrink-0 text-primary-300" />
                    <a :href="phoneHref" class="hover:text-white">{{ contacts.contact_phone }}</a>
                  </li>
                  <li v-if="contacts.contact_email" class="flex gap-2.5">
                    <Mail :size="14" class="mt-0.5 shrink-0 text-primary-300" />
                    <a :href="`mailto:${contacts.contact_email}`" class="hover:text-white">{{ contacts.contact_email }}</a>
                  </li>
                </ul>
              </div>

              <div class="sm:col-span-3">
                <p class="eyebrow">{{ $t('nav.language') }}</p>

                <ul class="mt-4 flex flex-wrap gap-x-5 gap-y-1.5">
                  <li v-for="code in locales.available" :key="code">
                    <a
                      :href="hrefForLocale(code)"
                      class="text-sm transition-colors"
                      :class="code === locale ? 'text-primary-300' : 'text-white/55 hover:text-white'"
                      :lang="code"
                    >
                      {{ locales.labels[code] ?? code }}
                    </a>
                  </li>
                </ul>

                <ul v-if="socialLinks.length" class="mt-5 flex flex-wrap gap-x-5 gap-y-1.5">
                  <li v-for="network in socialLinks" :key="network.key">
                    <a
                      :href="String(social[network.key])"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="text-[0.6875rem] uppercase tracking-[0.14em] text-white/50 transition-colors hover:text-white"
                    >
                      {{ network.label }}
                    </a>
                  </li>
                </ul>
              </div>

              <div class="flex flex-col items-start gap-4 sm:col-span-4 sm:items-end">
                <button type="button" class="btn btn-accent whitespace-nowrap" @click="requestCall">
                  {{ $t('nav.contact_us') }}
                </button>

                <ul class="flex flex-wrap gap-x-4 gap-y-1 sm:justify-end">
                  <li v-for="item in legal" :key="item.id">
                    <Link :href="item.href" class="text-xs text-white/40 transition-colors hover:text-white/80">
                      {{ item.label }}
                    </Link>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Правая колонка: кадр раздела во всю высоту окна -->
        <div class="relative hidden w-[38%] shrink-0 overflow-hidden lg:block xl:w-[42%]">
          <Transition
            enter-active-class="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
            enter-from-class="opacity-0 scale-105"
            leave-active-class="transition-opacity duration-700 absolute inset-0"
            leave-to-class="opacity-0"
          >
            <img
              :key="hovered"
              :src="previews[hovered % previews.length]"
              alt=""
              class="h-full w-full object-cover"
              loading="lazy"
              decoding="async"
            >
          </Transition>

          <span
            class="pointer-events-none absolute inset-0"
            style="background: linear-gradient(90deg, rgba(6,21,35,0.85) 0%, rgba(6,21,35,0.18) 40%, rgba(6,21,35,0.05) 100%)"
          />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
