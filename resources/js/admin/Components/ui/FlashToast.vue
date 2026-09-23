<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { CheckCircle2, AlertCircle, X } from 'lucide-vue-next'

const props = withDefaults(defineProps<{ message: string; type?: 'success' | 'error' }>(), { type: 'success' })

const visible = ref(true)

onMounted(() => window.setTimeout(() => (visible.value = false), 5000))
</script>

<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0 translate-y-2"
    leave-active-class="transition duration-150 ease-in"
    leave-to-class="opacity-0 translate-y-2"
  >
    <div v-if="visible" class="fixed bottom-6 right-6 z-[70] w-[min(24rem,calc(100vw-3rem))]" role="status" aria-live="polite">
      <div class="panel flex items-start gap-3 px-4 py-3 shadow-lg">
        <CheckCircle2 v-if="props.type === 'success'" :size="18" class="mt-0.5 shrink-0 text-[color:var(--color-success)]" />
        <AlertCircle v-else :size="18" class="mt-0.5 shrink-0 text-[color:var(--color-danger)]" />

        <p class="flex-1 text-sm text-steel-700">{{ props.message }}</p>

        <button type="button" class="text-steel-400 hover:text-steel-700" aria-label="×" @click="visible = false">
          <X :size="15" />
        </button>
      </div>
    </div>
  </Transition>
</template>
