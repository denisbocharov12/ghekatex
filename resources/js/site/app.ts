import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import { ZiggyVue } from 'ziggy-js'
import i18n from './i18n'
import { vReveal } from './Composables/reveal'
import { vSheen } from './Composables/sheen'
import { applyOverrides } from '../shared/i18nOverrides'
import '../../css/site.css'

const appName = import.meta.env.VITE_APP_NAME || 'GHEKATEX'

createInertiaApp({
    title: (title) => (title ? `${title}` : appName),

    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),

    setup({ el, App, props, plugin }) {
        const page = props.initialPage.props as Record<string, any>
        const locale = (page.locale as string) || 'ro'

        i18n.global.locale.value = locale as never
        applyOverrides(i18n, locale, page.i18n)

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(ZiggyVue)
            .use(i18n)
            .directive('reveal', vReveal)
            .directive('sheen', vSheen)
            .mount(el)
    },

    progress: {
        color: '#e8a33d',
        showSpinner: false,
    },
})

router.on('navigate', () => {
    const page = (router.page?.props ?? {}) as Record<string, any>

    if (page.locale) {
        i18n.global.locale.value = page.locale as never
        applyOverrides(i18n, page.locale as string, page.i18n)
    }
})
