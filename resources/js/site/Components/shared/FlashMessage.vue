<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { CheckCircle2, AlertCircle, X } from 'lucide-vue-next'

const props = withDefaults(defineProps<{ message: string; type?: 'success' | 'error'; timeout?: number }>(), {
    type: 'success',
    timeout: 6000,
})

const visible = ref(true)

onMounted(() => {
    // Сообщение исчезает само: посетителю не нужно его закрывать вручную
    window.setTimeout(() => (visible.value = false), props.timeout)
})
</script>

<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-3"
    leave-active-class="transition duration-200 ease-in"
    leave-to-class="opacity-0 translate-y-3"
  >
    <div
      v-if="visible"
      class="fixed left-1/2 top-24 z-[65] w-[min(30rem,calc(100vw-2rem))] -translate-x-1/2"
      role="status"
      aria-live="polite"
    >
      <div
        class="flex items-start gap-3 border px-5 py-4 shadow-lg"
        :class="props.type === 'success' ? 'bg-white border-success/30' : 'bg-white border-danger/30'"
      >
        <CheckCircle2 v-if="props.type === 'success'" :size="20" class="mt-0.5 shrink-0 text-[color:var(--color-success)]" />
        <AlertCircle v-else :size="20" class="mt-0.5 shrink-0 text-[color:var(--color-danger)]" />

        <p class="flex-1 text-sm text-bone-700">{{ props.message }}</p>

        <button type="button" class="text-bone-400 hover:text-bone-700" aria-label="×" @click="visible = false">
          <X :size="16" />
        </button>
      </div>
    </div>
  </Transition>
</template>
