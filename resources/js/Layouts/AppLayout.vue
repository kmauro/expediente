<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/Components/ui/Sidebar/Sidebar.vue'
import PageTitle from '@/Components/PageTitle.vue'
import { Head } from '@inertiajs/vue3'

const sidebarOpen = ref(true)

defineProps({
    title: String,
})

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

onMounted(() => {
  sidebarOpen.value = window.innerWidth >= 1024
})
</script>

<template>
    <Head :title="title" />
    <div class="app-container">
        <div class="main-container">
            <Transition name="sidebar-collapse">
                <div v-show="sidebarOpen" class="sidebar-wrapper">
                    <Sidebar :sidebar-open="sidebarOpen" @toggle-sidebar="toggleSidebar" />
                </div>
            </Transition>

                <main :class="sidebarOpen ? 'ml-80' : ''" class="flex-1 flex flex-col">
                <PageTitle @toggle-sidebar="toggleSidebar" :title="title">
                    {{ title }}
                </PageTitle>

                <div class="flex-1 overflow-y-auto p-6">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.app-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
}

.main-container {
    display: flex;
    flex: 1;
    overflow: hidden;
}

.sidebar-wrapper {
    transform-origin: left;
}

@media (max-width: 1023px) {
    .sidebar-wrapper {
        position: fixed;
        inset: 0;
        z-index: 50;
    }
}

@media (min-width: 1024px) {
    .sidebar-wrapper {
        position: fixed;
        left: 0;
        top: 0;
        height: 100%;
        z-index: 40;
    }
}

/* Animación de colapso de sidebar */
.sidebar-collapse-enter-active,
.sidebar-collapse-leave-active {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.sidebar-collapse-enter-from,
.sidebar-collapse-leave-to {
    transform: scaleX(0);
    opacity: 0;
}

.sidebar-collapse-enter-to,
.sidebar-collapse-leave-from {
    transform: scaleX(1);
    opacity: 1;
}

/* Animación de expansión del contenido */
.content-expand-enter-active,
.content-expand-leave-active {
    transition: margin-left 0.3s ease;
}
.content-expand-enter-from,
.content-expand-leave-to {
    margin-left: 0;
}
.content-expand-enter-to,
.content-expand-leave-from {
    margin-left: 20rem; /* Ancho del sidebar */
}

main {
    transition: margin-left 0.3s ease;
}
</style>
