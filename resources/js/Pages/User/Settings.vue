<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { computed, ref } from 'vue';
import Input from '@/Components/ui/input/Input.vue';
import Label from '@/Components/ui/label/Label.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import NotificationToast from '@/Components/NotificationToast.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({
    layout: AppLayout,
})

const stateClasses = {
    default:``,
    green: `
        bg-green-100 text-green-800
        dark:bg-green-900 dark:text-green-200
    `,

    yellow: `
        bg-yellow-100 text-yellow-800
        dark:bg-yellow-700 dark:text-yellow-200
    `,

    red: `
        bg-red-100 text-red-800
        dark:bg-red-900 dark:text-red-200

    `,
    violet: `
        bg-violet-100 text-violet-800
        dark:bg-violet-900 dark:text-violet-200
    `,
    blue: `
        bg-blue-100 text-blue-800
        dark:bg-blue-900 dark:text-blue-200
    `,
    orange: `
        bg-orange-100 text-orange-800
        dark:bg-orange-900 dark:text-orange-200
    `,
    teal: `
        bg-teal-100 text-teal-800
        dark:bg-teal-900 dark:text-teal-200
    `,
    fuchsia: `
        bg-fuchsia-100 text-fuchsia-800
        dark:bg-fuchsia-900 dark:text-fuchsia-200
    `,
    stone: `
        bg-stone-200 text-stone-700
        dark:bg-stone-800 dark:text-stone-300
    `,
    slate: `
        bg-slate-200 text-slate-700
        dark:bg-slate-800 dark:text-slate-200
    `,
    mist: `
        bg-mist-200 text-mist-800
        dark:bg-mist-900 dark:text-mist-200
    `,

}

const showMessage = ref(false);
const page = usePage();
const successMessage = computed(() => page.props.flash?.success ?? null);
const errorMessage = computed(() => page.props.flash?.error ?? null);

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



function stateSubmit() {
    stateForm.post('/config/addState', {
        onSuccess: () => {
            stateForm.name = null;
            stateForm.color = null;
            show();
        },
    });
}

function areaSubmit() {
    areaForm.post('/config/addArea', {
        onSuccess: () => {
            areaForm.name = null;
            show();
        },
    });
}

function typeSubmit() {
    typeForm.post('/config/addType', {
        onSuccess: () => {
            typeForm.name = null;
            show();
        },
    });
}

function counterPartySubmit() {
    counterpartyForm.post('/config/addCounterparty', {
        onSuccess: () => {
            counterpartyForm.name = null;
            show();
        },
    });
}

function show(){
    showMessage.value = true;
    setTimeout(() => {
        showMessage.value = false;
    }, 3000);
}

</script>


<template>
    <NotificationToast :show="showMessage" :success="successMessage" :error="errorMessage" />

    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Configuración</h1>
        <p>Aquí puedes configurar tus preferencias y ajustes de la cuenta.</p>

    </div>
    <form @submit.prevent="areaSubmit" class="border p-4 bg-card space-y-2 rounded-lg shadow-md bg-background mt-4">
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

    <form @submit.prevent="typeSubmit" class="border p-4 bg-card space-y-2 rounded-lg shadow-md bg-background mt-4">
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

    <form @submit.prevent="counterPartySubmit" class="border p-4 bg-card space-y-2 rounded-lg shadow-md bg-background mt-4">
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


    <form @submit.prevent="stateSubmit" class="border p-4 bg-card rounded-lg shadow-md bg-background mt-4 space-y-2">
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
                    <SelectItem value="default">
                        Predeterminado
                    </SelectItem>
                    <SelectItem value="green">
                        Verde
                    </SelectItem>
                    <SelectItem value="yellow">
                        Amarillo
                    </SelectItem>
                    <SelectItem value="red">
                        Rojo
                    </SelectItem>
                    <SelectItem value="violet">
                        Violeta
                    </SelectItem>
                    <SelectItem value="blue">
                        Azul
                    </SelectItem>
                    <SelectItem value="orange">
                        Naranja
                    </SelectItem>
                    <SelectItem value="teal">
                        Verde agua
                    </SelectItem>
                    <SelectItem value="fuchsia">
                        Fucsia
                    </SelectItem>
                    <SelectItem value="stone">
                        Gris
                    </SelectItem>
                    <SelectItem value="slate">
                        Pizarra
                    </SelectItem>
                    <SelectItem value="mist">
                        Gris
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="stateForm.errors.color" class="text-sm text-destructive">{{ stateForm.errors.color }}</p>
        </div>
        <span>Vista previa:</span>
        <section class="border p-4 rounded-lg shadow-md flex justify-between mb-4" :class="stateClasses[stateForm.color]">
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
