<script setup lang="ts">
import { ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { Plus, Save, Trash2, Lock } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import ConfirmModal from '@admin/Components/ui/ConfirmModal.vue'
import { usePermissions } from '@admin/Composables/usePermissions'

interface Role {
    id: number
    name: string
    system: boolean
    permissions: string[]
}

const props = defineProps<{
    roles: Role[]
    areas: { area: string; label: string; permissions: string[] }[]
}>()

const { can } = usePermissions()

const selected = ref<Role | null>(props.roles[0] ?? null)
const creating = ref(false)
const deleting = ref<number | null>(null)

const form = useForm({
    name: '',
    permissions: [] as string[],
})

function select(role: Role): void {
    selected.value = role
    creating.value = false
    form.name = role.name
    form.permissions = [...role.permissions]
}

function startCreate(): void {
    creating.value = true
    selected.value = null
    form.reset()
    form.permissions = []
}

function submit(): void {
    if (creating.value) {
        form.post(route('admin.roles.store'), { preserveScroll: true, onSuccess: () => (creating.value = false) })

        return
    }

    if (selected.value) {
        form.patch(route('admin.roles.update', selected.value.id), { preserveScroll: true })
    }
}

function confirmDelete(): void {
    if (deleting.value === null) return

    router.delete(route('admin.roles.destroy', deleting.value), {
        preserveScroll: true,
        onFinish: () => (deleting.value = null),
    })
}

/** Отметить или снять всю область целиком — так быстрее настраивать роль. */
function toggleArea(permissions: string[]): void {
    const allChecked = permissions.every((permission) => form.permissions.includes(permission))

    form.permissions = allChecked
        ? form.permissions.filter((permission) => !permissions.includes(permission))
        : Array.from(new Set([...form.permissions, ...permissions]))
}

if (selected.value) {
    select(selected.value)
}
</script>

<template>
  <Head :title="$t('roles.title')" />

  <AdminLayout>
    <PageHeader :title="$t('roles.title')" :subtitle="$t('roles.system_hint')">
      <template #actions>
        <button v-if="can('roles.create')" type="button" class="btn btn-primary" @click="startCreate">
          <Plus :size="16" />
          {{ $t('action.create') }}
        </button>
      </template>
    </PageHeader>

    <div class="grid gap-5 lg:grid-cols-4">
      <nav class="panel h-fit overflow-hidden lg:col-span-1">
        <ul>
          <li v-for="role in props.roles" :key="role.id">
            <div class="flex items-center">
              <button
                type="button"
                :class="[
                  'flex-1 border-l-2 px-4 py-3 text-left text-sm transition-colors',
                  selected?.id === role.id
                    ? 'border-primary-900 bg-steel-50 font-semibold text-primary-900'
                    : 'border-transparent text-steel-600 hover:bg-steel-50',
                ]"
                @click="select(role)"
              >
                {{ role.name }}
                <Lock v-if="role.system" :size="12" class="ml-1 inline text-steel-400" />
              </button>

              <button
                v-if="!role.system && can('roles.delete')"
                type="button"
                class="btn btn-ghost h-9 w-9 shrink-0 p-0 text-[color:var(--color-danger)]"
                :aria-label="$t('action.delete')"
                @click="deleting = role.id"
              >
                <Trash2 :size="14" />
              </button>
            </div>
          </li>
        </ul>
      </nav>

      <section v-if="selected || creating" class="panel p-5 lg:col-span-3">
        <form @submit.prevent="submit">
          <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-64 flex-1">
              <label class="label" for="role-name">{{ $t('roles.name') }}</label>
              <input
                id="role-name"
                v-model="form.name"
                type="text"
                class="field"
                :disabled="selected?.system"
                :class="{ 'field-invalid': form.errors.name }"
              >
              <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
            </div>

            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              <Save :size="15" />
              {{ $t('action.save') }}
            </button>
          </div>

          <div class="mt-7 space-y-5">
            <div v-for="area in props.areas" :key="area.area" class="rounded-md border border-steel-200 p-4">
              <div class="mb-3 flex items-center justify-between gap-3">
                <h3 class="text-sm font-semibold text-steel-800">{{ area.label }}</h3>

                <button type="button" class="text-xs text-primary-700 hover:underline" @click="toggleArea(area.permissions)">
                  {{ $t('action.apply') }}
                </button>
              </div>

              <div class="flex flex-wrap gap-x-6 gap-y-2">
                <label
                  v-for="permission in area.permissions"
                  :key="permission"
                  class="flex items-center gap-2 text-sm text-steel-600"
                >
                  <input v-model="form.permissions" type="checkbox" :value="permission" class="accent-[#162456]">
                  {{ permission.split('.')[1] }}
                </label>
              </div>
            </div>
          </div>
        </form>
      </section>
    </div>

    <ConfirmModal :open="deleting !== null" @confirm="confirmDelete" @cancel="deleting = null" />
  </AdminLayout>
</template>
