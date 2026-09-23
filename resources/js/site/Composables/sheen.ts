import type { Directive } from 'vue'

/**
 * Блики, которые смещаются вместе с прокруткой.
 *
 * Директива пишет в элемент переменную `--sheen` со значением от 0 до 1 —
 * долю пройденного пути секции через экран. Всё остальное делает CSS:
 * так блики остаются задачей стилей, а не JavaScript.
 */
const watched = new Set<HTMLElement>()

let frame = 0
let listening = false

function update(): void {
    frame = 0

    const viewport = window.innerHeight || 1

    for (const element of watched) {
        const box = element.getBoundingClientRect()
        const travelled = (viewport - box.top) / (viewport + box.height)

        element.style.setProperty('--sheen', Math.min(1, Math.max(0, travelled)).toFixed(4))
    }
}

function schedule(): void {
    if (frame) return

    frame = window.requestAnimationFrame(update)
}

function listen(): void {
    if (listening) return

    listening = true
    window.addEventListener('scroll', schedule, { passive: true })
    window.addEventListener('resize', schedule, { passive: true })

    // На скрытой вкладке кадры не выдаются: запланированный сбрасываем,
    // иначе после возврата очередь так и осталась бы занятой
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) return

        if (frame) window.cancelAnimationFrame(frame)

        frame = 0
        schedule()
    })
}

export const vSheen: Directive<HTMLElement> = {
    mounted(element) {
        // При запрете анимаций оставляем один статичный кадр
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            element.style.setProperty('--sheen', '0.5')

            return
        }

        watched.add(element)
        listen()
        schedule()
    },

    unmounted(element) {
        watched.delete(element)
    },
}
