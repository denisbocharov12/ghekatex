<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Shirt, Wrench, Newspaper, Images, Award, Inbox, ArrowRight } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import StatusBadge from '@admin/Components/ui/StatusBadge.vue'

const props = defineProps<{
    counters: Record<string, number>
    latestRequests: { id: number; name: string; email: string; company: string | null; source: string; status: string; created_at: string }[]
    requestsChart: { date: string; count: number }[]
}>()

const tiles = computed(() => [
    { key: 'products', icon: Shirt, route: 'admin.products.index', value: props.counters.products },
    { key: 'services', icon: Wrench, route: 'admin.services.index', value: props.counters.services },
    { key: 'posts', icon: Newspaper, route: 'admin.posts.index', value: props.counters.posts },
    { key: 'media', icon: Images, route: 'admin.media-items.index', value: props.counters.media },
    { key: 'certificates', icon: Award, route: 'admin.certificates.index', value: props.counters.certificates },
    { key: 'requests_new', icon: Inbox, route: 'admin.requests.index', value: props.counters.requests_new, accent: true },
])

/** Высота столбца в процентах от максимума за период. */
const maxCount = computed(() => Math.max(1, ...props.requestsChart.map((point) => point.count)))

const statusLabels: Record<string, string> = {
    new: 'Новая',
    in_progress: 'В работе',
    answered: 'Отвечена',
    spam: 'Спам',
    archived: 'В архиве',
}

const statusTones: Record<string, string> = {
    new: 'accent',
    in_progress: 'info',
    answered: 'success',
    spam: 'danger',
    archived: 'muted',
}

function formatDate(value: string): string {
    return new Date(value).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' })
}
</script>

<template>
  <Head :title="$t('dashboard.title')" />

  <AdminLayout>
    <PageHeader :title="$t('dashboard.title')" />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
      <Link
        v-for="tile in tiles"
        :key="tile.key"
        :href="route(tile.route)"
        class="panel group p-5 transition-colors hover:border-primary-300"
      >
        <component
          :is="tile.icon"
          :size="20"
          :class="tile.accent ? 'text-accent-600' : 'text-steel-400'"
        />
        <p class="mt-4 text-3xl font-semibold text-steel-900">{{ tile.value ?? 0 }}</p>
        <p class="mt-1 text-xs text-steel-500">{{ $t(`dashboard.${tile.key}`) }}</p>
      </Link>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-3">
      <section class="panel p-5 lg:col-span-2">
        <h2 class="text-sm font-semibold text-steel-800">{{ $t('dashboard.requests_chart') }}</h2>

        <div class="mt-6 flex h-40 items-end gap-1">
          <div
            v-for="point in props.requestsChart"
            :key="point.date"
            class="group relative flex-1 rounded-t bg-primary-100 transition-colors hover:bg-primary-300"
            :style="{ height: `${Math.max(4, (point.count / maxCount) * 100)}%` }"
          >
            <span
              class="pointer-events-none absolute -top-7 left-1/2 hidden -translate-x-1/2 whitespace-nowrap rounded bg-steel-800 px-2 py-1 text-[0.6875rem] text-white group-hover:block"
            >
              {{ formatDate(point.date) }}: {{ point.count }}
            </span>
          </div>
        </div>
      </section>

      <section class="panel overflow-hidden lg:col-span-1">
        <div class="flex items-center justify-between gap-3 border-b border-steel-200 px-5 py-4">
          <h2 class="text-sm font-semibold text-steel-800">{{ $t('dashboard.latest_requests') }}</h2>

          <Link :href="route('admin.requests.index')" class="text-steel-400 hover:text-primary-700">
            <ArrowRight :size="16" />
          </Link>
        </div>

        <ul v-if="props.latestRequests.length" class="divide-y divide-steel-100">
          <li v-for="request in props.latestRequests" :key="request.id">
            <Link :href="route('admin.requests.show', request.id)" class="block px-5 py-3 hover:bg-steel-50">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <p class="truncate text-sm font-medium text-steel-800">{{ request.name }}</p>
                  <p class="truncate text-xs text-steel-400">{{ request.company || request.email }}</p>
                </div>

                <StatusBadge :tone="statusTones[request.status] ?? 'muted'" :label="statusLabels[request.status] ?? request.status" />
              </div>
            </Link>
          </li>
        </ul>

        <p v-else class="px-5 py-10 text-center text-sm text-steel-400">{{ $t('dashboard.no_requests') }}</p>
      </section>
    </div>
  </AdminLayout>
</template>
