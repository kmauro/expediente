<script setup>
import Footer from '@/Components/ui/footer/Footer.vue'
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue'
import PageTitle from '@/Components/ui/page-title/PageTitle.vue'
import Header from '@/Components/ui//header/Header.vue'
import Sidebar from '@/Components/ui/Sidebar/Sidebar.vue'
import { Head } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

defineProps({
  title: String,
})

const sidebarOpen = ref(false)

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}

onMounted(() => {
  sidebarOpen.value = window.innerWidth >= 1024
})
</script>

<template>
  <Head :title="title" />
  <div class="flex h-dvh flex-col bg-background text-foreground">
    <Header class="shrink-0" />

    <div class="relative flex min-h-0 flex-1 overflow-hidden">
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="-translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="-translate-x-full opacity-0"
      >
        <div v-show="sidebarOpen" class="absolute inset-y-0 left-0 z-40 lg:block">
          <Sidebar :sidebar-open="sidebarOpen" @toggle-sidebar="toggleSidebar" />
        </div>
      </Transition>

      <button
        v-if="sidebarOpen"
        type="button"
        class="absolute inset-0 z-30 bg-black/40 lg:hidden"
        aria-label="Cerrar navegación"
        @click="toggleSidebar"
      />

      <main
        class="flex min-w-0 flex-1 flex-col transition-[margin] duration-300"
        :class="sidebarOpen ? 'lg:ml-80' : 'ml-0'"
      >
        <PageTitle :title="title" @toggle-sidebar="toggleSidebar">
          {{ title }}
        </PageTitle>

        <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
          <div class="mx-auto w-full max-w-7xl">
            <slot />
          </div>
        </div>
      </main>
    </div>

    <Footer class="shrink-0" />
    <NotificationToast />
  </div>
</template>
