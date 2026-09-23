<script setup lang="ts">
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * Переводимое поле: по вкладке на локаль.
 *
 * Основная локаль помечена звёздочкой и идёт первой — именно она обязательна
 * и из неё строится слаг. Вкладка с заполненным переводом получает точку,
 * чтобы редактор видел, где ещё пусто.
 */
const props = defineProps<{ modelValue: Record<string, string>; label: string; required?: boolean; error?: string | Record<string, string> }>()
const emit = defineEmits<{ 'update:modelValue': [Record<string, string>] }>()

const page = usePage()
const locales = computed(() => (page.props.locales as { available: string[]; labels: Record<string, string>; default: string }))

const active = ref(locales.value.default)

const value = computed({
    get: () => props.modelValue ?? {},
    set: (next) => emit('update:modelValue', next),
})

function update(locale: string, next: string): void {
    value.value = { ...value.value, [locale]: next }
}
</script>

<template>
  <div>
    <div class="mb-1.5 flex items-center justify-between gap-3">
      <span class="label mb-0">{{ props.label }}<span v-if="props.required" class="text-[color:var(--color-danger)]"> *</span></span>

      <div class="flex gap-1">
        <button
          v-for="code in locales.available"
          :key="code"
          type="button"
          :class="[
            'inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-semibold uppercase transition-colors',
            active === code ? 'bg-primary-900 text-white' : 'text-steel-500 hover:bg-steel-100',
          ]"
          @click="active = code"
        >
          {{ code }}
          <span
            v-if="value[code]"
            class="h-1.5 w-1.5 rounded-full"
            :class="active === code ? 'bg-accent-400' : 'bg-emerald-500'"
          />
        </button>
      </div>
    </div>

    <div v-for="code in locales.available" v-show="active === code" :key="code">
      <slot
        :locale="code"
        :value="value[code] ?? ''"
        :update="(next: string) => update(code, next)"
        :is-primary="code === locales.default"
      />
    </div>

    <p v-if="typeof props.error === 'string' && props.error" class="error-text">{{ props.error }}</p>
  </div>
</template>
