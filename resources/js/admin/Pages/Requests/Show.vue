<script setup lang="ts">
import { ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { Mail, Phone, Building2, Globe, Save, Trash2 } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import ConfirmModal from '@admin/Components/ui/ConfirmModal.vue'

const props = defineProps<{
    item: Record<string, any>
    statuses: { value: string; label: string }[]
}>()

const form = useForm({
    status: props.item.status,
    admin_note: props.item.admin_note ?? '',
})

const deleting = ref(false)

function submit(): void {
    form.patch(route('admin.requests.update', props.item.id), { preserveScroll: true })
}

function destroy(): void {
    router.delete(route('admin.requests.destroy', props.item.id))
}

function formatDate(value: string | null): string {
    return value ? new Date(value).toLocaleString('ru-RU') : '—'
}
</script>

<template>
  <Head :title="`${$t('requests.title')} #${props.item.id}`" />

  <AdminLayout>
    <PageHeader
      :title="`${$t('requests.title')} #${props.item.id}`"
      :subtitle="formatDate(props.item.created_at)"
      :back-url="route('admin.requests.index')"
    >
      <template #actions>
        <a :href="`mailto:${props.item.email}`" class="btn btn-secondary">
          <Mail :size="15" />
          {{ $t('requests.reply') }}
        </a>

        <button type="button" class="btn btn-secondary text-[color:var(--color-danger)]" @click="deleting = true">
          <Trash2 :size="15" />
          {{ $t('action.delete') }}
        </button>
      </template>
    </PageHeader>

    <div class="grid gap-5 lg:grid-cols-3">
      <section class="panel p-6 lg:col-span-2">
        <h2 class="text-sm font-semibold text-steel-800">{{ $t('requests.message') }}</h2>
        <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-steel-700">{{ props.item.message }}</p>

        <dl class="mt-8 grid gap-5 border-t border-steel-200 pt-6 sm:grid-cols-2">
          <div>
            <dt class="text-xs uppercase tracking-wider text-steel-400">{{ $t('requests.name') }}</dt>
            <dd class="mt-1 text-sm font-medium text-steel-800">{{ props.item.name }}</dd>
          </div>

          <div>
            <dt class="text-xs uppercase tracking-wider text-steel-400">E-mail</dt>
            <dd class="mt-1 flex items-center gap-2 text-sm">
              <Mail :size="14" class="text-steel-400" />
              <a :href="`mailto:${props.item.email}`" class="text-primary-700 hover:underline">{{ props.item.email }}</a>
            </dd>
          </div>

          <div v-if="props.item.phone">
            <dt class="text-xs uppercase tracking-wider text-steel-400">{{ $t('requests.name') }}</dt>
            <dd class="mt-1 flex items-center gap-2 text-sm">
              <Phone :size="14" class="text-steel-400" />
              <a :href="`tel:${props.item.phone}`" class="text-primary-700 hover:underline">{{ props.item.phone }}</a>
            </dd>
          </div>

          <div v-if="props.item.company">
            <dt class="text-xs uppercase tracking-wider text-steel-400">{{ $t('requests.company') }}</dt>
            <dd class="mt-1 flex items-center gap-2 text-sm text-steel-800">
              <Building2 :size="14" class="text-steel-400" />
              {{ props.item.company }}
            </dd>
          </div>

          <div v-if="props.item.country">
            <dt class="text-xs uppercase tracking-wider text-steel-400">{{ $t('requests.source') }}</dt>
            <dd class="mt-1 flex items-center gap-2 text-sm text-steel-800">
              <Globe :size="14" class="text-steel-400" />
              {{ props.item.country }}
            </dd>
          </div>

          <div>
            <dt class="text-xs uppercase tracking-wider text-steel-400">{{ $t('requests.source') }}</dt>
            <dd class="mt-1 text-sm text-steel-800">{{ props.item.source }} · {{ props.item.locale }}</dd>
          </div>
        </dl>
      </section>

      <aside class="panel h-fit p-6">
        <form class="space-y-5" @submit.prevent="submit">
          <div>
            <label class="label" for="request-status">{{ $t('requests.status') }}</label>
            <select id="request-status" v-model="form.status" class="field">
              <option v-for="status in props.statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
            </select>
          </div>

          <div>
            <label class="label" for="request-note">{{ $t('requests.note') }}</label>
            <textarea id="request-note" v-model="form.admin_note" rows="5" class="field h-auto py-2" />
          </div>

          <p v-if="props.item.handler" class="hint">
            {{ $t('requests.handled_by') }}: {{ props.item.handler.name }} · {{ formatDate(props.item.handled_at) }}
          </p>

          <button type="submit" class="btn btn-primary w-full" :disabled="form.processing">
            <Save :size="15" />
            {{ $t('action.save') }}
          </button>
        </form>
      </aside>
    </div>

    <ConfirmModal :open="deleting" @confirm="destroy" @cancel="deleting = false" />
  </AdminLayout>
</template>
