<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ResourceForm from '@admin/Components/data/ResourceForm.vue'
import type { FormField } from '@admin/Types/forms'

const props = defineProps<{ item?: Record<string, any> | null; menus?: { value: string; label: string }[]; parents?: Record<string, any>[]; routes?: string[] }>()

/** Справочник в список опций: подпись берём из перевода основной локали. */
function optionsFrom(items: Record<string, any>[] | undefined, key: string) {
    const primary = (usePage().props.locales as { default: string })?.default ?? 'ro'

    return (items ?? []).map((item) => ({
        value: item.id as number,
        label: typeof item[key] === 'object' ? item[key]?.[primary] ?? '—' : String(item[key] ?? '—'),
    }))
}

const fields = computed<FormField[]>(() => ([
        {
            key: 'label',
            label: 'Название пункта',
            type: 'text',
            translated: true,
            required: true
        },
        {
            key: 'menu',
            label: 'Меню',
            type: 'select',
            half: true,
            required: true,
            options: props.menus ?? []
        },
        {
            key: 'parent_id',
            label: 'Родительский пункт',
            type: 'select',
            half: true,
            options: optionsFrom(props.parents, 'name')
        },
        {
            key: 'route_name',
            label: 'Маршрут',
            type: 'select',
            half: true,
            options: props.routes?.map((value) => ({ value, label: value })) ?? [],
            hint: 'Либо маршрут, либо произвольная ссылка'
        },
        {
            key: 'url',
            label: 'Произвольная ссылка',
            type: 'text',
            half: true
        },
        {
            key: 'opens_in_new_tab',
            label: 'Открывать в новой вкладке',
            type: 'checkbox'
        },
        {
            key: 'sort_order',
            label: 'Порядок',
            type: 'number',
            half: true
        },
        {
            key: 'is_active',
            label: 'Опубликовано',
            type: 'checkbox'
        }
    ]))

const title = props.item?.id ? 'Меню сайта — редактирование' : 'Меню сайта — создание'
</script>

<template>
  <ResourceForm
    :title="title"
    route-prefix="navigation"
    permission="navigation"
    :fields="fields"
    :item="props.item"
    :with-seo="false"
  />
</template>
