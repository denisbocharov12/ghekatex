<script setup lang="ts">
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SiteHeader from '@site/Components/shared/SiteHeader.vue'
import SiteFooter from '@site/Components/shared/SiteFooter.vue'
import CookieConsent from '@site/Components/shared/CookieConsent.vue'
import SiteAnalytics from '@site/Components/shared/SiteAnalytics.vue'
import FlashMessage from '@site/Components/shared/FlashMessage.vue'
import ScrollProgress from '@site/Components/shared/ScrollProgress.vue'
import ContactDialog from '@site/Components/shared/ContactDialog.vue'

/**
 * `transparentHeader` включают страницы с полноэкранной обложкой:
 * там шапка лежит поверх кадра и уплотняется только при прокрутке.
 */
withDefaults(defineProps<{ transparentHeader?: boolean }>(), { transparentHeader: false })

const page = usePage()
const consentOpen = ref(false)

const flash = computed(() => page.props.flash as { success?: string; error?: string })
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bone-100">
    <a
      href="#content"
      class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[95] focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-ink-900"
    >
      {{ $t('common.more') }}
    </a>

    <ScrollProgress />

    <SiteHeader :transparent="transparentHeader" />

    <main id="content" class="flex-1">
      <slot />
    </main>

    <SiteFooter @manage-cookies="consentOpen = true" />

    <ContactDialog />

    <CookieConsent :open="consentOpen" @close="consentOpen = false" />
    <SiteAnalytics />

    <FlashMessage v-if="flash?.success" type="success" :message="flash.success" />
    <FlashMessage v-if="flash?.error" type="error" :message="flash.error" />
  </div>
</template>
