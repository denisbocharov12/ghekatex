<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'

/**
 * Счётчик, который досчитывает до значения, когда попадает в кадр.
 * Нечисловое значение выводится как есть — редактор может написать «24/7».
 */
const props = withDefaults(defineProps<{ value: string | null; duration?: number }>(), { duration: 1600 })

const root = ref<HTMLElement | null>(null)
const shown = ref('0')

let observer: IntersectionObserver | null = null
let frame = 0

function digitsOf(value: string): number | null {
    const numeric = Number(value.replace(/[^\d.,-]/g, '').replace(',', '.'))

    return Number.isFinite(numeric) && numeric !== 0 ? numeric : null
}

function run(): void {
    const raw = props.value ?? ''
    const target = digitsOf(raw)

    if (target === null) {
        shown.value = raw

        return
    }

    const started = performance.now()
    const formatter = new Intl.NumberFormat('ru-RU')

    const step = (now: number): void => {
        const progress = Math.min(1, (now - started) / props.duration)
        // Замедление к концу — цифра «доезжает», а не обрывается
        const eased = 1 - Math.pow(1 - progress, 3)

        shown.value = formatter.format(Math.round(target * eased))

        if (progress < 1) {
            frame = requestAnimationFrame(step)
        }
    }

    frame = requestAnimationFrame(step)
}

onMounted(() => {
    if (!root.value) return

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        shown.value = props.value ?? ''

        return
    }

    observer = new IntersectionObserver((entries) => {
        if (entries[0]?.isIntersecting) {
            run()
            observer?.disconnect()
        }
    }, { threshold: 0.4 })

    observer.observe(root.value)
})

onBeforeUnmount(() => {
    observer?.disconnect()
    cancelAnimationFrame(frame)
})
</script>

<template>
  <span ref="root">{{ shown }}</span>
</template>
