<script setup lang="ts">
import { ref } from 'vue'
import { Plus } from 'lucide-vue-next'
import SectionHeading from '@site/Components/shared/SectionHeading.vue'
import type { FaqEntry } from '@site/Types'

/**
 * Частые вопросы.
 *
 * Открыт всегда один вопрос: так список остаётся обозримым, а ответ
 * не теряется среди соседних. Разметка — details/summary, чтобы работали
 * клавиатура и поиск по странице без дополнительного кода.
 */
const props = withDefaults(
    defineProps<{ items: FaqEntry[]; eyebrow?: string | null; title?: string | null }>(),
    { eyebrow: null, title: null },
)

const open = ref<number | null>(props.items[0]?.id ?? null)

function toggle(id: number): void {
    open.value = open.value === id ? null : id
}
</script>

<template>
  <section v-if="props.items.length" class="section section-bone">
    <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-10">
      <!-- Шапка едет вместе со списком: вопросов много, заголовок не должен уезжать -->
      <div v-reveal class="lg:col-span-4 lg:sticky lg:top-[calc(var(--header-height)+2rem)] lg:self-start">
        <SectionHeading
          :eyebrow="props.eyebrow ?? $t('home.faq_eyebrow')"
          :title="props.title ?? $t('faq.title')"
        />
      </div>

      <ul class="lg:col-span-8">
        <li
          v-for="(item, position) in props.items"
          :key="item.id"
          v-reveal="position * 60"
          class="border-t border-bone-300 last:border-b"
        >
          <h3>
            <button
              type="button"
              class="group flex w-full items-start justify-between gap-6 py-6 text-left"
              :aria-expanded="open === item.id"
              :aria-controls="`faq-answer-${item.id}`"
              @click="toggle(item.id)"
            >
              <span
                class="font-[family-name:var(--font-display)] text-xl leading-snug text-ink-900 transition-colors group-hover:text-accent-700 lg:text-2xl"
              >
                {{ item.question }}
              </span>

              <span
                class="mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center border border-bone-300 text-ink-700 transition-all duration-300 group-hover:border-accent-500 group-hover:text-accent-600"
                :class="open === item.id ? 'rotate-45' : ''"
              >
                <Plus :size="16" />
              </span>
            </button>
          </h3>

          <Transition
            enter-active-class="transition-all duration-400 ease-out overflow-hidden"
            enter-from-class="max-h-0 opacity-0"
            enter-to-class="max-h-[40rem] opacity-100"
            leave-active-class="transition-all duration-300 ease-in overflow-hidden"
            leave-from-class="max-h-[40rem] opacity-100"
            leave-to-class="max-h-0 opacity-0"
          >
            <div v-show="open === item.id" :id="`faq-answer-${item.id}`">
              <div class="prose-site max-w-3xl pb-7" v-html="item.answer" />
            </div>
          </Transition>
        </li>
      </ul>
    </div>
  </section>
</template>
