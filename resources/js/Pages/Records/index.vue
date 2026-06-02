<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { getStateActive, getStateClasses } from '@/Constants/ui';
import { Input } from '@/Components/ui/input';
import { Label } from 'reka-ui';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';
import { watch, reactive } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';

defineOptions({
  layout: AppLayout,
})

const props = defineProps({
    title: String,
    records: Object,
    filters: Object,
    states: Object,
    fields: Array,
})


const filters = reactive({
    field: props.filters.field ?? 'number',
    search: props.filters.search ?? '',
    state: props.filters.state ?? null,
})

const reload = useDebounceFn(() => {
    router.get(
        '/records',
        filters,
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    )
}, 300)

watch(filters, reload, { deep: true })

const selectState = (state) => {
    filters.state = filters.state === state ? null : state
}

</script>

<template>
    <NotificationToast />
    <section class="flex justify-between gap-4 px-6 py-3">
        <div class=" grid grid-cols-5 gap-2">
            <button
            v-for="state in states"
            @click="selectState(state)"
            :key="state.id"
            class=" text-xs pl-3 pt-1 pb-1 pr-3 rounded-xl border-1 inset-shadow-sm/18"
            :class="
                filters.state === state ? getStateActive(state.color) : getStateClasses(state.color)"
            >
                {{ state.name }}
            </button>
        </div>
        <div class="flex gap-2">
            <Input
                v-model="filters.search"
                type="text"
                placeholder="Buscar..."
                class="border rounded px-3 py-2 w-full"
            />
            <Select id="field" v-model="filters.field" >
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un campo" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="field in fields" :key="field.name" :value="field.name">
                        {{ field.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
    </section>

    <div v-if="records.length === 0" class="flex items-center justify-center">
        <p class="text-muted-foreground">No se encontraron expedientes.</p>
    </div>
    <div v-else>
        <section v-for="record in records" class="border p-4 rounded-lg shadow-md flex justify-between mb-4" :class="getStateClasses(record.color)" >
            <div class="space-y-2">
                <h2 class="text-2xl font-semibold">{{ record.number }}</h2>
                <div class="max-w-2xl line-clamp-2 text-sm ">
                    <span>{{ record.title }}</span>
                </div>
            </div>
            <div>
                <Link
                view-transition
                :href="`/records/${record.id}`"
                class="flex items-center bg-primary text-white font-semibold hover:bg-primary/10 gap-2 rounded-lg px-3 py-2 text-sm text-sidebar-foreground"
            >
                <span>Ver mas</span>
            </Link>
            </div>
        </section>
    </div>
</template>
