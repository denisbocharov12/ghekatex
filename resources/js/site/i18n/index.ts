import { createI18n } from 'vue-i18n'
import ro from './ro.json'
import en from './en.json'
import ru from './ru.json'

/**
 * Словарь витрины. Локаль задаёт сервер — она уже в пути и в пропсах Inertia,
 * поэтому определять её на клиенте не нужно.
 */
const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: document.documentElement.lang?.slice(0, 2) || 'ro',
    fallbackLocale: 'ro',
    messages: { ro, en, ru },
    // Недостающий ключ в проде не должен шуметь в консоли посетителя
    missingWarn: import.meta.env.DEV,
    fallbackWarn: import.meta.env.DEV,
})

export default i18n
