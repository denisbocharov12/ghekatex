<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useDebounceFn } from '@vueuse/core'
import { Plus, Search, Pencil, Trash2, GripVertical, Eye, EyeOff } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import ConfirmModal from '@admin/Components/ui/ConfirmModal.vue'
import StatusBadge from '@admin/Components/ui/StatusBadge.vue'
import { usePermissions } from '@admin/Composables/usePermissions'
import type { Column, Paginator } from '@admin/Types/forms'

/**
 * Список раздела админки.
 *
 * Все разделы ведут себя одинаково: поиск, сортировка, публикация,
 * порядок и удаление. Различаются только колонки — их и передаёт страница.
 */
const props = withDefaults(
    defineProps<{
        title: string
        routePrefix: string
        permission: string
        columns: Column[]
        items: Paginator
        searchable?: boolean
        reorderable?: boolean
        toggleable?: boolean
        titleKey?: string
    }>(),
    { searchable: true, reorderable: true, toggleable: true, titleKey: 'name' },
)

const page = usePage()
const { can } = usePermissions()

const defaultLocale = computed(() => (page.props.locales as { default: string })?.default ?? 'ro')

const search = ref(new URLSearchParams(window.location.search).get('filter[search]') ?? '')

const applySearch = useDebounceFn(() => {
    router.get(
        route(`admin.${props.routePrefix}.index`),
        search.value ? { filter: { search: search.value } } : {},
        { preserveState: true, preserveScroll: true, replace: true },
    )
}, 400)

watch(search, () => applySearch())

const deleting = ref<number | null>(null)

function confirmDelete(): void {
    if (deleting.value === null) return

    router.delete(route(`admin.${props.routePrefix}.destroy`, deleting.value), {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    })
}

function toggle(id: number): void {
    router.patch(route(`admin.${props.routePrefix}.toggle`, id), {}, { preserveScroll: true, preserveState: false })
}

/** Значение ячейки с учётом переводимых полей и дат. */
function cell(item: Record<string, any>, column: Column): string {
    const raw = item[column.key]

    if (raw === null || raw === undefined || raw === '') return '—'

    if (column.type === 'translated' || (typeof raw === 'object' && !Array.isArray(raw))) {
        return raw[defaultLocale.value] || Object.values(raw).find(Boolean) || '—'
    }

    if (column.type === 'date') {
        return new Date(raw).toLocaleDateString('ru-RU')
    }

    if (column.type === 'boolean') {
        return raw ? '✓' : '—'
    }

    return String(raw)
}

function rowTitle(item: Record<string, any>): string {
    return cell(item, { key: props.titleKey, label: '', type: 'translated' })
}

/** Перетаскивание строк: сохраняем новый порядок одним запросом. */
const dragIndex = ref<number | null>(null)

function onDrop(targetIndex: number): void {
    if (dragIndex.value === null || dragIndex.value === targetIndex) return

    const rows = [...props.items.data]
    const [moved] = rows.splice(dragIndex.value, 1)
    rows.splice(targetIndex, 0, moved)

    dragIndex.value = null

    router.post(
        route(`admin.${props.routePrefix}.reorder`),
        { rows: rows.map((row, index) => ({ id: row.id, sort_order: index })) },
        { preserveScroll: true },
    )
}
</script>

<template>
  <Head :title="props.title" />

  <AdminLayout>
    <PageHeader :title="props.title" :subtitle="$t('table.total', { count: props.items.total })">
      <template #actions>
        <Link
          v-if="can(`${props.permission}.create`)"
          :href="route(`admin.${props.routePrefix}.create`)"
          class="btn btn-primary"
        >
          <Plus :size="16" />
          {{ $t('action.create') }}
        </Link>
      </template>
    </PageHeader>

    <div class="panel overflow-hidden">
      <div v-if="props.searchable" class="border-b border-steel-200 p-4">
        <div class="relative max-w-sm">
          <Search :size="16" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-steel-400" />
          <input v-model="search" type="search" :placeholder="$t('action.search')" class="field pl-9">
        </div>
      </div>

      <div v-if="props.items.data.length" class="overflow-x-auto">
        <table class="table-admin">
          <thead>
            <tr>
              <th v-if="props.reorderable" class="w-10" />
              <th v-for="column in props.columns" :key="column.key" :style="column.width ? { width: column.width } : undefined">
                {{ column.label }}
              </th>
              <th v-if="props.toggleable" class="w-28">{{ $t('table.status') }}</th>
              <th class="w-24 text-right">{{ $t('table.actions') }}</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(item, index) in props.items.data"
              :key="item.id"
              :draggable="props.reorderable && can(`${props.permission}.update`)"
              @dragstart="dragIndex = index"
              @dragover.prevent
              @drop="onDrop(index)"
            >
              <td v-if="props.reorderable" class="cursor-grab text-steel-300">
                <GripVertical :size="16" />
              </td>

              <td v-for="column in props.columns" :key="column.key">
                <img
                  v-if="column.type === 'image'"
                  :src="item[column.key] || '/brand/icon_pattern.svg'"
                  :alt="rowTitle(item)"
                  class="h-10 w-10 rounded object-cover"
                  loading="lazy"
                >
                <span v-else class="block max-w-xs truncate" :title="cell(item, column)">{{ cell(item, column) }}</span>
              </td>

              <td v-if="props.toggleable">
                <button
                  type="button"
                  class="inline-flex items-center gap-1.5"
                  :disabled="!can(`${props.permission}.update`)"
                  @click="toggle(item.id)"
                >
                  <StatusBadge
                    :tone="item.is_active ? 'success' : 'muted'"
                    :label="item.is_active ? $t('status.active') : $t('status.inactive')"
                  />
                  <Eye v-if="item.is_active" :size="14" class="text-steel-300" />
                  <EyeOff v-else :size="14" class="text-steel-300" />
                </button>
              </td>

              <td class="text-right">
                <div class="flex justify-end gap-1">
                  <Link
                    v-if="can(`${props.permission}.view`)"
                    :href="route(`admin.${props.routePrefix}.edit`, item.id)"
                    class="btn btn-ghost h-9 w-9 p-0"
                    :aria-label="$t('action.edit')"
                  >
                    <Pencil :size="15" />
                  </Link>

                  <button
                    v-if="can(`${props.permission}.delete`) && !item.is_system"
                    type="button"
                    class="btn btn-ghost h-9 w-9 p-0 text-[color:var(--color-danger)]"
                    :aria-label="$t('action.delete')"
                    @click="deleting = item.id"
                  >
                    <Trash2 :size="15" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="px-6 py-16 text-center">
        <p class="text-sm font-medium text-steel-600">{{ $t('table.empty') }}</p>
        <p class="mt-1 text-sm text-steel-400">{{ $t('table.empty_hint') }}</p>
      </div>

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

    <ConfirmModal :open="deleting !== null" @confirm="confirmDelete" @cancel="deleting = null" />
  </AdminLayout>
</template>
