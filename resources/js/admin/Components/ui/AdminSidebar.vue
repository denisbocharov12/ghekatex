<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    LayoutDashboard, Images, Sparkles, History, Factory, Award, Handshake,
    FolderTree, Shirt, Layers, Scissors, Wrench, Tags, Newspaper, GalleryHorizontal,
    FileText, MapPin, Inbox, Menu as MenuIcon, Settings, Languages, Shuffle,
    Users, ShieldCheck, ScrollText, HelpCircle, X,
} from 'lucide-vue-next'
import { usePermissions } from '@admin/Composables/usePermissions'

defineProps<{ open: boolean; mobileOpen: boolean }>()
defineEmits<{ closeMobile: [] }>()

const page = usePage()
const { can } = usePermissions()

/** Разделы панели. Пункт скрывается, если у пользователя нет права на просмотр. */
const groups = [
    {
        key: 'overview',
        items: [{ key: 'dashboard', route: 'admin.dashboard', icon: LayoutDashboard, permission: 'dashboard.view' }],
    },
    {
        key: 'home',
        items: [
            { key: 'hero-slides', route: 'admin.hero-slides.index', icon: Images, permission: 'hero-slides.view' },
            { key: 'advantages', route: 'admin.advantages.index', icon: Sparkles, permission: 'advantages.view' },
        ],
    },
    {
        key: 'company',
        items: [
            { key: 'pages', route: 'admin.pages.index', icon: FileText, permission: 'pages.view' },
            { key: 'milestones', route: 'admin.milestones.index', icon: History, permission: 'milestones.view' },
            { key: 'facilities', route: 'admin.facilities.index', icon: Factory, permission: 'facilities.view' },
            { key: 'certificates', route: 'admin.certificates.index', icon: Award, permission: 'certificates.view' },
            { key: 'partners', route: 'admin.partners.index', icon: Handshake, permission: 'partners.view' },
        ],
    },
    {
        key: 'catalog',
        items: [
            { key: 'product-categories', route: 'admin.product-categories.index', icon: FolderTree, permission: 'product-categories.view' },
            { key: 'products', route: 'admin.products.index', icon: Shirt, permission: 'products.view' },
            { key: 'fabrics', route: 'admin.fabrics.index', icon: Layers, permission: 'fabrics.view' },
            { key: 'treatments', route: 'admin.treatments.index', icon: Scissors, permission: 'treatments.view' },
        ],
    },
    {
        key: 'services',
        items: [
            { key: 'services', route: 'admin.services.index', icon: Wrench, permission: 'services.view' },
            { key: 'faqs', route: 'admin.faqs.index', icon: HelpCircle, permission: 'faqs.view' },
        ],
    },
    {
        key: 'content',
        items: [
            { key: 'post-categories', route: 'admin.post-categories.index', icon: Tags, permission: 'post-categories.view' },
            { key: 'posts', route: 'admin.posts.index', icon: Newspaper, permission: 'posts.view' },
            { key: 'media-albums', route: 'admin.media-albums.index', icon: GalleryHorizontal, permission: 'media.view' },
            { key: 'media-items', route: 'admin.media-items.index', icon: Images, permission: 'media.view' },
        ],
    },
    {
        key: 'communication',
        items: [
            { key: 'requests', route: 'admin.requests.index', icon: Inbox, permission: 'requests.view' },
            { key: 'offices', route: 'admin.offices.index', icon: MapPin, permission: 'offices.view' },
        ],
    },
    {
        key: 'system',
        items: [
            { key: 'navigation', route: 'admin.navigation.index', icon: MenuIcon, permission: 'navigation.view' },
            { key: 'settings', route: 'admin.settings.index', icon: Settings, permission: 'settings.view' },
            { key: 'translations', route: 'admin.translations.index', icon: Languages, permission: 'translations.view' },
            { key: 'redirects', route: 'admin.redirects.index', icon: Shuffle, permission: 'redirects.view' },
            { key: 'users', route: 'admin.users.index', icon: Users, permission: 'users.view' },
            { key: 'roles', route: 'admin.roles.index', icon: ShieldCheck, permission: 'roles.view' },
            { key: 'activity-log', route: 'admin.activity-log.index', icon: ScrollText, permission: 'activity-log.view' },
        ],
    },
]

const visibleGroups = computed(() =>
    groups
        .map((group) => ({ ...group, items: group.items.filter((item) => can(item.permission)) }))
        .filter((group) => group.items.length > 0),
)

function isCurrent(routeName: string): boolean {
    // Сравниваем по началу пути: страница формы должна подсвечивать свой раздел
    const target = new URL(route(routeName), window.location.origin).pathname

    return target === '/admin' ? page.url === '/admin' : page.url.startsWith(target)
}
</script>

<template>
  <div
    v-if="mobileOpen"
    class="fixed inset-0 z-40 bg-steel-900/50 lg:hidden"
    @click="$emit('closeMobile')"
  />

  <aside
    :class="[
      'fixed inset-y-0 left-0 z-50 flex flex-col bg-primary-950 text-white transition-all duration-300',
      open ? 'w-64' : 'w-16',
      mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
    ]"
  >
    <div class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-white/10 px-4">
      <Link :href="route('admin.dashboard')" class="flex items-center overflow-hidden">
        <img
          v-if="open"
          src="/brand/logo_horizontal_white.svg"
          alt="GHEKATEX"
          class="h-6 w-auto"
          width="160"
          height="27"
        >
        <img v-else src="/brand/icon_pattern.svg" alt="GHEKATEX" class="h-7 w-auto invert" width="24" height="32">
      </Link>

      <button
        type="button"
        class="text-white/60 hover:text-white lg:hidden"
        :aria-label="$t('action.close')"
        @click="$emit('closeMobile')"
      >
        <X :size="20" />
      </button>
    </div>

    <nav class="flex-1 overflow-y-auto py-4" :aria-label="$t('app.title')">
      <div v-for="group in visibleGroups" :key="group.key" class="mb-5">
        <p v-if="open" class="px-4 pb-2 text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-white/35">
          {{ $t(`group.${group.key}`) }}
        </p>

        <ul>
          <li v-for="item in group.items" :key="item.key">
            <Link
              :href="route(item.route)"
              :class="[
                'flex items-center gap-3 px-4 py-2.5 text-sm transition-colors',
                isCurrent(item.route)
                  ? 'bg-white/10 text-white font-medium border-l-2 border-accent-400'
                  : 'text-white/65 hover:bg-white/5 hover:text-white border-l-2 border-transparent',
              ]"
              :title="open ? undefined : $t(`nav.${item.key}`)"
            >
              <component :is="item.icon" :size="17" class="shrink-0" />
              <span v-if="open" class="truncate">{{ $t(`nav.${item.key}`) }}</span>
            </Link>
          </li>
        </ul>
      </div>
    </nav>
  </aside>
</template>
