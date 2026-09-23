<script setup lang="ts">
import { computed } from 'vue'
import LucideIcon from '@site/Components/ui/LucideIcon.vue'
import CountUp from '@site/Components/ui/CountUp.vue'
import SectionHeading from '@site/Components/shared/SectionHeading.vue'
import BrandMark from '@site/Components/ui/BrandMark.vue'
import type { Advantage } from '@site/Types'

const props = defineProps<{ items: Advantage[] }>()

const counters = computed(() => props.items.filter((item) => item.is_counter && item.value))
const features = computed(() => props.items.filter((item) => !item.is_counter || !item.value))
</script>

<template>
  <!-- Секция плотнее остальных: знак ушёл под заголовок, отступы сжаты -->
  <section
    v-if="props.items.length"
    class="grain section-ink pattern-veil relative overflow-hidden py-12 md:py-14 lg:py-16"
  >
    <div class="container-site relative">
      <div class="relative">
        <BrandMark
          :size="180"
          class="pointer-events-none absolute -left-12 -top-5 z-0 hidden opacity-20 lg:block"
        />

        <SectionHeading
          class="relative z-[1]"
          :eyebrow="$t('home.advantages_eyebrow')"
          :title="$t('home.advantages_title')"
        />
      </div>

      <div v-if="counters.length" class="mt-8 grid gap-px bg-white/10 sm:grid-cols-2 lg:grid-cols-4">
        <div
          v-for="(item, position) in counters"
          :key="item.id"
          v-reveal="position * 90"
          class="bg-ink-900/70 px-6 py-7"
        >
          <p class="font-[family-name:var(--font-display)] text-5xl font-light leading-none text-white lg:text-6xl">
            <CountUp :value="item.value" />
          </p>

          <p v-if="item.value_suffix" class="mt-3 text-[0.6875rem] uppercase tracking-[0.18em] text-accent-300">
            {{ item.value_suffix }}
          </p>

          <p class="mt-5 text-sm font-semibold text-white">{{ item.title }}</p>
          <p v-if="item.description" class="mt-2 text-sm leading-relaxed text-white/55">{{ item.description }}</p>
        </div>
      </div>

      <div v-if="features.length" class="mt-8 grid gap-x-10 gap-y-8 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="(item, position) in features"
          :key="item.id"
          v-reveal="position * 90"
          class="flex gap-5 border-t border-white/12 pt-6"
        >
          <LucideIcon :name="item.icon" :size="24" class="mt-0.5 shrink-0 text-accent-400" />

          <div>
            <h3 class="text-base font-semibold text-white">{{ item.title }}</h3>
            <p v-if="item.description" class="mt-2.5 text-sm leading-relaxed text-white/60">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
