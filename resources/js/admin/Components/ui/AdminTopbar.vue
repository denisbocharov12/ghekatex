<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { onClickOutside } from '@vueuse/core'
import { Menu, PanelLeftClose, PanelLeftOpen, ExternalLink, ChevronDown, LogOut, User } from 'lucide-vue-next'

defineProps<{ sidebarOpen: boolean }>()
defineEmits<{ toggleSidebar: []; toggleMobile: [] }>()

const page = usePage()
const menuOpen = ref(false)
const menuRoot = ref<HTMLElement | null>(null)

onClickOutside(menuRoot, () => (menuOpen.value = false))

const user = computed(() => page.props.auth?.user as { name: string; email: string; avatar: string | null; position: string | null } | null)

function logout(): void {
    router.post(route('admin.logout'))
}
</script>

<template>
  <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-steel-200 bg-white px-4 lg:px-6">
    <button
      type="button"
      class="btn btn-ghost -ml-2 h-10 w-10 p-0 lg:hidden"
      :aria-label="$t('app.title')"
      @click="$emit('toggleMobile')"
    >
      <Menu :size="20" />
    </button>

    <button
      type="button"
      class="btn btn-ghost -ml-2 hidden h-10 w-10 p-0 lg:inline-flex"
      :aria-label="sidebarOpen ? $t('app.collapse') : $t('app.expand')"
      @click="$emit('toggleSidebar')"
    >
      <PanelLeftClose v-if="sidebarOpen" :size="18" />
      <PanelLeftOpen v-else :size="18" />
    </button>

    <div class="ml-auto flex items-center gap-2">
      <a
        :href="`/${page.props.locales?.default ?? 'ro'}`"
        target="_blank"
        rel="noopener"
        class="btn btn-ghost hidden sm:inline-flex"
      >
        <ExternalLink :size="15" />
        {{ $t('app.site') }}
      </a>

      <div ref="menuRoot" class="relative">
        <button type="button" class="btn btn-ghost gap-2.5 pl-1.5" @click="menuOpen = !menuOpen">
          <span class="inline-flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-primary-100 text-primary-800">
            <img v-if="user?.avatar" :src="user.avatar" :alt="user.name" class="h-full w-full object-cover">
            <User v-else :size="15" />
          </span>
          <span class="hidden text-left sm:block">
            <span class="block text-sm font-semibold leading-tight text-steel-800">{{ user?.name }}</span>
            <span v-if="user?.position" class="block text-xs leading-tight text-steel-400">{{ user.position }}</span>
          </span>
          <ChevronDown :size="15" class="text-steel-400" />
        </button>

        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="opacity-0 scale-95"
          leave-active-class="transition duration-100 ease-in"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="menuOpen"
            class="absolute right-0 top-full mt-1 w-56 origin-top-right rounded-lg border border-steel-200 bg-white py-1 shadow-lg"
          >
            <p class="border-b border-steel-100 px-4 py-2.5 text-xs text-steel-400">{{ user?.email }}</p>

            <Link :href="route('admin.profile.edit')" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-steel-700 hover:bg-steel-50">
              <User :size="15" />
              {{ $t('app.profile') }}
            </Link>

            <button
              type="button"
              class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-[color:var(--color-danger)] hover:bg-steel-50"
              @click="logout"
            >
              <LogOut :size="15" />
              {{ $t('app.logout') }}
            </button>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>
