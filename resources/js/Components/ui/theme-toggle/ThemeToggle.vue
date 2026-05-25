<script setup>
import { Button } from '@/Components/ui/button'
import { useTheme } from '@/Composables/useTheme'
import { MoonIcon, SunIcon } from 'lucide-vue-next'

const { mode } = useTheme()

function toggleTheme() {
    mode.value = mode.value === 'dark'
        ? 'light'
        : 'dark'
}
</script>

<template>
    <Button variant="outline" @click="toggleTheme" class="ml-auto">
        <MoonIcon v-if="mode==='dark'" class="size-4" />
        <SunIcon v-else class="size-4" />
    </Button>
</template>
