import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import { ZiggyVue } from 'ziggy-js'
import i18n from './i18n'
import { applyOverrides } from '../shared/i18nOverrides'
import '../../css/admin.css'

const appName = import.meta.env.VITE_APP_NAME || 'GHEKATEX'

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : `${appName} — панель управления`),

    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),

    setup({ el, App, props, plugin }) {
        const page = props.initialPage.props as Record<string, any>

        applyOverrides(i18n, 'ru', page.i18n)

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(ZiggyVue)
            .use(i18n)
            .mount(el)
    },

    progress: {
        color: '#162456',
        showSpinner: true,
    },
})
