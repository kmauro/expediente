<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';


defineOptions({
  layout: AppLayout,
})


const props = defineProps({
    title: String,
    data: Object
})
const records = props.data

const stateClasses = {
    ok: `
        bg-green-100 text-green-800
        dark:bg-green-900 dark:text-green-200
    `,

    ok: `
        bg-yellow-100 text-yellow-800
        dark:bg-yellow-900 dark:text-yellow-200
    `,

    ok: `
        bg-red-100 text-red-800
        dark:bg-red-900 dark:text-red-200

    `,
    ok: `
        bg-violet-100 text-violet-800
        dark:bg-violet-900 dark:text-violet-200
    `,
    ok: `
        bg-blue-100 text-blue-800
        dark:bg-blue-900 dark:text-blue-200
    `,
    ok: `
        bg-orange-100 text-orange-800
        dark:bg-orange-900 dark:text-orange-200
    `,
    ok: `
        bg-teal-100 text-teal-800
        dark:bg-teal-900 dark:text-teal-200
    `,
    ok: `
        bg-fuchsia-100 text-fuchsia-800
        dark:bg-fuchsia-900 dark:text-fuchsia-200
    `,
    c: `
        bg-stone-200 text-stone-700
        dark:bg-stone-800 dark:text-stone-300
    `,
    success: `
        bg-slate-200 text-slate-700
        dark:bg-slate-800 dark:text-slate-200
    `,
    ok: `
        bg-red-100 text-red-800
        dark:bg-red-900 dark:text-red-200
    `,

}

</script>

<template>

    <span class="text-lg font-semibold">Expedientes</span>


    <section v-for="record in records" class="border p-4 rounded-lg shadow-md flex justify-between mb-4" :class="stateClasses['success']">
        <div class="space-y-2">
            <h2 class="text-2xl font-semibold">{{ record.title }}</h2>
            <div class="max-w-2xl text-sm ">
                <span>{{ record.number }}</span>
            </div>
        </div>
        <div>
            <Link
            view-transition
            :href="`/record/${record.id}`"
            class="flex items-center bg-primary text-white font-semibold hover:bg-primary/10 gap-2 rounded-lg px-3 py-2 text-sm text-sidebar-foreground"
        >
            <span>Ver mas</span>
        </Link>
        </div>
    </section>
</template>
