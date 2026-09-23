/** Форматирование дат и чисел под текущую локаль витрины. */

const LOCALE_MAP: Record<string, string> = {
    ro: 'ro-MD',
    en: 'en-GB',
    ru: 'ru-RU',
}

export function formatDate(value: string | null | undefined, locale: string): string {
    if (!value) return ''

    return new Intl.DateTimeFormat(LOCALE_MAP[locale] ?? locale, {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(value))
}

export function formatNumber(value: number | string | null | undefined, locale: string): string {
    if (value === null || value === undefined || value === '') return ''

    const numeric = typeof value === 'number' ? value : Number(String(value).replace(/\s/g, ''))

    if (Number.isNaN(numeric)) return String(value)

    return new Intl.NumberFormat(LOCALE_MAP[locale] ?? locale).format(numeric)
}
