<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { Save, Trash2, ExternalLink } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'
import ConfirmModal from '@admin/Components/ui/ConfirmModal.vue'
import LocaleTabs from '@admin/Components/forms/LocaleTabs.vue'
import RichTextEditor from '@admin/Components/forms/RichTextEditor.vue'
import MediaUploader from '@admin/Components/forms/MediaUploader.vue'
import GalleryUploader from '@admin/Components/forms/GalleryUploader.vue'
import RepeatableRows from '@admin/Components/forms/RepeatableRows.vue'
import SeoFields from '@admin/Components/forms/SeoFields.vue'
import { usePermissions } from '@admin/Composables/usePermissions'
import type { FormField } from '@admin/Types/forms'

/**
 * Форма раздела админки.
 *
 * Поля описываются страницей раздела, а работа с локалями, файлами,
 * ошибками и отправкой одинакова везде и живёт здесь.
 */
const props = withDefaults(
    defineProps<{
        title: string
        routePrefix: string
        permission: string
        fields: FormField[]
        item?: Record<string, any> | null
        withSeo?: boolean
        previewUrl?: string | null
    }>(),
    { item: null, withSeo: false, previewUrl: null },
)

const page = usePage()
const { can } = usePermissions()

const locales = computed(() => (page.props.locales as { available: string[]; default: string }))
const isEdit = computed(() => Boolean(props.item?.id))

/** Начальное значение поля: из записи, иначе пустое нужного типа. */
function initial(field: FormField): unknown {
    const stored = props.item?.[field.key]

    if (field.translated) {
        const source = (stored ?? {}) as Record<string, string>

        return Object.fromEntries(locales.value.available.map((code) => [code, source[code] ?? '']))
    }

    switch (field.type) {
        case 'checkbox':
            return stored ?? false
        case 'media':
            return null
        case 'gallery':
            return []
        case 'rows':
        case 'multiselect':
        case 'list':
            return Array.isArray(stored) ? stored : []
        case 'number':
            return stored ?? null
        default:
            return stored ?? ''
    }
}

const initialValues: Record<string, unknown> = {}

for (const field of props.fields) {
    initialValues[field.key] = initial(field)
}

if (props.withSeo) {
    initialValues.seo = props.item?.seo ?? {}
}

const form = useForm(initialValues)

const deleting = ref(false)

// Загрузка файлов требует multipart, а Laravel не читает PUT с файлами —
// поэтому обновление тоже отправляется методом POST
function submit(): void {
    const target = isEdit.value
        ? route(`admin.${props.routePrefix}.update`, props.item!.id)
        : route(`admin.${props.routePrefix}.store`)

    form.post(target, { forceFormData: true, preserveScroll: true })
}

function destroy(): void {
    router.delete(route(`admin.${props.routePrefix}.destroy`, props.item!.id))
}

const sections = reactive({
    media: props.fields.some((field) => field.type === 'media' || field.type === 'gallery'),
})

const mainFields = computed(() =>
    props.fields.filter((field) => !['media', 'gallery', 'html', 'rows'].includes(field.type)),
)
const contentFields = computed(() => props.fields.filter((field) => field.type === 'html' || field.type === 'rows'))
const mediaFields = computed(() => props.fields.filter((field) => field.type === 'media' || field.type === 'gallery'))

function errorFor(field: FormField): string {
    const errors = form.errors as Record<string, string>

    return field.translated ? errors[`${field.key}.${locales.value.default}`] ?? '' : errors[field.key] ?? ''
}
</script>

