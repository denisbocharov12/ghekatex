<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Plus, Pencil, Trash2 } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import ConfirmModal from '@admin/Components/ui/ConfirmModal.vue'
import StatusBadge from '@admin/Components/ui/StatusBadge.vue'
import { usePermissions } from '@admin/Composables/usePermissions'
import type { Paginator } from '@admin/Types/forms'

const props = defineProps<{ items: Paginator }>()

const { can } = usePermissions()
const deleting = ref<number | null>(null)

function confirmDelete(): void {
    if (deleting.value === null) return

    router.delete(route('admin.users.destroy', deleting.value), {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    })
}

function formatDate(value: string | null): string {
    return value ? new Date(value).toLocaleString('ru-RU') : ''
}
</script>

<template>
  <Head :title="$t('users.title')" />

  <AdminLayout>
    <PageHeader :title="$t('users.title')" :subtitle="$t('table.total', { count: props.items.total })">
      <template #actions>
        <Link v-if="can('users.create')" :href="route('admin.users.create')" class="btn btn-primary">
          <Plus :size="16" />
          {{ $t('action.create') }}
        </Link>
      </template>
    </PageHeader>

    <div class="panel overflow-hidden">
      <div class="overflow-x-auto">
        <table class="table-admin">
          <thead>
            <tr>
              <th>{{ $t('requests.name') }}</th>
              <th>E-mail</th>
              <th>{{ $t('users.roles') }}</th>
              <th>{{ $t('users.last_login') }}</th>
              <th>{{ $t('table.status') }}</th>
              <th class="w-24 text-right">{{ $t('table.actions') }}</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in props.items.data" :key="item.id">
              <td class="font-medium text-steel-800">
                {{ item.name }}
                <span v-if="item.position" class="block text-xs font-normal text-steel-400">{{ item.position }}</span>
              </td>
              <td class="text-steel-500">{{ item.email }}</td>
              <td>
                <span v-for="role in item.roles ?? []" :key="role.id ?? role" class="badge mr-1 bg-steel-100 text-steel-600">
                  {{ role.name ?? role }}
                </span>
              </td>
              <td class="whitespace-nowrap text-steel-500">{{ formatDate(item.last_login_at) || $t('users.never') }}</td>
              <td>
                <StatusBadge
                  :tone="item.is_active ? 'success' : 'muted'"
                  :label="item.is_active ? $t('status.active') : $t('status.inactive')"
                />
              </td>
              <td class="text-right">
                <div class="flex justify-end gap-1">
                  <Link
                    :href="route('admin.users.edit', item.id)"
                    class="btn btn-ghost h-9 w-9 p-0"
                    :aria-label="$t('action.edit')"
                  >
                    <Pencil :size="15" />
                  </Link>

                  <button
                    v-if="can('users.delete')"
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
    </div>

    <ConfirmModal :open="deleting !== null" @confirm="confirmDelete" @cancel="deleting = null" />
  </AdminLayout>
</template>
