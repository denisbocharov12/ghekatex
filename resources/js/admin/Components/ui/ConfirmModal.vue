<script setup lang="ts">
import { onKeyStroke } from '@vueuse/core'
import { AlertTriangle } from 'lucide-vue-next'

const props = withDefaults(
    defineProps<{ open: boolean; title?: string; text?: string; confirmLabel?: string; danger?: boolean }>(),
    { danger: true },
)

const emit = defineEmits<{ confirm: []; cancel: [] }>()

onKeyStroke('Escape', () => {
    if (props.open) emit('cancel')
})
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0"
      leave-active-class="transition duration-100 ease-in"
      leave-to-class="opacity-0"
    >
      <div
        v-if="props.open"
        class="fixed inset-0 z-[80] flex items-center justify-center bg-steel-900/50 p-4"
        role="dialog"
        aria-modal="true"
        @click.self="emit('cancel')"
      >
        <div class="panel w-full max-w-md p-6 shadow-xl">
          <div class="flex gap-4">
            <span
              class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
              :class="props.danger ? 'bg-red-50 text-[color:var(--color-danger)]' : 'bg-steel-100 text-steel-600'"
            >
              <AlertTriangle :size="19" />
            </span>

            <div class="flex-1">
              <h2 class="text-base font-semibold text-steel-900">{{ props.title ?? $t('confirm.delete_title') }}</h2>
              <p class="mt-1.5 text-sm text-steel-500">{{ props.text ?? $t('confirm.delete_text') }}</p>
            </div>
          </div>

          <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="btn btn-secondary" @click="emit('cancel')">{{ $t('action.cancel') }}</button>
            <button
              type="button"
              :class="['btn', props.danger ? 'btn-danger' : 'btn-primary']"
              @click="emit('confirm')"
            >
              {{ props.confirmLabel ?? $t('action.delete') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
