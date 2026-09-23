<script setup lang="ts">
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { onClickOutside } from '@vueuse/core'
import { Globe, Check } from 'lucide-vue-next'

const props = withDefaults(defineProps<{ dark?: boolean }>(), { dark: false })

const page = usePage()
const open = ref(false)
const root = ref<HTMLElement | null>(null)

onClickOutside(root, () => (open.value = false))

const locale = computed(() => page.props.locale as string)
const locales = computed(() => page.props.locales as { available: string[]; labels: Record<string, string> })

/**
 * Ссылка на ту же страницу в другом языке: подменяем первый сегмент пути.
 * Параметры запроса сохраняем — фильтр каталога не должен сбрасываться.
 */
function hrefFor(target: string): string {
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
</script>

<template>
  <div ref="root" class="relative">
    <button
      type="button"
      class="inline-flex items-center gap-1.5 h-11 px-2 text-sm font-semibold uppercase tracking-wider transition-colors"
      :class="props.dark ? 'text-white hover:text-accent-300' : 'text-primary-900 hover:text-accent-600'"
      :aria-label="$t('nav.language')"
      :aria-expanded="open"
      @click="open = !open"
    >
      <Globe :size="16" />
      {{ locale }}
    </button>

    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 scale-95"
      leave-active-class="transition duration-100 ease-in"
      leave-to-class="opacity-0 scale-95"
    >
      <ul
        v-if="open"
        class="absolute right-0 top-full mt-1 min-w-[10rem] bg-white border border-bone-200 shadow-lg py-1 origin-top-right"
      >
        <li v-for="code in locales.available" :key="code">
          <a
            :href="hrefFor(code)"
            class="flex items-center justify-between gap-3 px-4 py-2.5 text-sm text-bone-700 hover:bg-bone-50"
            :lang="code"
          >
            {{ locales.labels[code] ?? code }}
            <Check v-if="code === locale" :size="15" class="text-accent-600" />
          </a>
        </li>
      </ul>
    </Transition>
  </div>
</template>
