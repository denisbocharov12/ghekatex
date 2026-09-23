<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Cookie } from 'lucide-vue-next'
import { useConsent, type ConsentConfig } from '@site/Composables/useConsent'

const props = defineProps<{ open?: boolean }>()
const emit = defineEmits<{ close: [] }>()

const page = usePage()
const config = computed(() => page.props.consent as ConsentConfig)
const locale = computed(() => page.props.locale as string)

const { decided, granted, save } = useConsent(config.value)

const showSettings = ref(false)
const selection = ref<Record<string, boolean>>({
    necessary: true,
    analytics: granted.value.includes('analytics'),
    marketing: granted.value.includes('marketing'),
})

// Ссылка «Настройки cookie» в подвале открывает баннер повторно
const visible = computed(() => props.open === true || !decided.value)

watch(
    () => props.open,
    (value) => {
        if (value) showSettings.value = true
    },
)

const descriptions: Record<string, { title: string; text: string }> = {
    necessary: { title: 'cookie.necessary', text: 'cookie.necessary_text' },
    analytics: { title: 'cookie.analytics', text: 'cookie.analytics_text' },
    marketing: { title: 'cookie.marketing', text: 'cookie.marketing_text' },
}

async function accept(categories: string[]): Promise<void> {
    await save(categories)
    showSettings.value = false
    emit('close')
}

function acceptSelected(): void {
    accept(Object.entries(selection.value)
        .filter(([, enabled]) => enabled)
        .map(([name]) => name))
}
</script>

<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-6"
    leave-active-class="transition duration-200 ease-in"
    leave-to-class="opacity-0 translate-y-6"
  >
    <div
      v-if="visible"
      class="fixed inset-x-0 bottom-0 z-[60] p-4 sm:p-6"
      role="dialog"
      aria-modal="false"
      :aria-label="$t('cookie.title')"
    >
      <div class="container-site">
        <div class="panel bg-white border border-bone-200 shadow-2xl p-6 lg:p-8">
          <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-10">
            <div class="flex-1">
              <div class="flex items-center gap-3">
                <Cookie :size="20" class="text-accent-600" />
                <h2 class="text-lg font-semibold text-primary-900">{{ $t('cookie.title') }}</h2>
              </div>

              <p class="mt-3 text-sm leading-relaxed text-bone-600 max-w-2xl">
                {{ $t('cookie.text') }}
                <Link
                  :href="route('pages.show', { locale, page: 'cookie-policy' })"
                  class="underline underline-offset-2 text-primary-700 hover:text-accent-600"
                >
                  {{ $t('cookie.policy') }}
                </Link>
              </p>

              <div v-if="showSettings" class="mt-6 grid gap-3 sm:grid-cols-3">
                <label
                  v-for="category in config.categories"
                  :key="category"
                  class="flex gap-3 border border-bone-200 p-4 cursor-pointer transition-colors hover:border-accent-300"
                >
                  <input
                    v-model="selection[category]"
                    type="checkbox"
                    class="mt-0.5 accent-[var(--color-accent-500)]"
                    :disabled="category === 'necessary'"
                  >
                  <span>
                    <span class="block text-sm font-semibold text-primary-900">{{ $t(descriptions[category].title) }}</span>
                    <span class="mt-1 block text-xs leading-relaxed text-bone-500">{{ $t(descriptions[category].text) }}</span>
                  </span>
                </label>
              </div>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row lg:flex-col lg:w-56 lg:shrink-0">
              <button type="button" class="btn btn-ink w-full" @click="accept(['analytics', 'marketing'])">
                {{ $t('cookie.accept_all') }}
              </button>

              <button
                v-if="showSettings"
                type="button"
                class="btn btn-outline w-full"
                @click="acceptSelected"
              >
                {{ $t('cookie.save') }}
              </button>
              <button
                v-else
                type="button"
                class="btn btn-outline w-full"
                @click="showSettings = true"
              >
                {{ $t('cookie.settings') }}
              </button>

              <button type="button" class="btn btn-outline w-full" @click="accept([])">
                {{ $t('cookie.reject') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>
