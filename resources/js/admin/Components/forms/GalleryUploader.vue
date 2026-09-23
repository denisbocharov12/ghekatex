<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Upload, Trash2, GripVertical } from 'lucide-vue-next'
import ConfirmModal from '@admin/Components/ui/ConfirmModal.vue'
import type { GalleryItem } from '@admin/Types/forms'

/**
 * Галерея раздела.
 *
 * Уже загруженные файлы живут на сервере, поэтому удаление и порядок
 * отправляются сразу отдельными запросами — иначе редактор потерял бы
 * изменения, не нажав «Сохранить».
 */
const props = defineProps<{
    modelValue: File[]
    existing: GalleryItem[]
    routePrefix: string
    itemId: number | null
}>()

const emit = defineEmits<{ 'update:modelValue': [File[]] }>()

const deleting = ref<number | null>(null)
const dragIndex = ref<number | null>(null)
const order = ref<GalleryItem[]>([...props.existing])

function add(event: Event): void {
    const files = Array.from((event.target as HTMLInputElement).files ?? [])

    emit('update:modelValue', [...props.modelValue, ...files])
}

function removePending(index: number): void {
    emit('update:modelValue', props.modelValue.filter((_, position) => position !== index))
}

function confirmDelete(): void {
    if (deleting.value === null || props.itemId === null) return

    router.delete(route(`admin.${props.routePrefix}.media.destroy`, [props.itemId, deleting.value]), {
        preserveScroll: true,
        onSuccess: () => {
            order.value = order.value.filter((item) => item.id !== deleting.value)
        },
        onFinish: () => (deleting.value = null),
    })
}

function onDrop(targetIndex: number): void {
    if (dragIndex.value === null || dragIndex.value === targetIndex || props.itemId === null) return

    const items = [...order.value]
    const [moved] = items.splice(dragIndex.value, 1)
    items.splice(targetIndex, 0, moved)

    order.value = items
    dragIndex.value = null

    router.post(
        route(`admin.${props.routePrefix}.media.reorder`, props.itemId),
        { ids: items.map((item) => item.id) },
        { preserveScroll: true, preserveState: true },
    )
}
</script>

<template>
  <div>
    <span class="label">{{ $t('form.gallery') }}</span>

    <div v-if="order.length" class="mb-4 grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6">
      <figure
        v-for="(item, index) in order"
        :key="item.id"
        class="group relative aspect-square overflow-hidden rounded-md border border-steel-200"
        draggable="true"
        @dragstart="dragIndex = index"
        @dragover.prevent
        @drop="onDrop(index)"
      >
        <img :src="item.thumb" :alt="item.name" class="h-full w-full object-cover" loading="lazy">

        <span class="absolute left-1 top-1 cursor-grab rounded bg-white/85 p-1 text-steel-500 opacity-0 transition-opacity group-hover:opacity-100">
          <GripVertical :size="13" />
        </span>

        <button
          type="button"
          class="absolute right-1 top-1 rounded bg-white/85 p-1 text-[color:var(--color-danger)] opacity-0 transition-opacity group-hover:opacity-100"
          :aria-label="$t('action.delete')"
          @click="deleting = item.id"
        >
          <Trash2 :size="13" />
        </button>
      </figure>
    </div>

    <ul v-if="props.modelValue.length" class="mb-3 space-y-1.5">
      <li
        v-for="(file, index) in props.modelValue"
        :key="`${file.name}-${index}`"
        class="flex items-center justify-between gap-3 rounded border border-steel-200 px-3 py-2 text-sm"
      >
        <span class="truncate text-steel-600">{{ file.name }}</span>
        <button type="button" class="text-xs text-[color:var(--color-danger)]" @click="removePending(index)">
          {{ $t('action.remove') }}
        </button>
      </li>
    </ul>

    <label class="flex cursor-pointer items-center justify-center gap-2 rounded-md border border-dashed border-steel-300 px-4 py-5 text-sm text-steel-500 transition-colors hover:border-primary-400 hover:text-primary-700">
      <Upload :size="16" />
      {{ $t('form.drop_here') }}
      <input type="file" class="sr-only" accept="image/*" multiple @change="add">
    </label>

    <ConfirmModal
      :open="deleting !== null"
      :title="$t('confirm.delete_media')"
      @confirm="confirmDelete"
      @cancel="deleting = null"
    />
  </div>
</template>
