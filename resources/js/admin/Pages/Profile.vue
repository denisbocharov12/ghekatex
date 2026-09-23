<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Save, User } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'

const props = defineProps<{ item: Record<string, any>; localeCodes: string[] }>()

const preview = ref<string | null>(null)

const form = useForm({
    name: props.item.name ?? '',
    email: props.item.email ?? '',
    phone: props.item.phone ?? '',
    position: props.item.position ?? '',
    locale: props.item.locale ?? 'ru',
    password: '',
    password_confirmation: '',
    avatar: null as File | null,
})

function pickAvatar(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null

    form.avatar = file
    preview.value = file ? URL.createObjectURL(file) : null
}

function submit(): void {
    // PATCH с файлом браузер не отправит, поэтому подменяем метод полем формы
    form
        .transform((data) => ({ ...data, _method: 'patch' }))
        .post(route('admin.profile.update'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => form.reset('password', 'password_confirmation'),
        })
}
</script>

<template>
  <Head :title="$t('profile.title')" />

  <AdminLayout>
    <form @submit.prevent="submit">
      <PageHeader :title="$t('profile.title')">
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
              <label class="label" for="profile-name">{{ $t('requests.name') }} *</label>
              <input id="profile-name" v-model="form.name" type="text" required class="field">
              <p v-if="form.errors.name" class="error-text">{{ form.errors.name }}</p>
            </div>

            <div>
              <label class="label" for="profile-email">E-mail *</label>
              <input id="profile-email" v-model="form.email" type="email" required class="field">
              <p v-if="form.errors.email" class="error-text">{{ form.errors.email }}</p>
            </div>

            <div>
              <label class="label" for="profile-phone">Телефон</label>
              <input id="profile-phone" v-model="form.phone" type="tel" class="field">
            </div>

            <div>
              <label class="label" for="profile-position">Должность</label>
              <input id="profile-position" v-model="form.position" type="text" class="field">
            </div>

            <div>
              <label class="label" for="profile-locale">{{ $t('profile.locale') }}</label>
              <select id="profile-locale" v-model="form.locale" class="field">
                <option v-for="locale in props.localeCodes" :key="locale" :value="locale">{{ locale }}</option>
              </select>
            </div>
          </div>

          <div class="grid gap-5 border-t border-steel-200 pt-5 md:grid-cols-2">
            <div>
              <label class="label" for="profile-password">{{ $t('users.password') }}</label>
              <input id="profile-password" v-model="form.password" type="password" autocomplete="new-password" class="field">
              <p class="hint">{{ $t('users.password_hint') }}</p>
              <p v-if="form.errors.password" class="error-text">{{ form.errors.password }}</p>
            </div>

            <div>
              <label class="label" for="profile-password-confirm">{{ $t('users.password_confirm') }}</label>
              <input
                id="profile-password-confirm"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                class="field"
              >
            </div>
          </div>
        </section>

        <aside class="panel h-fit p-5">
          <span class="label">{{ $t('profile.avatar') }}</span>

          <div class="flex items-center gap-4">
            <span class="inline-flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-primary-100 text-primary-800">
              <img v-if="preview || props.item.avatar" :src="preview || props.item.avatar" alt="" class="h-full w-full object-cover">
              <User v-else :size="22" />
            </span>

            <input type="file" accept="image/*" class="text-sm" @change="pickAvatar">
          </div>
        </aside>
      </div>
    </form>
  </AdminLayout>
</template>
