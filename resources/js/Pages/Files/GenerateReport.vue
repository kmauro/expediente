<script setup>
import { Button } from '@/Components/ui/button';
import Input from '@/Components/ui/input/Input.vue';
import { Label } from '@/Components/ui/label';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { CalendarSearch, Download, LoaderCircle } from 'lucide-vue-next';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    title: String,
    date: {
        type: String,
        required: true,
    },
    records: {
        type: Array,
        default: () => [],
    },
    hasSearched: {
        type: Boolean,
        default: false,
    },
    searchUrl: {
        type: String,
        required: true,
    },
    downloadUrl: {
        type: String,
        default: null,
    },
});

const form = useForm({
    date: props.date,
});

function submit() {
    form.get(props.searchUrl, {
        preserveScroll: true,
        replace: true,
    });
}
</script>

<template>
    <div class="space-y-6">
        <NotificationToast />

        <section aria-labelledby="report-filters-title" class="rounded-lg border bg-card p-4 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-1">
                    <h2 id="report-filters-title" class="text-lg font-semibold">Reporte por fecha</h2>
                    <p class="text-sm text-muted-foreground">
                        Consulta los expedientes actualizados en una fecha y descarga el resultado en Excel.
                    </p>
                </div>

                <form class="flex flex-col gap-3 sm:flex-row sm:items-end" :aria-busy="form.processing" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label for="report-date">Seleccione una fecha</Label>
                        <Input
                            id="report-date"
                            v-model="form.date"
                            class="w-full sm:w-44"
                            type="date"
                            :aria-invalid="Boolean(form.errors.date)"
                        />
                        <p v-if="form.errors.date" class="text-sm text-destructive">{{ form.errors.date }}</p>
                    </div>

                    <Button type="submit" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="size-4 animate-spin" aria-hidden="true" />
                        <CalendarSearch v-else class="size-4" aria-hidden="true" />
                        {{ form.processing ? 'Buscando...' : 'Buscar' }}
                    </Button>
                </form>
            </div>
        </section>

        <section class="space-y-4" aria-live="polite">
            <div v-if="hasSearched" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-muted-foreground">
                    {{ records.length }} {{ records.length === 1 ? 'expediente encontrado' : 'expedientes encontrados' }}
                </p>

                <Button v-if="downloadUrl" as-child>
                    <a :href="downloadUrl">
                        <Download class="size-4" aria-hidden="true" />
                        Descargar reporte
                    </a>
                </Button>
            </div>

            <div v-if="!hasSearched" class="rounded-lg border border-dashed py-12 text-center">
                <CalendarSearch class="mx-auto size-10 text-muted-foreground" aria-hidden="true" />
                <p class="mt-3 font-medium">Selecciona una fecha para consultar expedientes.</p>
            </div>

            <div v-else-if="records.length === 0" class="rounded-lg border border-dashed py-12 text-center">
                <p class="font-medium">No se encontraron expedientes para la fecha seleccionada.</p>
                <p class="mt-1 text-sm text-muted-foreground">Prueba con otra fecha.</p>
            </div>

            <div v-else class="overflow-hidden rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="min-w-56">Número</TableHead>
                            <TableHead class="min-w-80">Título</TableHead>
                            <TableHead class="min-w-48">Objeto</TableHead>
                            <TableHead class="min-w-56">Proveedor/programa</TableHead>
                            <TableHead class="min-w-80">Último movimiento</TableHead>
                            <TableHead class="min-w-96">Movimientos</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="record in records" :key="record.id">
                            <TableCell class="whitespace-normal align-top">{{ record.number }}</TableCell>
                            <TableCell class="whitespace-normal align-top">{{ record.title }}</TableCell>
                            <TableCell class="whitespace-normal align-top">{{ record.type }}</TableCell>
                            <TableCell class="whitespace-normal align-top">{{ record.counterparty }}</TableCell>
                            <TableCell class="whitespace-normal align-top">{{ record.last_movement }}</TableCell>
                            <TableCell class="whitespace-normal align-top">
                                <ul class="space-y-2">
                                    <li v-for="movement in record.history" :key="movement.id" class="text-sm text-muted-foreground">
                                        {{ movement.moved_at }} enviado a {{ movement.area_name }}: {{ movement.associated_doc }}
                                    </li>
                                </ul>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </section>
    </div>
</template>
