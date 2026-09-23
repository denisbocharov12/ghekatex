<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { Bold, Italic, List, ListOrdered, Heading2, Heading3, Link2, Undo2, Redo2, Eraser } from 'lucide-vue-next'

/**
 * Простой редактор форматированного текста на contenteditable.
 *
 * Сторонний редактор здесь избыточен: нужны заголовки, списки, ссылки
 * и жирный текст — ровно то, что рендерит `.prose-site` на витрине.
 * Вставка очищается от чужой разметки, иначе Word приносит свои стили.
 */
const props = defineProps<{ modelValue: string; placeholder?: string }>()
const emit = defineEmits<{ 'update:modelValue': [string] }>()

const editor = ref<HTMLElement | null>(null)

const tools = [
    { command: 'bold', icon: Bold, label: 'Полужирный' },
    { command: 'italic', icon: Italic, label: 'Курсив' },
    { command: 'formatBlock', value: 'h2', icon: Heading2, label: 'Заголовок 2' },
    { command: 'formatBlock', value: 'h3', icon: Heading3, label: 'Заголовок 3' },
    { command: 'insertUnorderedList', icon: List, label: 'Маркированный список' },
    { command: 'insertOrderedList', icon: ListOrdered, label: 'Нумерованный список' },
]

function exec(command: string, value?: string): void {
    editor.value?.focus()
    document.execCommand(command, false, value)
    sync()
}

function createLink(): void {
    const url = window.prompt('URL')

    if (url) exec('createLink', url)
}

function sync(): void {
    emit('update:modelValue', editor.value?.innerHTML ?? '')
}

function onPaste(event: ClipboardEvent): void {
    event.preventDefault()

    const text = event.clipboardData?.getData('text/plain') ?? ''
    document.execCommand('insertText', false, text)
    sync()
}

onMounted(() => {
    if (editor.value) editor.value.innerHTML = props.modelValue ?? ''
})

// Значение меняется извне при переключении локали — переписываем содержимое
watch(
    () => props.modelValue,
    (next) => {
        if (editor.value && editor.value.innerHTML !== (next ?? '')) {
            editor.value.innerHTML = next ?? ''
        }
    },
)
</script>

<template>
  <div class="overflow-hidden rounded-md border border-steel-300 bg-white focus-within:border-primary-500">
    <div class="flex flex-wrap items-center gap-0.5 border-b border-steel-200 bg-steel-50 p-1.5">
      <button
        v-for="tool in tools"
        :key="tool.label"
        type="button"
        class="btn btn-ghost h-8 w-8 p-0"
        :title="tool.label"
        :aria-label="tool.label"
        @click="exec(tool.command, tool.value)"
      >
        <component :is="tool.icon" :size="15" />
      </button>

      <button type="button" class="btn btn-ghost h-8 w-8 p-0" title="Ссылка" aria-label="Ссылка" @click="createLink">
        <Link2 :size="15" />
      </button>

      <span class="mx-1 h-5 w-px bg-steel-200" />

      <button type="button" class="btn btn-ghost h-8 w-8 p-0" title="Убрать форматирование" aria-label="Убрать форматирование" @click="exec('removeFormat')">
        <Eraser :size="15" />
      </button>
      <button type="button" class="btn btn-ghost h-8 w-8 p-0" title="Отменить" aria-label="Отменить" @click="exec('undo')">
        <Undo2 :size="15" />
      </button>
      <button type="button" class="btn btn-ghost h-8 w-8 p-0" title="Повторить" aria-label="Повторить" @click="exec('redo')">
        <Redo2 :size="15" />
      </button>
    </div>

    <div
      ref="editor"
      contenteditable="true"
      role="textbox"
      aria-multiline="true"
      class="prose-admin min-h-48 px-4 py-3 text-sm leading-relaxed outline-none"
      :data-placeholder="props.placeholder"
      @input="sync"
      @blur="sync"
      @paste="onPaste"
    />
  </div>
</template>

<style scoped>
.prose-admin:empty::before {
  content: attr(data-placeholder);
  color: var(--color-steel-400);
}

.prose-admin :deep(h2) {
  font-size: 1.25rem;
  font-weight: 600;
  margin-block: 1rem 0.5rem;
}

.prose-admin :deep(h3) {
  font-size: 1.0625rem;
  font-weight: 600;
  margin-block: 0.875rem 0.375rem;
}

.prose-admin :deep(p) {
  margin-block: 0.5rem;
}

.prose-admin :deep(ul),
.prose-admin :deep(ol) {
  margin-block: 0.5rem;
  padding-inline-start: 1.5rem;
}

.prose-admin :deep(ul) {
  list-style: disc;
}

.prose-admin :deep(ol) {
  list-style: decimal;
}

.prose-admin :deep(a) {
  color: var(--color-primary-700);
  text-decoration: underline;
}
</style>
