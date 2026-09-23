<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Plus, Trash2, ChevronUp, ChevronDown } from 'lucide-vue-next'
import type { RowField } from '@admin/Types/forms'

/**
 * Список-структура: шаги процесса, характеристики, ключевые факты.
 *
 * Переводы лежат внутри элементов, поэтому вкладки локалей общие для всей
 * строки — иначе редактор переключал бы язык в каждом поле отдельно.
 */
const props = defineProps<{ modelValue: Record<string, any>[]; label: string; fields: RowField[]; hint?: string }>()
const emit = defineEmits<{ 'update:modelValue': [Record<string, any>[]] }>()

const page = usePage()
const locales = computed(() => page.props.locales as { available: string[]; default: string })

const rows = computed({
    get: () => props.modelValue ?? [],
    set: (next) => emit('update:modelValue', next),
})

function emptyRow(): Record<string, any> {
    const row: Record<string, any> = {}

    for (const field of props.fields) {
        row[field.key] = field.translated
            ? Object.fromEntries(locales.value.available.map((code) => [code, '']))
            : ''
    }

    return row
}

function add(): void {
    rows.value = [...rows.value, emptyRow()]
}

function remove(index: number): void {
    rows.value = rows.value.filter((_, position) => position !== index)
}

function move(index: number, delta: number): void {
    const target = index + delta

    if (target < 0 || target >= rows.value.length) return

    const next = [...rows.value]
    ;[next[index], next[target]] = [next[target], next[index]]
    rows.value = next
}

function update(index: number, field: RowField, locale: string | null, value: string): void {
    const next = [...rows.value]
    const row = { ...next[index] }

    if (field.translated && locale) {
        row[field.key] = { ...(row[field.key] ?? {}), [locale]: value }
    } else {
        row[field.key] = value
    }

    next[index] = row
    rows.value = next
}
</script>

<template>
  <div>
    <div class="mb-2 flex items-center justify-between gap-3">
      <span class="label mb-0">{{ props.label }}</span>
      <button type="button" class="btn btn-secondary h-8 px-2.5 text-xs" @click="add">
        <Plus :size="14" />
        {{ $t('action.add') }}
      </button>
    </div>

    <p v-if="props.hint" class="hint mb-3">{{ props.hint }}</p>

    <div v-if="rows.length" class="space-y-3">
      <div v-for="(row, index) in rows" :key="index" class="rounded-md border border-steel-200 bg-steel-50/60 p-4">
        <div class="mb-3 flex items-center justify-between">
          <span class="text-xs font-semibold text-steel-400">#{{ index + 1 }}</span>

          <div class="flex gap-0.5">
            <button type="button" class="btn btn-ghost h-8 w-8 p-0" :aria-label="$t('table.order')" @click="move(index, -1)">
              <ChevronUp :size="14" />
            </button>
            <button type="button" class="btn btn-ghost h-8 w-8 p-0" :aria-label="$t('table.order')" @click="move(index, 1)">
              <ChevronDown :size="14" />
            </button>
            <button
              type="button"
              class="btn btn-ghost h-8 w-8 p-0 text-[color:var(--color-danger)]"
              :aria-label="$t('action.delete')"
              @click="remove(index)"
            >
              <Trash2 :size="14" />
            </button>
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div v-for="field in props.fields" :key="field.key">
            <span class="label">{{ field.label }}</span>

            <template v-if="field.translated">
              <div class="space-y-1.5">
                <div v-for="code in locales.available" :key="code" class="flex items-center gap-2">
                  <span class="w-7 shrink-0 text-xs font-semibold uppercase text-steel-400">{{ code }}</span>
                  <input
                    v-if="field.type !== 'textarea'"
                    :value="row[field.key]?.[code] ?? ''"
                    type="text"
                    class="field"
                    @input="update(index, field, code, ($event.target as HTMLInputElement).value)"
                  >
                  <textarea
                    v-else
                    :value="row[field.key]?.[code] ?? ''"
                    rows="2"
                    class="field h-auto py-2"
                    @input="update(index, field, code, ($event.target as HTMLTextAreaElement).value)"
                  />
                </div>
              </div>
            </template>

            <select
              v-else-if="field.type === 'select'"
              :value="row[field.key] ?? ''"
              class="field"
              @change="update(index, field, null, ($event.target as HTMLSelectElement).value)"
            >
              <option value="">—</option>
              <option v-for="option in field.options ?? []" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>

            <input
              v-else
              :value="row[field.key] ?? ''"
              type="text"
              class="field"
              @input="update(index, field, null, ($event.target as HTMLInputElement).value)"
            >
          </div>
        </div>
      </div>
    </div>

    <p v-else class="rounded-md border border-dashed border-steel-300 px-4 py-6 text-center text-sm text-steel-400">
      {{ $t('table.empty') }}
    </p>
  </div>
</template>
