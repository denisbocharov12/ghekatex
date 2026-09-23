import type { I18n } from 'vue-i18n'

/**
 * Накладывает правки текстов из админки поверх словаря локали.
 *
 * Ключи приходят плоскими («nav.about»), а vue-i18n хранит их деревом,
 * поэтому разворачиваем путь перед слиянием.
 */
export function applyOverrides(i18n: I18n, locale: string, overrides: Record<string, string> | undefined): void {
    if (!overrides || Object.keys(overrides).length === 0) {
        return
    }

    const tree: Record<string, unknown> = {}

    for (const [path, value] of Object.entries(overrides)) {
        if (!value) continue

        const parts = path.split('.')
        let node = tree

        parts.forEach((part, index) => {
            if (index === parts.length - 1) {
                node[part] = value
                return
            }

            node[part] = (node[part] as Record<string, unknown>) ?? {}
            node = node[part] as Record<string, unknown>
        })
    }

    i18n.global.mergeLocaleMessage(locale, tree as never)
}