<template>
  <Head :title="props.title" />

  <AdminLayout>
    <form @submit.prevent="submit">
      <PageHeader :title="props.title" :back-url="route(`admin.${props.routePrefix}.index`)">
        <template #actions>
          <a v-if="props.previewUrl" :href="props.previewUrl" target="_blank" rel="noopener" class="btn btn-secondary">
            <ExternalLink :size="15" />
            {{ $t('action.open') }}
          </a>

          <button
            v-if="isEdit && can(`${props.permission}.delete`) && !props.item?.is_system"
            type="button"
            class="btn btn-secondary text-[color:var(--color-danger)]"
            @click="deleting = true"
          >
            <Trash2 :size="15" />
            {{ $t('action.delete') }}
          </button>

          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <Save :size="15" />
            {{ form.processing ? $t('action.saving') : $t('action.save') }}
          </button>
        </template>
      </PageHeader>

      <div class="grid gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
          <section class="panel p-5">
            <h2 class="mb-5 text-sm font-semibold text-steel-800">{{ $t('form.main') }}</h2>

            <div class="grid gap-5 md:grid-cols-2">
              <div v-for="field in mainFields" :key="field.key" :class="field.half ? '' : 'md:col-span-2'">
                <LocaleTabs
                  v-if="field.translated"
                  v-model="form[field.key]"
                  :label="field.label"
                  :required="field.required"
                  :error="errorFor(field)"
                >
                  <template #default="{ value, update }">
                    <textarea
                      v-if="field.type === 'textarea'"
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

                <template v-else-if="field.type === 'checkbox'">
                  <label class="flex cursor-pointer items-start gap-2.5 pt-6">
                    <input v-model="form[field.key]" type="checkbox" class="mt-0.5 accent-[#162456]">
                    <span>
                      <span class="block text-sm font-medium text-steel-700">{{ field.label }}</span>
                      <span v-if="field.hint" class="hint">{{ field.hint }}</span>
                    </span>
                  </label>
                </template>

                <template v-else>
                  <label class="label" :for="`field-${field.key}`">
                    {{ field.label }}<span v-if="field.required" class="text-[color:var(--color-danger)]"> *</span>
                  </label>

                  <select
                    v-if="field.type === 'select'"
                    :id="`field-${field.key}`"
                    v-model="form[field.key]"
                    class="field"
                    :class="{ 'field-invalid': errorFor(field) }"
                  >
                    <option value="">—</option>
                    <option v-for="option in field.options ?? []" :key="option.value" :value="option.value">
                      {{ option.label }}
                    </option>
                  </select>

                  <select
                    v-else-if="field.type === 'multiselect'"
                    :id="`field-${field.key}`"
                    v-model="form[field.key]"
                    multiple
                    class="field h-auto min-h-32 py-2"
                  >
                    <option v-for="option in field.options ?? []" :key="option.value" :value="option.value">
                      {{ option.label }}
                    </option>
                  </select>

                  <textarea
                    v-else-if="field.type === 'textarea'"
                    :id="`field-${field.key}`"
                    v-model="form[field.key]"
                    rows="3"
                    class="field h-auto py-2"
                  />

                  <div v-else-if="field.type === 'list'" class="space-y-2">
                    <div v-for="(entry, index) in (form[field.key] as string[])" :key="index" class="flex gap-2">
                      <input
                        :value="entry"
                        type="text"
                        class="field"
                        @input="(form[field.key] as string[])[index] = ($event.target as HTMLInputElement).value"
                      >
                      <button
                        type="button"
                        class="btn btn-secondary h-[2.625rem] w-11 p-0 text-[color:var(--color-danger)]"
                        :aria-label="$t('action.remove')"
                        @click="form[field.key] = (form[field.key] as string[]).filter((_, i) => i !== index)"
                      >
                        <Trash2 :size="15" />
                      </button>
                    </div>

                    <button
                      type="button"
                      class="btn btn-secondary h-9 text-xs"
                      @click="form[field.key] = [...(form[field.key] as string[]), '']"
                    >
                      {{ $t('action.add') }}
                    </button>
                  </div>

                  <input
                    v-else
                    :id="`field-${field.key}`"
                    v-model="form[field.key]"
                    :type="field.type === 'number' ? 'number' : field.type === 'date' ? 'date' : field.type === 'color' ? 'color' : 'text'"
                    class="field"
                    :class="{ 'field-invalid': errorFor(field) }"
                  >

                  <p v-if="field.hint" class="hint">{{ field.hint }}</p>
                  <p v-if="errorFor(field)" class="error-text">{{ errorFor(field) }}</p>
                </template>
              </div>
            </div>
          </section>

          <section v-if="contentFields.length" class="panel space-y-6 p-5">
            <h2 class="text-sm font-semibold text-steel-800">{{ $t('form.content') }}</h2>

            <template v-for="field in contentFields" :key="field.key">
              <LocaleTabs
                v-if="field.type === 'html'"
                v-model="form[field.key]"
                :label="field.label"
                :required="field.required"
              >
                <template #default="{ value, update }">
                  <RichTextEditor :model-value="value" @update:model-value="update" />
                </template>
              </LocaleTabs>

              <RepeatableRows
                v-else
                v-model="form[field.key]"
                :label="field.label"
                :fields="field.rowFields ?? []"
                :hint="field.hint"
              />
            </template>
          </section>

          <SeoFields v-if="props.withSeo" v-model="form.seo" />
        </div>

        <aside v-if="sections.media" class="space-y-5">
          <section class="panel space-y-6 p-5">
            <h2 class="text-sm font-semibold text-steel-800">{{ $t('form.media') }}</h2>

            <template v-for="field in mediaFields" :key="field.key">
              <MediaUploader
                v-if="field.type === 'media'"
                v-model="form[field.key]"
                :label="field.label"
                :accept="field.accept"
                :hint="field.hint"
                :current-url="props.item?.[`${field.key}_url`] ?? null"
                :error="errorFor(field)"
              />

              <GalleryUploader
                v-else
                v-model="form[field.key]"
                :existing="props.item?.gallery ?? []"
                :route-prefix="props.routePrefix"
                :item-id="props.item?.id ?? null"
              />
            </template>
          </section>
        </aside>
      </div>
    </form>

    <ConfirmModal :open="deleting" @confirm="destroy" @cancel="deleting = false" />
  </AdminLayout>
</template>
