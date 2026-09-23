<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ArrowUpRight } from 'lucide-vue-next'
import LucideIcon from './LucideIcon.vue'
import type { Service } from '@site/Types'

const props = defineProps<{ service: Service }>()

const page = usePage()
const locale = computed(() => page.props.locale as string)
</script>

<template>
  <Link
    :href="route('services.show', { locale, service: props.service.slug })"
    class="group flex h-full flex-col justify-between gap-8 border-t border-bone-300 pt-6 transition-colors hover:border-accent-500"
  >
    <div>
      <LucideIcon :name="props.service.icon" :size="26" class="text-accent-600" />

      <h3 class="heading-3 mt-5 text-xl transition-colors group-hover:text-accent-700">{{ props.service.name }}</h3>

      <p v-if="props.service.summary" class="mt-3 text-sm leading-relaxed text-bone-600">{{ props.service.summary }}</p>
    </div>

    <span class="link-arrow">
      {{ $t('common.more') }}
      <ArrowUpRight :size="14" />
    </span>
  </Link>
</template>
