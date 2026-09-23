<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Save } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import LocaleTabs from '@admin/Components/forms/LocaleTabs.vue'
import RichTextEditor from '@admin/Components/forms/RichTextEditor.vue'

interface Setting {
    id: number
    key: string
    group: string
    type: string
    label: string | null
    hint: string | null
    is_translatable: boolean
    value: Record<string, unknown>
    file_url: string | null
}

const props = defineProps<{
    settings: Setting[]
    groups: { value: string; label: string }[]
    localeCodes: string[]
}>()

const activeGroup = ref(props.groups[0]?.value ?? 'general')

const visible = computed(() => props.settings.filter((setting) => setting.group === activeGroup.value))

/** Форма держит значения всех групп сразу: переключение вкладок ничего не теряет. */
const values: Record<string, unknown> = {}

for (const setting of props.settings) {
    values[setting.key] = setting.is_translatable
        ? Object.fromEntries(props.localeCodes.map((code) => [code, (setting.value as Record<string, string>)?.[code] ?? '']))
        : (setting.value as { value?: unknown })?.value ?? (setting.type === 'bool' ? false : '')
}

const form = useForm<{ values: Record<string, unknown>; files: Record<string, File | null> }>({
    values,
    files: {},
})

function pickFile(key: string, event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null

    form.files = { ...form.files, [key]: file }
}

function submit(): void {
    form.post(route('admin.settings.update'), { forceFormData: true, preserveScroll: true })
}
</script>

<template>
  <Head :title="$t('settings.title')" />

  <AdminLayout>
    <form @submit.prevent="submit">
      <PageHeader :title="$t('settings.title')">
        <template #actions>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <Save :size="15" />
            {{ form.processing ? $t('action.saving') : $t('action.save') }}
          </button>
        </template>
      </PageHeader>

      <div class="grid gap-5 lg:grid-cols-4">
        <nav class="panel h-fit overflow-hidden lg:col-span-1" :aria-label="$t('settings.title')">
          <ul>
            <li v-for="group in props.groups" :key="group.value">
              <button
                type="button"
                :class="[
                  'w-full border-l-2 px-4 py-3 text-left text-sm transition-colors',
                  activeGroup === group.value
                    ? 'border-primary-900 bg-steel-50 font-semibold text-primary-900'
                    : 'border-transparent text-steel-600 hover:bg-steel-50',
                ]"
                @click="activeGroup = group.value"
              >
                {{ group.label }}
              </button>
            </li>
          </ul>
        </nav>

        <section class="panel space-y-6 p-5 lg:col-span-3">
          <div v-for="setting in visible" :key="setting.key">
            <LocaleTabs
              v-if="setting.is_translatable"
              v-model="(form.values[setting.key] as Record<string, string>)"
              :label="setting.label || setting.key"
            >
              <template #default="{ value, update }">
                <RichTextEditor v-if="setting.type === 'html'" :model-value="value" @update:model-value="update" />
                <textarea
                  v-else-if="setting.type === 'text'"
                  :value="value"
                  rows="3"
                  class="field h-auto py-2"
                  @input="update(($event.target as HTMLTextAreaElement).value)"
                />
                <input
                  v-else
                  :value="value"
                  type="text"
                  class="field"
                  @input="update(($event.target as HTMLInputElement).value)"
                >
              </template>
            </LocaleTabs>

            <template v-else>
              <label v-if="setting.type !== 'bool'" class="label" :for="`setting-${setting.key}`">
                {{ setting.label || setting.key }}
              </label>

              <label v-if="setting.type === 'bool'" class="flex items-start gap-2.5">
                <input v-model="form.values[setting.key]" type="checkbox" class="mt-0.5 accent-[#162456]">
                <span class="text-sm font-medium text-steel-700">{{ setting.label || setting.key }}</span>
              </label>

              <div v-else-if="setting.type === 'image'" class="flex items-center gap-4">
                <img
                  v-if="setting.file_url"
                  :src="setting.file_url"
                  :alt="setting.label || setting.key"
                  class="h-16 w-24 rounded border border-steel-200 object-cover"
                >
                <input
                  :id="`setting-${setting.key}`"
                  type="file"
                  accept="image/*"
                  class="text-sm"
                  @change="pickFile(setting.key, $event)"
                >
              </div>

              <textarea
                v-else-if="setting.type === 'text'"
                :id="`setting-${setting.key}`"
                v-model="form.values[setting.key]"
                rows="3"
                class="field h-auto py-2"
              />

              <input
                v-else
                :id="`setting-${setting.key}`"
                v-model="form.values[setting.key]"
                :type="setting.type === 'number' ? 'number' : 'text'"
                class="field"
              >

              <p v-if="setting.hint" class="hint">{{ setting.hint }}</p>
            </template>
          </div>
        </section>
      </div>
    </form>
  </AdminLayout>
</template>
