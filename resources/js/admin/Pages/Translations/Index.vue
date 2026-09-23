<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { useDebounceFn } from '@vueuse/core'
import { Search, Check } from 'lucide-vue-next'
import AdminLayout from '@admin/Layouts/AdminLayout.vue'
import PageHeader from '@admin/Components/ui/PageHeader.vue'

interface Row {
    key: string
    source: string
    values: Record<string, string>
    modified: boolean
}

const props = defineProps<{
    group: string
    groups: string[]
    rows: Row[]
    localeCodes: string[]
    localeLabels: Record<string, string>
}>()

const search = ref('')
const onlyModified = ref(false)

/** Локальные правки, ещё не отправленные на сервер. */
const drafts = ref<Record<string, Record<string, string>>>({})
const saved = ref<Record<string, boolean>>({})

const filtered = computed(() => {
    const term = search.value.trim().toLowerCase()

    return props.rows.filter((row) => {
        if (onlyModified.value && !row.modified) return false
        if (!term) return true

        return row.key.toLowerCase().includes(term) || row.source.toLowerCase().includes(term)
    })
})

function valueOf(row: Row, locale: string): string {
    return drafts.value[row.key]?.[locale] ?? row.values[locale] ?? ''
}

const persist = useDebounceFn((key: string) => {
    router.post(
        route('admin.translations.update'),
        { group: props.group, key, values: drafts.value[key] ?? {} },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                saved.value = { ...saved.value, [key]: true }
                window.setTimeout(() => (saved.value = { ...saved.value, [key]: false }), 1800)
            },
        },
    )
}, 700)

function update(row: Row, locale: string, value: string): void {
    const current = drafts.value[row.key] ?? { ...row.values }

    drafts.value = { ...drafts.value, [row.key]: { ...current, [locale]: value } }

    persist(row.key)
}

function switchGroup(group: string): void {
    router.get(route('admin.translations.index'), { group }, { preserveScroll: true })
}

const groupLabels: Record<string, string> = {
    site: 'Витрина',
    admin: 'Панель',
    php: 'Серверные строки',
}
</script>

<template>
  <Head :title="$t('translations.title')" />

  <AdminLayout>
    <PageHeader :title="$t('translations.title')" :subtitle="$t('translations.hint')" />

    <div class="panel overflow-hidden">
      <div class="flex flex-wrap items-center gap-4 border-b border-steel-200 p-4">
        <div class="flex gap-1">
          <button
            v-for="group in props.groups"
            :key="group"
            type="button"
            :class="[
              'rounded px-3 py-1.5 text-sm transition-colors',
              props.group === group ? 'bg-primary-900 text-white' : 'text-steel-600 hover:bg-steel-100',
            ]"
            @click="switchGroup(group)"
          >
            {{ groupLabels[group] ?? group }}
          </button>
        </div>

        <div class="relative max-w-xs flex-1">
          <Search :size="16" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-steel-400" />
          <input v-model="search" type="search" :placeholder="$t('action.search')" class="field pl-9">
        </div>

        <label class="ml-auto flex items-center gap-2 text-sm text-steel-600">
          <input v-model="onlyModified" type="checkbox" class="accent-[#162456]">
          {{ $t('translations.modified') }}
        </label>
      </div>

      <div class="overflow-x-auto">
        <table class="table-admin">
          <thead>
            <tr>
              <th class="w-64">{{ $t('translations.key') }}</th>
              <th class="w-64">{{ $t('translations.source') }}</th>
              <th v-for="locale in props.localeCodes" :key="locale">{{ props.localeLabels[locale] ?? locale }}</th>
              <th class="w-10" />
            </tr>
          </thead>

          <tbody>
            <tr v-for="row in filtered" :key="row.key">
              <td class="align-top">
                <code class="text-xs text-steel-500">{{ row.key }}</code>
              </td>
              <td class="align-top text-steel-500">{{ row.source }}</td>

              <td v-for="locale in props.localeCodes" :key="locale" class="align-top">
                <input
                  :value="valueOf(row, locale)"
                  type="text"
                  class="field"
                  :placeholder="locale === props.localeCodes[0] ? row.source : ''"
                  @input="update(row, locale, ($event.target as HTMLInputElement).value)"
                >
              </td>

              <td class="align-top">
                <Check v-if="saved[row.key]" :size="16" class="mt-2.5 text-[color:var(--color-success)]" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p v-if="!filtered.length" class="px-6 py-14 text-center text-sm text-steel-400">{{ $t('table.empty') }}</p>
    </div>
  </AdminLayout>
</template>
