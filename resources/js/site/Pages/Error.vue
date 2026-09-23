<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'
import BrandMark from '@site/Components/ui/BrandMark.vue'

/**
 * Страница ошибки.
 *
 * Намеренно не использует общий макет: она отдаётся из обработчика исключений,
 * куда посредники Inertia уже не доходят, и общих пропсов (меню, контакты)
 * здесь нет. Всё нужное приходит отдельными пропсами.
 */
const props = withDefaults(
    defineProps<{ status: number; locale?: string; home?: string }>(),
    { locale: 'ro', home: '/' },
)

const copy = computed(() => {
    const texts: Record<string, Record<number, { title: string; text: string }>> = {
        ro: {
            404: { title: 'Pagina nu a fost găsită', text: 'Este posibil ca adresa să se fi schimbat sau pagina să fi fost ștearsă.' },
            403: { title: 'Acces restricționat', text: 'Nu aveți drepturi pentru această pagină.' },
            419: { title: 'Sesiunea a expirat', text: 'Reîncărcați pagina și încercați din nou.' },
            429: { title: 'Prea multe cereri', text: 'Așteptați puțin și încercați din nou.' },
            500: { title: 'Ceva nu a funcționat', text: 'Știm deja despre problemă și lucrăm la ea.' },
            503: { title: 'Lucrări tehnice', text: 'Site-ul revine în câteva minute.' },
        },
        en: {
            404: { title: 'Page not found', text: 'The address may have changed or the page may have been removed.' },
            403: { title: 'Access restricted', text: 'You do not have rights for this page.' },
            419: { title: 'Session expired', text: 'Reload the page and try again.' },
            429: { title: 'Too many requests', text: 'Please wait a moment and try again.' },
            500: { title: 'Something went wrong', text: 'We already know about the problem and are working on it.' },
            503: { title: 'Maintenance', text: 'The site will be back in a few minutes.' },
        },
        ru: {
            404: { title: 'Страница не найдена', text: 'Возможно, адрес изменился или страница была удалена.' },
            403: { title: 'Доступ закрыт', text: 'У вас нет прав на эту страницу.' },
            419: { title: 'Сессия истекла', text: 'Обновите страницу и попробуйте снова.' },
            429: { title: 'Слишком много запросов', text: 'Подождите немного и попробуйте снова.' },
            500: { title: 'Что-то пошло не так', text: 'Мы уже знаем о проблеме и работаем над ней.' },
            503: { title: 'Технические работы', text: 'Сайт вернётся через несколько минут.' },
        },
    }

    const byLocale = texts[props.locale] ?? texts.ro
    const fallback = props.status >= 500 ? 500 : 404

    return byLocale[props.status] ?? byLocale[fallback]
})

const backLabel = computed(() => ({ ro: 'Spre pagina principală', en: 'Go to homepage', ru: 'На главную' })[props.locale] ?? 'Acasă')
</script>

<template>
  <Head :title="`${props.status} — ${copy.title}`" />

  <div class="on-ink relative flex min-h-screen flex-col bg-ink-950 text-white" style="background: var(--gradient-ink)">

    <header class="container-site relative flex h-[var(--header-height)] shrink-0 items-center">
      <a :href="props.home" aria-label="GHEKATEX">
        <img src="/brand/logo_horizontal_white.svg" alt="GHEKATEX" class="h-5 w-auto md:h-6" width="180" height="30">
      </a>
    </header>

    <main class="container-site relative flex flex-1 items-center py-16">
      <div class="grid w-full items-center gap-10 lg:grid-cols-12">
        <div class="lg:col-span-7">
          <p class="font-[family-name:var(--font-display)] text-[6rem] font-semibold leading-none text-accent-400 lg:text-[9rem]">
            {{ props.status }}
          </p>

          <h1 class="heading-1 mt-4 text-white">{{ copy.title }}</h1>

          <p class="lead mt-5">{{ copy.text }}</p>

          <a :href="props.home" class="btn btn-outline mt-9">
            <ArrowLeft :size="16" />
            {{ backLabel }}
          </a>
        </div>

        <div class="hidden justify-end lg:col-span-5 lg:flex">
          <BrandMark :size="220" />
        </div>
      </div>
    </main>

    <footer class="container-site relative pb-8">
      <p class="text-xs text-white/35">Ghekatex Group SRL</p>
    </footer>
  </div>
</template>
