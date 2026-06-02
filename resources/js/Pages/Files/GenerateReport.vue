<script setup>
import Input from '@/Components/ui/input/Input.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/Components/ui/table';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';
import { ref } from 'vue';

defineOptions({
    layout: AppLayout,
})

const props = defineProps({
    title: String,
    date: String,
    records: Object,
})

const form = useForm({
    date: props.date,
})

const isDownloading = ref(false);

function submit() {
    form.post('/report/search', {
        preserveState: true,
        onSuccess: () => {

        },
        onError: () => {
        }
    })
}

async function download() {
    isDownloading.value = true;

    try {
        const response = await fetch('/report/download', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                records: props.records,
            }),
        });

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');

        link.href = url;
        link.download = 'reporte_expedientes.xlsx';
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    }finally {
        isDownloading.value = false;
    }
}
</script>

<template>
<NotificationToast />
<div class="space-y-2">
    <Label>Seleccione fecha</Label>
    <form @submit.prevent="submit" class="space-x-2 flex">
        <Input type="date" v-model="form.date" class="w-40"/>
        <Button type="submit">Buscar</Button>
    </form>
</div>
<section>


    <div v-if="props.records.length === 0" class="text-center py-10">
        <p class="text-muted-foreground">No se encontraron expedientes para la fecha seleccionada.</p>
    </div>
    <div v-else>
        <Table>
        <TableHeader>
            <TableRow>
                <TableHead>Numero</TableHead>
                <TableHead>Titulo</TableHead>
                <TableHead>Objeto</TableHead>
                <TableHead>Proveedor/programa</TableHead>
                <TableHead>Último movimiento</TableHead>
                <TableHead>Movimientos</TableHead>

            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="record in props.records" :key="record.id">
                <TableCell>{{ record.number }}</TableCell>
                <TableCell>{{ record.title }}</TableCell>
                <TableCell>{{ record.type }}</TableCell>
                <TableCell>{{ record.counterparty}}</TableCell>
                <TableCell>{{ record.last_movement}}</TableCell>
                <TableCell>
                    <div v-for="pase in record.history" :key="pase.id">
                    <span class="ml-4 mt-2 list-disc text-sm text-muted-foreground">
                        {{ pase.moved_at + ' enviado a '+ pase.area_name + ': ' + pase.associated_doc }}
                    </span>
                    </div>
            </TableCell>
            </TableRow>
        </TableBody>
        </Table>

            <Button @click="download" :disabled="isDownloading">Descargar reporte</Button>

    </div>

</section>

</template>
