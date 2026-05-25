<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { getStateClasses } from '@/Constants/ui';
import { Input } from '@/Components/ui/input';
import { Label } from 'reka-ui';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';


defineOptions({
  layout: AppLayout,
})


const props = defineProps({
    title: String,
    data: Object,
    states: Object
})
const records = props.data

</script>

<template>
        <NotificationToast />
        <section class="flex items-center justify-between gap-4 px-6 py-3">
            <div class=" flex items-center gap-2">
                <span v-for="state in states" class=" text-xs pl-3 pr-3 rounded-xl border-1 inset-shadow-sm/18" :class="getStateClasses(state.color)">
                    {{ state.name }}
                </span>
            </div>
            <div class="">
                <Label for="search" >Buscador: </Label>
                <Input
                id="search"
                placeholder="Buscar... "
                />
            </div>

        </section>


    <section v-for="record in records" class="border p-4 rounded-lg shadow-md flex justify-between mb-4" :class="getStateClasses(record.color)" >
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
