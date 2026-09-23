import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * Права текущего пользователя.
 *
 * Серверные проверки остаются главными — здесь мы лишь не показываем кнопки,
 * которые всё равно вернут 403.
 */
export function usePermissions() {
    const page = usePage()

    const user = computed(() => page.props.auth?.user as { roles: string[]; permissions: string[] } | null)

    const isSuperAdmin = computed(() => user.value?.roles?.includes('super-admin') ?? false)

    function can(permission: string): boolean {
        if (isSuperAdmin.value) return true

        return user.value?.permissions?.includes(permission) ?? false
    }

    function canAny(permissions: string[]): boolean {
        return permissions.some((permission) => can(permission))
    }

    return { user, isSuperAdmin, can, canAny }
}
