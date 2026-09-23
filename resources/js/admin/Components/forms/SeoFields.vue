<script setup lang="ts">
import { computed, ref } from 'vue'
import { ChevronDown, Search } from 'lucide-vue-next'
import LocaleTabs from './LocaleTabs.vue'

/** Блок SEO формы: заголовки и описания по локалям плюс технические поля. */
const props = defineProps<{ modelValue: Record<string, any> }>()
const emit = defineEmits<{ 'update:modelValue': [Record<string, any>] }>()

const open = ref(false)

const seo = computed({
    get: () => props.modelValue ?? {},
    set: (next) => emit('update:modelValue', next),
})

function set(key: string, value: unknown): void {
    seo.value = { ...seo.value, [key]: value }
}

const robotsOptions = ['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow']
const frequencyOptions = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never']
</script>

<template>
  <section class="panel overflow-hidden">
    <button
      type="button"
      class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left"
      :aria-expanded="open"
      @click="open = !open"
    >
      <span class="flex items-center gap-2.5">
        <Search :size="17" class="text-steel-400" />
        <span class="text-sm font-semibold text-steel-800">{{ $t('form.seo') }}</span>
      </span>
      <ChevronDown :size="17" :class="['text-steel-400 transition-transform', open ? 'rotate-180' : '']" />
    </button>

    <div v-show="open" class="space-y-5 border-t border-steel-200 p-5">
      <LocaleTabs :model-value="seo.title ?? {}" label="Title" @update:model-value="set('title', $event)">
        <template #default="{ value, update }">
          <input :value="value" type="text" maxlength="70" class="field" @input="update(($event.target as HTMLInputElement).value)">
        </template>
      </LocaleTabs>

      <LocaleTabs :model-value="seo.description ?? {}" label="Description" @update:model-value="set('description', $event)">
        <template #default="{ value, update }">
          <textarea :value="value" rows="3" maxlength="200" class="field h-auto py-2" @input="update(($event.target as HTMLTextAreaElement).value)" />
        </template>
      </LocaleTabs>

      <LocaleTabs :model-value="seo.og_title ?? {}" label="OG title" @update:model-value="set('og_title', $event)">
        <template #default="{ value, update }">
          <input :value="value" type="text" class="field" @input="update(($event.target as HTMLInputElement).value)">
        </template>
      </LocaleTabs>

      <LocaleTabs :model-value="seo.og_description ?? {}" label="OG description" @update:model-value="set('og_description', $event)">
        <template #default="{ value, update }">
          <textarea :value="value" rows="2" class="field h-auto py-2" @input="update(($event.target as HTMLTextAreaElement).value)" />
        </template>
      </LocaleTabs>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="label" for="seo-og-image">OG image URL</label>
          <input
            id="seo-og-image"
            :value="seo.og_image ?? ''"
            type="text"
            class="field"
            @input="set('og_image', ($event.target as HTMLInputElement).value)"
          >
        </div>

        <div>
          <label class="label" for="seo-canonical">Canonical URL</label>
          <input
            id="seo-canonical"
            :value="seo.canonical_url ?? ''"
            type="text"
            class="field"
            @input="set('canonical_url', ($event.target as HTMLInputElement).value)"
          >
        </div>

        <div>
          <label class="label" for="seo-robots">Robots</label>
          <select id="seo-robots" :value="seo.robots ?? 'index,follow'" class="field" @change="set('robots', ($event.target as HTMLSelectElement).value)">
            <option v-for="option in robotsOptions" :key="option" :value="option">{{ option }}</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label" for="seo-priority">Sitemap priority</label>
            <input
              id="seo-priority"
              :value="seo.sitemap_priority ?? 0.5"
              type="number"
              step="0.1"
              min="0"
              max="1"
              class="field"
              @input="set('sitemap_priority', ($event.target as HTMLInputElement).value)"
            >
          </div>

          <div>
            <label class="label" for="seo-frequency">Sitemap frequency</label>
            <select
              id="seo-frequency"
              :value="seo.sitemap_frequency ?? 'monthly'"
              class="field"
              @change="set('sitemap_frequency', ($event.target as HTMLSelectElement).value)"
            >
              <option v-for="option in frequencyOptions" :key="option" :value="option">{{ option }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
