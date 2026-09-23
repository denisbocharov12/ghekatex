<script setup lang="ts">
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useStorage } from '@vueuse/core'
import AdminSidebar from '@admin/Components/ui/AdminSidebar.vue'
import AdminTopbar from '@admin/Components/ui/AdminTopbar.vue'
import FlashToast from '@admin/Components/ui/FlashToast.vue'

// Состояние меню запоминается: редактор не разворачивает его на каждой странице
const sidebarOpen = useStorage('ghekatex.admin.sidebar', true)
const mobileOpen = ref(false)

const page = usePage()
const flash = computed(() => page.props.flash as { success?: string; error?: string })
</script>

<template>
  <div class="min-h-screen">
    <AdminSidebar :open="sidebarOpen" :mobile-open="mobileOpen" @close-mobile="mobileOpen = false" />

    <div :class="['flex min-h-screen flex-col transition-all duration-300', sidebarOpen ? 'lg:ml-64' : 'lg:ml-16']">
      <AdminTopbar
        :sidebar-open="sidebarOpen"
        @toggle-sidebar="sidebarOpen = !sidebarOpen"
        @toggle-mobile="mobileOpen = true"
      />

      <main class="flex-1 p-4 lg:p-6">
        <slot />
      </main>
    </div>

    <FlashToast v-if="flash?.success" type="success" :message="flash.success" />
    <FlashToast v-if="flash?.error" type="error" :message="flash.error" />
  </div>
</template>
