import { ref } from 'vue'

/**
 * Состояние модального окна обратной связи.
 *
 * Окно живёт в макете в единственном экземпляре, а открывают его кнопки из
 * шапки, меню и обложки. Состояние на уровне модуля избавляет от прокидывания
 * событий через всё дерево.
 */
const open = ref(false)
const preselected = ref<string[]>([])

export function useContactDialog() {
    /** @param services слаги услуг, отмеченных заранее */
    function openDialog(services: string[] = []): void {
        preselected.value = services
        open.value = true
    }

    function closeDialog(): void {
        open.value = false
    }

    return { open, preselected, openDialog, closeDialog }
}
