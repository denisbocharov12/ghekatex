import { createI18n } from 'vue-i18n'
import ru from './ru.json'

/**
 * Словарь панели. Язык интерфейса берётся из профиля пользователя,
 * а не из языка витрины: редактор ведёт румынский сайт из русской админки.
 */
const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: 'ru',
    fallbackLocale: 'ru',
    messages: { ru },
    missingWarn: import.meta.env.DEV,
    fallbackWarn: import.meta.env.DEV,
})

export default i18n
