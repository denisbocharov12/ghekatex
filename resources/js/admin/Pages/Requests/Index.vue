<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { useDebounceFn } from '@vueuse/core'
import { Search, Eye } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import StatusBadge from '@admin/Components/ui/StatusBadge.vue'
import type { Paginator } from '@admin/Types/forms'

const props = defineProps<{
    items: Paginator
    statuses: { value: string; label: string; tone: string }[]
    sources: string[]
}>()

const params = new URLSearchParams(window.location.search)
const search = ref(params.get('filter[search]') ?? '')
const status = ref(params.get('filter[status]') ?? '')

const apply = useDebounceFn(() => {
    const filter: Record<string, string> = {}

    if (search.value) filter.search = search.value
    if (status.value) filter.status = status.value

    router.get(route('admin.requests.index'), Object.keys(filter).length ? { filter } : {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}, 400)

watch([search, status], () => apply())

function toneOf(value: string): string {
    return props.statuses.find((item) => item.value === value)?.tone ?? 'muted'
}

function labelOf(value: string): string {
    return props.statuses.find((item) => item.value === value)?.label ?? value
}

function formatDate(value: string): string {
    return new Date(value).toLocaleString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <Head :title="$t('requests.title')" />

  <AdminLayout>
    <PageHeader :title="$t('requests.title')" :subtitle="$t('table.total', { count: props.items.total })" />

    <div class="panel overflow-hidden">
      <div class="flex flex-wrap items-center gap-3 border-b border-steel-200 p-4">
        <div class="relative max-w-xs flex-1">
          <Search :size="16" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-steel-400" />
          <input v-model="search" type="search" :placeholder="$t('action.search')" class="field pl-9">
        </div>

        <select v-model="status" class="field max-w-48">
          <option value="">{{ $t('action.filter') }}</option>
          <option v-for="item in props.statuses" :key="item.value" :value="item.value">{{ item.label }}</option>
        </select>
      </div>

      <div v-if="props.items.data.length" class="overflow-x-auto">
        <table class="table-admin">
          <thead>
            <tr>
              <th>{{ $t('requests.name') }}</th>
              <th>E-mail</th>
              <th>{{ $t('requests.company') }}</th>
              <th>{{ $t('requests.source') }}</th>
              <th>{{ $t('requests.status') }}</th>
              <th>{{ $t('table.created') }}</th>
              <th class="w-16 text-right">{{ $t('table.actions') }}</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in props.items.data" :key="item.id">
              <td class="font-medium text-steel-800">{{ item.name }}</td>
              <td>
                <a :href="`mailto:${item.email}`" class="text-primary-700 hover:underline">{{ item.email }}</a>
              </td>
              <td class="text-steel-500">{{ item.company || '—' }}</td>
              <td class="text-steel-500">{{ item.source }}</td>
              <td><StatusBadge :tone="toneOf(item.status)" :label="labelOf(item.status)" /></td>
              <td class="whitespace-nowrap text-steel-500">{{ formatDate(item.created_at) }}</td>
              <td class="text-right">
                <Link
                  :href="route('admin.requests.show', item.id)"
                  class="btn btn-ghost h-9 w-9 p-0"
                  :aria-label="$t('action.open')"
                >
                  <Eye :size="15" />
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="px-6 py-16 text-center text-sm text-steel-400">{{ $t('table.empty') }}</p>

      <nav v-if="props.items.last_page > 1" class="flex items-center justify-between gap-4 border-t border-steel-200 px-4 py-3">
        <p class="text-sm text-steel-500">
          {{ $t('table.page_of', { current: props.items.current_page, last: props.items.last_page }) }}
        </p>

        <div class="flex flex-wrap gap-1">
          <component
            :is="link.url ? Link : 'span'"
            v-for="link in props.items.links"
            :key="link.label"
            :href="link.url || undefined"
            preserve-scroll
            :class="[
              'inline-flex h-9 min-w-9 items-center justify-center rounded px-2 text-sm',
              link.active ? 'bg-primary-900 text-white' : link.url ? 'text-steel-600 hover:bg-steel-100' : 'text-steel-300',
            ]"
            v-html="link.label"
          />
        </div>
      </nav>
    </div>
  </AdminLayout>
</template>
