<script setup>
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'


const page = usePage()

const show = ref(false)

const successMessage = computed(() => page.props.flash.success ?? null)
const errorMessage = computed(() => page.props.flash.error ?? null)
let timeout

watch(
    [successMessage, errorMessage],
    ([success, error]) => {
        if (success || error) {
            show.value = true

            clearTimeout(timeout)

            timeout = setTimeout(() => {
                show.value = false
            }, 3000)
        }
    },
    { immediate: true }
)
</script>

<template>
    <Transition
        enter-active-class="transition duration-300"
        enter-from-class="opacity-0 -translate-y-3"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-300"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-3"
    >
        <div
            v-if="show"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg"
            :class="{'bg-green-600' : successMessage, 'bg-red-600' : errorMessage}"
        >
            {{ successMessage || errorMessage }}
        </div>
    </Transition>
</template>
