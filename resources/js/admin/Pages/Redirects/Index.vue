<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { Plus, Trash2, Save } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import ConfirmModal from '@admin/Components/ui/ConfirmModal.vue'
import { usePermissions } from '@admin/Composables/usePermissions'
import type { Paginator } from '@admin/Types/forms'

const props = defineProps<{ items: Paginator }>()

const { can } = usePermissions()
const deleting = ref<number | null>(null)

const form = useForm({
    from_path: '',
    to_path: '',
    status_code: 301,
    is_active: true,
})

function submit(): void {
    form.post(route('admin.redirects.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}

function confirmDelete(): void {
    if (deleting.value === null) return

    router.delete(route('admin.redirects.destroy', deleting.value), {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    })
}
</script>

<template>
  <Head :title="$t('redirects.title')" />

  <AdminLayout>
    <PageHeader :title="$t('redirects.title')" :subtitle="$t('table.total', { count: props.items.total })" />

    <form v-if="can('redirects.create')" class="panel mb-5 p-5" @submit.prevent="submit">
      <div class="grid gap-4 md:grid-cols-[2fr_2fr_auto_auto]">
        <div>
          <label class="label" for="redirect-from">{{ $t('redirects.from') }}</label>
          <input
            id="redirect-from"
            v-model="form.from_path"
            type="text"
            required
            placeholder="/old-page"
            class="field"
            :class="{ 'field-invalid': form.errors.from_path }"
          >
          <p v-if="form.errors.from_path" class="error-text">{{ form.errors.from_path }}</p>
        </div>

        <div>
          <label class="label" for="redirect-to">{{ $t('redirects.to') }}</label>
          <input id="redirect-to" v-model="form.to_path" type="text" required placeholder="/ro/catalog" class="field">
        </div>

        <div>
          <label class="label" for="redirect-code">{{ $t('redirects.code') }}</label>
          <select id="redirect-code" v-model.number="form.status_code" class="field w-28">
            <option :value="301">301</option>
            <option :value="302">302</option>
            <option :value="307">307</option>
            <option :value="308">308</option>
          </select>
        </div>

        <div class="flex items-end">
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <Plus :size="16" />
            {{ $t('action.add') }}
          </button>
        </div>
      </div>
    </form>

    <div class="panel overflow-hidden">
      <div v-if="props.items.data.length" class="overflow-x-auto">
        <table class="table-admin">
          <thead>
            <tr>
              <th>{{ $t('redirects.from') }}</th>
              <th>{{ $t('redirects.to') }}</th>
              <th class="w-24">{{ $t('redirects.code') }}</th>
              <th class="w-28">{{ $t('redirects.hits') }}</th>
              <th class="w-16 text-right">{{ $t('table.actions') }}</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in props.items.data" :key="item.id">
              <td><code class="text-xs text-steel-600">{{ item.from_path }}</code></td>
              <td><code class="text-xs text-steel-600">{{ item.to_path }}</code></td>
              <td>{{ item.status_code }}</td>
              <td class="text-steel-500">{{ item.hits_count }}</td>
              <td class="text-right">
                <button
                  v-if="can('redirects.delete')"
                  type="button"
                  class="btn btn-ghost h-9 w-9 p-0 text-[color:var(--color-danger)]"
                  :aria-label="$t('action.delete')"
                  @click="deleting = item.id"
                >
                  <Trash2 :size="15" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-else class="px-6 py-14 text-center text-sm text-steel-400">{{ $t('table.empty') }}</p>
    </div>

    <ConfirmModal :open="deleting !== null" @confirm="confirmDelete" @cancel="deleting = null" />
  </AdminLayout>
</template>
