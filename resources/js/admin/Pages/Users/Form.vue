<script setup lang="ts">
import { computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Save } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'

const props = defineProps<{
    item?: Record<string, any> | null
    roles: string[]
    localeCodes: string[]
}>()

const isEdit = computed(() => Boolean(props.item?.id))

const form = useForm({
    name: props.item?.name ?? '',
    email: props.item?.email ?? '',
    phone: props.item?.phone ?? '',
    position: props.item?.position ?? '',
    locale: props.item?.locale ?? 'ru',
    is_active: props.item?.is_active ?? true,
    password: '',
    password_confirmation: '',
    roles: (props.item?.roles ?? []) as string[],
})

function submit(): void {
    const target = isEdit.value ? route('admin.users.update', props.item!.id) : route('admin.users.store')

    form.post(target, { preserveScroll: true, onSuccess: () => form.reset('password', 'password_confirmation') })
}
</script>

<template>
  <Head :title="$t('users.title')" />

  <AdminLayout>
    <form @submit.prevent="submit">
      <PageHeader
        :title="isEdit ? `${$t('users.title')} — ${form.name}` : $t('action.create')"
        :back-url="route('admin.users.index')"
      >
        <template #actions>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <Save :size="15" />
            {{ form.processing ? $t('action.saving') : $t('action.save') }}
          </button>
        </template>
      </PageHeader>

      <div class="grid gap-5 lg:grid-cols-3">
        <section class="panel space-y-5 p-5 lg:col-span-2">
          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="label" for="user-name">{{ $t('requests.name') }} *</label>
              <input id="user-name" v-model="form.name" type="text" required class="field" :class="{ 'field-invalid': form.errors.name }">
              <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
            </div>

            <div>
              <label class="label" for="user-email">E-mail *</label>
              <input id="user-email" v-model="form.email" type="email" required class="field" :class="{ 'field-invalid': form.errors.email }">
              <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
            </div>

            <div>
              <label class="label" for="user-phone">{{ $t('requests.name') }}</label>
              <input id="user-phone" v-model="form.phone" type="tel" class="field">
            </div>

            <div>
              <label class="label" for="user-position">{{ $t('profile.title') }}</label>
              <input id="user-position" v-model="form.position" type="text" class="field">
            </div>

            <div>
              <label class="label" for="user-locale">{{ $t('profile.locale') }}</label>
              <select id="user-locale" v-model="form.locale" class="field">
                <option v-for="locale in props.localeCodes" :key="locale" :value="locale">{{ locale }}</option>
              </select>
            </div>

            <label class="flex items-start gap-2.5 pt-7">
              <input v-model="form.is_active" type="checkbox" class="mt-0.5 accent-[#162456]">
              <span class="text-sm font-medium text-steel-700">{{ $t('status.active') }}</span>
            </label>
          </div>

          <div class="grid gap-5 border-t border-steel-200 pt-5 md:grid-cols-2">
            <div>
              <label class="label" for="user-password">{{ $t('users.password') }}</label>
              <input
                id="user-password"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                class="field"
                :class="{ 'field-invalid': form.errors.password }"
              >
              <p class="hint">{{ isEdit ? $t('users.password_hint') : '' }}</p>
              <p v-if="form.errors.password" class="error-text">{{ form.errors.password }}</p>
            </div>

            <div>
              <label class="label" for="user-password-confirm">{{ $t('users.password_confirm') }}</label>
              <input
                id="user-password-confirm"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                class="field"
              >
            </div>
          </div>
        </section>

        <aside class="panel h-fit p-5">
          <h2 class="text-sm font-semibold text-steel-800">{{ $t('users.roles') }}</h2>

          <div class="mt-4 space-y-2.5">
            <label v-for="role in props.roles" :key="role" class="flex items-center gap-2.5 text-sm text-steel-700">
              <input v-model="form.roles" type="checkbox" :value="role" class="accent-[#162456]">
              {{ role }}
            </label>
          </div>
        </aside>
      </div>
    </form>
  </AdminLayout>
</template>
