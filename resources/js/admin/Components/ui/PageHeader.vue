<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ChevronLeft } from 'lucide-vue-next'

defineProps<{ title: string; subtitle?: string | null; backUrl?: string | null }>()
</script>

<template>
  <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
      <Link v-if="backUrl" :href="backUrl" class="mb-2 inline-flex items-center gap-1 text-sm text-steel-500 hover:text-primary-700">
        <ChevronLeft :size="15" />
        {{ $t('action.back') }}
      </Link>

      <h1 class="text-2xl font-semibold text-steel-900">{{ title }}</h1>
      <p v-if="subtitle" class="mt-1 text-sm text-steel-500">{{ subtitle }}</p>
    </div>

    <div class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>
