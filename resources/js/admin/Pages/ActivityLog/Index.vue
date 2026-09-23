<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import type { Paginator } from '@admin/Types/forms'

const props = defineProps<{ items: Paginator }>()

function formatDate(value: string): string {
    return new Date(value).toLocaleString('ru-RU')
}

/** Имя модели без пространства имён — в журнале важен раздел, а не класс. */
function subject(type: string | null): string {
    return type ? (type.split('\\').pop() ?? type) : '—'
}
</script>

<template>
  <Head :title="$t('log.title')" />

  <AdminLayout>
    <PageHeader :title="$t('log.title')" :subtitle="$t('table.total', { count: props.items.total })" />

    <div class="panel overflow-hidden">
      <div v-if="props.items.data.length" class="overflow-x-auto">
        <table class="table-admin">
          <thead>
            <tr>
              <th class="w-48">{{ $t('log.when') }}</th>
              <th class="w-48">{{ $t('log.who') }}</th>
              <th>{{ $t('log.what') }}</th>
              <th class="w-48">{{ $t('log.subject') }}</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in props.items.data" :key="item.id">
              <td class="whitespace-nowrap text-steel-500">{{ formatDate(item.created_at) }}</td>
              <td>{{ item.causer?.name ?? '—' }}</td>
              <td class="text-steel-700">{{ item.description }}</td>
              <td class="text-steel-500">{{ subject(item.subject_type) }} #{{ item.subject_id ?? '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="px-6 py-14 text-center text-sm text-steel-400">{{ $t('table.empty') }}</p>

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
