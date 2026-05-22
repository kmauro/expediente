<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import Input from '@/Components/ui/input/Input.vue';
import Label from '@/Components/ui/label/Label.vue';
import { useForm } from '@inertiajs/vue3';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Link } from '@inertiajs/vue3';
import { stateClasses, getStateClasses } from '@/Constants/ui';

defineOptions({
    layout: AppLayout,
})

const stateForm = useForm({
    name: null,
    color: null
})
const areaForm = useForm({
    name: null,
})
const typeForm = useForm({
    name: null,
})
const counterpartyForm = useForm({
    name: null,
})

function submit(form, route) {
    form.post(route, {
        onSuccess: () => {
            form.name = null;
        },
    });
}


</script>


<template>
    <NotificationToast />

    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Configuración</h1>
        <p>Aquí puedes configurar tus preferencias y ajustes de la cuenta.</p>

    </div>
    <form @submit.prevent="submit(areaForm, '/config/addArea')" class="border p-4 bg-card space-y-2 rounded-lg shadow-md bg-background mt-4">
        <h2 class="text-lg font-semibold mb-2">Areas</h2>
        <Label for="areaName">Nombre del area:</Label>
        <Input
        id="areaName"
         placeholder="Area"
         v-model="areaForm.name"
         :aria-invalid="Boolean(areaForm.errors.name)"
          />
        <p v-if="areaForm.errors.name" class="text-sm text-destructive">
            {{ areaForm.errors.name }}
        </p>
        <Button type="submit" class="mt-2">Agregar</Button>
    </form>

    <form @submit.prevent="submit(typeForm, '/config/addType')" class="border p-4 bg-card space-y-2 rounded-lg shadow-md bg-background mt-4">
        <h2 class="text-lg font-semibold mb-2">Objetos</h2>
        <Label for="typeName">Nombre del Objeto:</Label>
        <Input
        id="typeName"
        placeholder="Objeto"
        v-model="typeForm.name"
        :aria-invalid="Boolean(typeForm.errors.name)"
         />
        <p v-if="typeForm.errors.name" class="text-sm text-destructive">
            {{ typeForm.errors.name }}
        </p>
        <Button type="submit" class="mt-2">Agregar</Button>
    </form>

    <form @submit.prevent="submit(counterpartyForm, '/config/addCounterparty')" class="border p-4 bg-card space-y-2 rounded-lg shadow-md bg-background mt-4">
        <h2 class="text-lg font-semibold mb-2">Proveedor/Programa</h2>
        <Label for="counterpartyName">Nombre del proveedor/programa:</Label>
        <Input
        id="counterpartyName"
        placeholder="proveedor/programa"
        v-model="counterpartyForm.name"
        :aria-invalid="Boolean(counterpartyForm.errors.name)"
        />
        <p v-if="counterpartyForm.errors.name" class="text-sm text-destructive">
            {{ counterpartyForm.errors.name }}
        </p>
        <Button type="submit" class="mt-2">Agregar</Button>
    </form>


    <form @submit.prevent="submit(stateForm, '/config/addState')" class="border p-4 bg-card rounded-lg shadow-md bg-background mt-4 space-y-2">
        <h2>Agregar Estado para expediente</h2>
        <Label for="stateName">Nombre:</Label>
        <Input
        id="stateName"
        v-model="stateForm.name"
        placeholder="Estado"
        :aria-invalid="Boolean(stateForm.errors.name)"
        />
        <p v-if="stateForm.errors.name" class="text-sm text-destructive">
            {{ stateForm.errors.name }}
        </p>
        <div class="w-60 mt-4">
            <Select id="object" :aria-invalid="Boolean(stateForm.errors.color)" v-model="stateForm.color">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un color" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                    v-for="state in stateClasses"
                    :key="state.value"
                    :value="state.value"
                    >
                    {{ state.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="stateForm.errors.color" class="text-sm text-destructive">{{ stateForm.errors.color }}</p>
        </div>
        <span>Vista previa:</span>
        <section class="border p-4 rounded-lg shadow-md flex justify-between mb-4" :class="getStateClasses(stateForm.color)">
            <div class="space-y-2">
                <h2 class="text-2xl font-semibold">Nombre del expediente</h2>
                <div class="max-w-2xl text-sm ">
                    <span>Numero del expediente</span>
                </div>
            </div>
            <div>
                <Link
                href="#"
                class="flex items-center bg-primary text-white font-semibold hover:bg-primary/10 gap-2 rounded-lg px-3 py-2 text-sm text-sidebar-foreground"
                >
                    <span>Ver mas</span>
                </Link>
            </div>
        </section>

        <Button type="submit" :disabled="stateForm.processing">Agregar estado</Button>
    </form>

</template>
