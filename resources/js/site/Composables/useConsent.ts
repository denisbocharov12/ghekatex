import { computed, ref } from 'vue'

/**
 * Согласие на cookie.
 *
 * Выбор посетителя хранится в обычной cookie: он должен пережить перезагрузку
 * и быть виден серверу. Факт согласия дополнительно уходит в журнал — этого
 * требует GDPR как доказательство.
 */

export interface ConsentConfig {
    cookie: string
    categories: string[]
    policy_version: string
    lifetime_days: number
}

export interface ConsentState {
    categories: string[]
    version: string
    id: string
}

function readCookie(name: string): string | null {
    const match = document.cookie.match(new RegExp(`(?:^|; )${name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}=([^;]*)`))

    return match ? decodeURIComponent(match[1]) : null
}

function writeCookie(name: string, value: string, days: number): void {
    const expires = new Date(Date.now() + days * 864e5).toUTCString()
    const secure = window.location.protocol === 'https:' ? '; Secure' : ''

    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax${secure}`
}

function uuid(): string {
    return typeof crypto !== 'undefined' && 'randomUUID' in crypto
        ? crypto.randomUUID()
        : `${Date.now().toString(16)}-${Math.random().toString(16).slice(2, 10)}-4000-8000-${Math.random().toString(16).slice(2, 14)}`
}

const state = ref<ConsentState | null>(null)
const loaded = ref(false)

export function useConsent(config: ConsentConfig) {
    if (!loaded.value) {
        loaded.value = true

        try {
            const raw = readCookie(config.cookie)
            const parsed = raw ? (JSON.parse(raw) as ConsentState) : null

            // Обновление политики сбрасывает согласие: посетитель соглашался с другой редакцией
            state.value = parsed && parsed.version === config.policy_version ? parsed : null
        } catch {
            state.value = null
        }
    }

    const decided = computed(() => state.value !== null)

    const granted = computed(() => state.value?.categories ?? ['necessary'])

    function allows(category: string): boolean {
        return granted.value.includes(category)
    }

    async function save(categories: string[]): Promise<void> {
        const next: ConsentState = {
            categories: Array.from(new Set(['necessary', ...categories])),
            version: config.policy_version,
            id: state.value?.id ?? uuid(),
        }

        state.value = next
        writeCookie(config.cookie, JSON.stringify(next), config.lifetime_days)

        try {
            await fetch('/consent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({ categories: next.categories, anonymous_id: next.id }),
            })
        } catch {
            // Журнал согласий не критичен для посетителя — выбор уже применён локально
        }
    }

    function reset(): void {
        state.value = null
    }

    return { decided, granted, allows, save, reset }
}
