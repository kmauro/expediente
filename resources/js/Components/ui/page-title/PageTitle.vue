<script setup>
import { ref } from 'vue'
import { ChevronLeftSquare } from 'lucide-vue-next'
import ThemeToggle from '@/Components/ui/theme-toggle/ThemeToggle.vue';

const isRotated = ref(false)

defineEmits(['toggle-sidebar'])

const toggleRotation = () => {
  isRotated.value = !isRotated.value
}
</script>

<template>
  <div class="border-b border-border bg-background py-2">
    <div class="flex items-center justify-between gap-4 px-6 py-3">
      <div class="flex items-center gap-4">
        <button
          type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-primary/10 hover:text-foreground"
          @click="toggleRotation(); $emit('toggle-sidebar')"
        >
        <ChevronLeftSquare :class="isRotated ? 'transform rotate-180 transition-transform duration-300' : 'transition-transform duration-300'" />
        </button>
        <div class="text-lg font-semibold">
          <slot />
        </div>
      </div>
      <ThemeToggle />
    </div>

  </div>
</template>
