<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { stateClasses, getStateClasses } from '@/Constants/ui';


defineOptions({
  layout: AppLayout,
})


const props = defineProps({
    title: String,
    data: Object
})
const records = props.data

</script>

<template>

    <span class="text-lg font-semibold">Expedientes</span>


    <section v-for="record in records" class="border p-4 rounded-lg shadow-md flex justify-between mb-4" :class="getStateClasses(record.color)">
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
