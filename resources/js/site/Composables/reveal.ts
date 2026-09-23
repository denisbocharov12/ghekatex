import type { Directive } from 'vue'

/**
 * Появление блока при прокрутке.
 *
 * Своя директива вместо библиотеки: нужен один класс и один наблюдатель,
 * а подключать ради этого отдельный пакет со своими стилями — перебор.
 * Значение директивы — задержка в миллисекундах.
 */

let observer: IntersectionObserver | null = null

function ensureObserver(): IntersectionObserver {
    observer ??= new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue

                const element = entry.target as HTMLElement
                const delay = Number(element.dataset.revealDelay ?? 0)

                window.setTimeout(() => element.classList.add('is-visible'), delay)
                observer?.unobserve(element)
            }
        },
        { rootMargin: '0px 0px -12% 0px', threshold: 0.12 },
    )

    return observer
}

export const vReveal: Directive<HTMLElement, number | undefined> = {
    mounted(element, binding) {
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches

        if (reduced) {
            element.classList.add('is-visible')

            return
        }

        if (!element.classList.contains('reveal-wipe')) {
            element.classList.add('reveal')
        }

        if (binding.value) {
            element.dataset.revealDelay = String(binding.value)
        }

        // Блок, уже находящийся в кадре при монтировании, показываем сразу:
        // наблюдатель сработал бы только на следующей прокрутке
        const box = element.getBoundingClientRect()

        if (box.top < window.innerHeight * 0.9) {
            window.setTimeout(() => element.classList.add('is-visible'), binding.value ?? 0)

            return
        }

        ensureObserver().observe(element)
    },

    unmounted(element) {
        observer?.unobserve(element)
    },
}
