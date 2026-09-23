<script setup lang="ts">
import { computed, onMounted, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useConsent, type ConsentConfig } from '@site/Composables/useConsent'

/**
 * Google Analytics 4 и Яндекс.Метрика.
 *
 * Скрипты подключаются только после согласия на аналитику — до этого момента
 * ни один внешний счётчик не должен попасть на страницу. Переходы Inertia
 * не перезагружают документ, поэтому просмотры отправляем вручную.
 */

const page = usePage()

const ids = computed(() => (page.props.analytics ?? {}) as { ga4?: string | null; metrika?: string | null; gtm?: string | null })
const consentConfig = computed(() => page.props.consent as ConsentConfig)

const { allows } = useConsent(consentConfig.value)

let loaded = false

function injectGa4(id: string): void {
    const script = document.createElement('script')
    script.async = true
    script.src = `https://www.googletagmanager.com/gtag/js?id=${id}`
    document.head.appendChild(script)

    const w = window as unknown as { dataLayer: unknown[]; gtag: (...args: unknown[]) => void }
    w.dataLayer = w.dataLayer || []
    w.gtag = function gtag() {
        w.dataLayer.push(arguments)
    }
    w.gtag('js', new Date())
    w.gtag('config', id, { send_page_view: true, anonymize_ip: true })
}

/**
 * Google Tag Manager.
 *
 * Контейнер грузится только после согласия на аналитику: теги внутри него
 * заводит маркетолог, и до согласия ни один из них попасть на страницу
 * не должен. Состояние согласия кладём в dataLayer — GTM умеет строить
 * на нём Consent Mode.
 */
function injectGtm(id: string): void {
    const w = window as unknown as { dataLayer: unknown[] }
    w.dataLayer = w.dataLayer || []
    w.dataLayer.push({ event: 'consent_granted', consent_analytics: true })
    w.dataLayer.push({ 'gtm.start': Date.now(), event: 'gtm.js' })

    const script = document.createElement('script')
    script.async = true
    script.src = `https://www.googletagmanager.com/gtm.js?id=${id}`
    document.head.appendChild(script)
}

function injectMetrika(id: string): void {
    const script = document.createElement('script')
    script.async = true
    script.src = 'https://mc.yandex.ru/metrika/tag.js'
    document.head.appendChild(script)

    const w = window as unknown as { ym?: ((...args: unknown[]) => void) & { a?: unknown[]; l?: number } }
    w.ym =
        w.ym ||
        function ym(...args: unknown[]) {
            ;(w.ym!.a = w.ym!.a || []).push(args)
        }
    w.ym.l = Date.now()
    w.ym(Number(id), 'init', { clickmap: true, trackLinks: true, accurateTrackBounce: true })
}

function boot(): void {
    if (loaded || !allows('analytics')) return

    loaded = true

    if (ids.value.gtm) injectGtm(ids.value.gtm)
    if (ids.value.ga4) injectGa4(ids.value.ga4)
    if (ids.value.metrika) injectMetrika(ids.value.metrika)
}

function trackPageView(url: string): void {
    if (!loaded) return

    const w = window as unknown as { gtag?: (...args: unknown[]) => void; ym?: (...args: unknown[]) => void }

    w.gtag?.('event', 'page_view', { page_path: url })

    // Переходы Inertia не перезагружают документ — сообщаем о них контейнеру
    if (ids.value.gtm) {
        (window as unknown as { dataLayer?: unknown[] }).dataLayer?.push({ event: 'pageview', page_path: url })
    }

    if (ids.value.metrika) {
        w.ym?.(Number(ids.value.metrika), 'hit', url)
    }
}

onMounted(() => {
    boot()
    router.on('navigate', (event) => trackPageView(event.detail.page.url))
})

// Посетитель может согласиться позже — счётчики подключаются в тот же момент
watch(() => allows('analytics'), (value) => {
    if (value) boot()
})
</script>

<template>
  <span class="hidden" aria-hidden="true" />
</template>
