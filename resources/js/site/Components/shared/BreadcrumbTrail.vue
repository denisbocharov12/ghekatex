<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'
import type { Breadcrumb } from '@site/Types'

defineProps<{ items: Breadcrumb[]; dark?: boolean }>()
</script>

<template>
  <nav :aria-label="$t('common.home')" class="text-xs">
    <ol class="flex flex-wrap items-center gap-1.5">
      <li v-for="(item, index) in items" :key="index" class="flex items-center gap-1.5">
        <ChevronRight v-if="index > 0" :size="13" :class="dark ? 'text-white/40' : 'text-bone-400'" />

        <Link
          v-if="item.url && index < items.length - 1"
          :href="item.url"
          :class="dark ? 'text-white/70 hover:text-white' : 'text-bone-500 hover:text-accent-600'"
        >
          {{ item.label }}
        </Link>
        <span v-else :class="dark ? 'text-white' : 'text-bone-800'">{{ item.label }}</span>
      </li>
    </ol>
  </nav>
</template>
