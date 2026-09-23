<script setup lang="ts">
import { computed, ref } from 'vue'
import { Upload, X, FileText } from 'lucide-vue-next'

/**
 * Одиночный файл коллекции: обложка, логотип, документ.
 * Пока файл не выбран, показываем текущий — редактор видит, что заменяет.
 */
const props = defineProps<{
    modelValue: File | null
    label: string
    currentUrl?: string | null
    accept?: string
    hint?: string
    error?: string
}>()

const emit = defineEmits<{ 'update:modelValue': [File | null] }>()

const input = ref<HTMLInputElement | null>(null)
const preview = ref<string | null>(null)

const isImage = computed(() => (props.accept ?? 'image/*').includes('image'))

function pick(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null

    emit('update:modelValue', file)
    preview.value = file && isImage.value ? URL.createObjectURL(file) : null
}

function clear(): void {
    emit('update:modelValue', null)
    preview.value = null

    if (input.value) input.value.value = ''
}

const shown = computed(() => preview.value ?? props.currentUrl ?? null)
</script>

<template>
  <div>
    <span class="label">{{ props.label }}</span>

    <div class="flex items-start gap-4">
      <div
        v-if="shown && isImage"
        class="relative h-28 w-28 shrink-0 overflow-hidden rounded-md border border-steel-200 bg-steel-50"
      >
        <img :src="shown" :alt="props.label" class="h-full w-full object-cover">
      </div>

      <a
        v-else-if="shown"
        :href="shown"
        target="_blank"
        rel="noopener"
        class="flex h-28 w-28 shrink-0 flex-col items-center justify-center gap-2 rounded-md border border-steel-200 bg-steel-50 text-steel-500 hover:text-primary-700"
      >
        <FileText :size="24" />
        <span class="text-xs">{{ $t('form.current_file') }}</span>
      </a>

      <div class="flex-1">
        <label
          class="flex cursor-pointer items-center justify-center gap-2 rounded-md border border-dashed border-steel-300 px-4 py-6 text-sm text-steel-500 transition-colors hover:border-primary-400 hover:text-primary-700"
        >
          <Upload :size="16" />
          {{ $t('form.drop_here') }}
          <input
            ref="input"
            type="file"
            class="sr-only"
            :accept="props.accept ?? 'image/*'"
            @change="pick"
          >
        </label>

        <div class="mt-2 flex items-center gap-3">
          <p v-if="props.hint" class="hint">{{ props.hint }}</p>

          <button
            v-if="props.modelValue"
            type="button"
            class="ml-auto inline-flex items-center gap-1 text-xs text-[color:var(--color-danger)]"
            @click="clear"
          >
            <X :size="13" />
            {{ $t('action.remove') }}
          </button>
        </div>

        <p v-if="props.error" class="error-text">{{ props.error }}</p>
      </div>
    </div>
  </div>
</template>
