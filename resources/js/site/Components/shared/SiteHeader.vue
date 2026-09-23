<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useWindowScroll } from '@vueuse/core'
import { Facebook, Instagram, Linkedin, ArrowUpRight } from 'lucide-vue-next'
import MegaMenu from './MegaMenu.vue'
import LocaleSwitcher from './LocaleSwitcher.vue'
import { useContactDialog } from '@site/Composables/contactDialog'
import type { SiteProps } from '@site/Types'

/**
 * Шапка сжата до одной строки: знак, призыв связаться, соцсети и меню.
 *
 * Телефон убран намеренно: заказчики пишут, а не звонят, и один понятный
 * призыв работает лучше строки цифр. Навигация живёт в полноэкранном окне.
 */
const props = withDefaults(defineProps<{ transparent?: boolean }>(), { transparent: true })

const page = usePage()
const { y } = useWindowScroll()

const menuOpen = ref(false)

const { openDialog } = useContactDialog()

const site = computed(() => page.props.site as SiteProps | null)
const locale = computed(() => page.props.locale as string)
const social = computed(() => site.value?.social ?? {})

const networks = computed(() =>
    [
        { key: 'social_facebook', icon: Facebook, label: 'Facebook' },
        { key: 'social_instagram', icon: Instagram, label: 'Instagram' },
        { key: 'social_linkedin', icon: Linkedin, label: 'LinkedIn' },
    ].filter((item) => Boolean(social.value[item.key])),
)

const solid = computed(() => !props.transparent || y.value > 40)

watch(() => page.url, () => (menuOpen.value = false))
</script>

<template>
  <header
    :class="[
      'fixed inset-x-0 top-0 z-[80] transition-[background-color,border-color,backdrop-filter] duration-500',
      solid ? 'border-b border-bone-200/80 bg-bone-100/92 backdrop-blur-md' : 'border-b border-white/10 bg-transparent',
    ]"
  >
    <div class="container-site flex h-[var(--header-height)] items-center justify-between gap-6">
      <Link :href="route('home', { locale })" class="shrink-0" aria-label="GHEKATEX">
        <img
          :src="solid ? '/brand/logo_horizontal_filled.svg' : '/brand/logo_horizontal_white.svg'"
          alt="GHEKATEX"
          class="h-5 w-auto transition-opacity duration-300 md:h-6"
          width="180"
          height="30"
        >
      </Link>

      <div class="flex items-center gap-3 md:gap-6">
        <ul v-if="networks.length" class="hidden items-center gap-1 md:flex">
          <li v-for="network in networks" :key="network.key">
            <a
              :href="String(social[network.key])"
              target="_blank"
              rel="noopener noreferrer"
              :aria-label="network.label"
              :class="[
                'inline-flex h-10 w-10 items-center justify-center transition-colors',
                solid ? 'text-ink-500 hover:text-accent-600' : 'text-white/70 hover:text-white',
              ]"
            >
              <component :is="network.icon" :size="17" />
            </a>
          </li>
        </ul>

        <button
          type="button"
          :class="[
            'group hidden items-center gap-2 text-[0.6875rem] font-semibold uppercase tracking-[0.16em] transition-colors sm:inline-flex',
            solid ? 'text-ink-900 hover:text-accent-600' : 'text-white hover:text-accent-300',
          ]"
          @click="openDialog()"
        >
          {{ $t('nav.contact_us') }}
          <ArrowUpRight :size="14" class="transition-transform duration-300 group-hover:translate-x-0.5" />
        </button>

        <span
          :class="['hidden h-5 w-px md:block', solid ? 'bg-bone-300' : 'bg-white/25']"
          aria-hidden="true"
        />

        <LocaleSwitcher :dark="!solid" />

        <!-- Кнопка меню: штрихи перестраиваются при наведении -->
        <button
          type="button"
          class="group flex h-12 items-center gap-3 px-1"
          :aria-label="$t('nav.menu')"
          :aria-expanded="menuOpen"
          @click="menuOpen = true"
        >
          <span
            :class="[
              'hidden text-[0.6875rem] font-semibold uppercase tracking-[0.22em] transition-colors sm:inline',
              solid ? 'text-ink-900 group-hover:text-accent-600' : 'text-white group-hover:text-accent-300',
            ]"
          >
            {{ $t('nav.menu') }}
          </span>

          <span class="flex h-5 w-7 flex-col justify-center gap-[5px]">
            <span
              v-for="line in 3"
              :key="line"
              :class="[
                'block h-px origin-right transition-all duration-300',
                solid ? 'bg-ink-900' : 'bg-white',
                line === 2 ? 'w-4/5 group-hover:w-full' : 'w-full group-hover:w-4/5',
              ]"
              :style="{ transitionDelay: `${line * 45}ms` }"
            />
          </span>
        </button>
      </div>
    </div>

    <MegaMenu :open="menuOpen" @close="menuOpen = false" />
  </header>
</template>
