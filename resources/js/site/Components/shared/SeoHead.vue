<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import type { SeoProps } from '@site/Types'

/**
 * Мета-теги страницы. Всё приходит с сервера готовым: клиент только
 * раскладывает значения по тегам, чтобы разметка совпадала с тем,
 * что видит поисковый робот.
 */
const props = defineProps<{ seo: SeoProps }>()

onMounted(() => {
    // Теги, напечатанные сервером для краулеров, дальше ведёт этот компонент
    for (const element of document.querySelectorAll('[data-server-seo]')) {
        element.remove()
    }
})

const schemaJson = computed(() =>
    props.seo.schema.length === 0 ? null : JSON.stringify(props.seo.schema.length === 1 ? props.seo.schema[0] : props.seo.schema),
)
</script>

<template>
  <Head :title="seo.title">
    <meta v-if="seo.description" name="description" :content="seo.description">
    <meta v-if="seo.keywords" name="keywords" :content="seo.keywords">
    <meta name="robots" :content="seo.robots">
    <link v-if="seo.canonical" rel="canonical" :href="seo.canonical">

    <link
      v-for="(href, hreflang) in seo.alternates"
      :key="hreflang"
      rel="alternate"
      :hreflang="String(hreflang)"
      :href="href"
    >

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="GHEKATEX">
    <meta property="og:title" :content="seo.og_title || seo.title">
    <meta v-if="seo.og_description" property="og:description" :content="seo.og_description">
    <meta v-if="seo.og_image" property="og:image" :content="seo.og_image">
    <meta v-if="seo.canonical" property="og:url" :content="seo.canonical">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" :content="seo.og_title || seo.title">
    <meta v-if="seo.og_description" name="twitter:description" :content="seo.og_description">
    <meta v-if="seo.og_image" name="twitter:image" :content="seo.og_image">

    <component :is="'script'" v-if="schemaJson" type="application/ld+json">{{ schemaJson }}</component>
  </Head>
</template>
