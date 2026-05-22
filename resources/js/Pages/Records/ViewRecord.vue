<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Edit, CircleXIcon } from 'lucide-vue-next';
import Input from '@/Components/ui/input/Input.vue';
import Button from '@/Components/ui/button/Button.vue';
import NotificationToast from '@/Components/NotificationToast.vue';
import Label from '@/Components/ui/label/Label.vue';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select';


defineOptions({
    layout : AppLayout,
})

const props = defineProps({
    title: String,
    record: Object,
    date: String,
    history: Array,
})
const record = props.record


//mocked data
const objects = [{
    id : 1,
    name : "Contencioso",
},{
    id : 2,
    name : "Rendiciones",
},{
    id : 3,
    name : "Legítimo",
}]

const states = [{
    id : 1,
    name : "En buzón",
},{
    id : 2,
    name : "Enviado",
},{
    id : 3,
    name : "Pendiente",
},{
    id : 4,
    name : "Terminado",
}]

const counterparties = [{
    id : 1,
    name : "Pampa Azul",
},{
    id : 2,
    name : "Innova-T",
},{
    id : 3,
    name : "Otro",
}]

const areas = [{
    id : 1,
    name : "Rendiciones",
},{
    id : 2,
    name : "Despacho",
},{
    id : 3,
    name : "Contables",
},{
    id : 4,
    name : "Contencioso",
}]

//end mocked data

const page = usePage();
const successMessage = computed(() => page.props.flash.success);
const errorMessage = computed(() => page.props.flash.error);
const showMessage = ref(false);

const isEditing = ref(false);

const last_movement = record.updated_at + ' - Enviado a ' + areas[record.last_area_id].name;

const recordForm = useForm({
    id: record.id,
    number: record.number,
    title: record.title,
    state: record.state_id,
    date: record.date,
    pases: record.pases,
    last_movement: last_movement,
    object: record.type_id,
    counterparty: record.counterparty_id,
    destination: null,

})

function submit() {
  recordForm.post('/record/edit', {
    onFinish: show,
  })
}

function show(){
    showMessage.value = true;
    isEditing.value = false;
    setTimeout(() => {
        showMessage.value = false;
    }, 3000);
}

</script>

<template>
    <NotificationToast :show="showMessage" :success="successMessage" :error="errorMessage"/>
    <section class="border p-4 rounded-lg shadow-md bg-background">
        <div class="flex items-center justify-between mb-4">
            <div class="mb-6 space-y-2">
              <h2 class="text-2xl font-semibold">{{ record.number }}</h2>
              <p class="text-sm text-muted-foreground">Fecha de creación: {{ record.date }}</p>
            </div>

            <Button variant="outline" @click="isEditing = !isEditing" class="ml-auto"
            :class="{
                'bg-primary text-white hover:bg-primary/10' : !isEditing,
                'bg-secondary text-primary hover:bg-secondary/50' : isEditing,

            }">
            <div v-if="!isEditing" class="flex items-center">
                <Edit class="size-4" />
                <p class="ml-2">Editar expediente</p>
            </div>
            <div v-else class="flex items-center">
                <CircleXIcon class="size-4"/>
                <p class="ml-2">Cancelar</p>
            </div>
            </Button>
        </div>

        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <Input
            type="number"
            id="id"
            v-model="recordForm.id"
            disabled
            hidden
            />

            <Label for="title">Titulo de expediente:</Label>
            <Input
            type="text"
            id="title"
            v-model="recordForm.title"
            :disabled = "!isEditing"
            :aria-invalid="Boolean(recordForm.errors.title)"
            />
            <p v-if="recordForm.errors.title" class="text-sm text-destructive">
                {{ recordForm.errors.title }}
            </p>

            <Label for="state">Estado:</Label>
            <Select id="state" v-model="recordForm.state" :disabled="!isEditing">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un estado" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="state in states" :key="state.id" :value="state.id">
                        {{ state.name }}
                    </SelectItem>
                </SelectContent>
            </Select>

            <Label for="object">Objeto:</Label>
            <Select id="object" v-model="recordForm.object" :disabled="!isEditing">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un objeto" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="object in objects" :key="object.id" :value="object.id">
                        {{ object.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="recordForm.errors.object" class="text-sm text-destructive">
                {{ recordForm.errors.object }}
            </p>

            <Label for="counterparty">Proveedor/Programa:</Label>
            <Select id="counterparty" v-model="recordForm.counterparty" :disabled="!isEditing">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un proveedor o programa" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="counterparty in counterparties" :key="counterparty.id" :value="counterparty.id">
                        {{ counterparty.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="recordForm.errors.counterparty" class="text-sm text-destructive">
                {{ recordForm.errors.counterparty }}
            </p>

            <Label for="pases">Ultimo pase:</Label>
            <Input
            type="text"
            id="pases"
            v-model="recordForm.last_movement"
            disabled
            :aria-invalid="Boolean(recordForm.errors.last_movement)"
            />
            <p v-if="recordForm.errors.last_movement" class="text-sm text-destructive">
                {{ recordForm.errors.last_movement }}
            </p>

            <Button  :disabled="recordForm.processing" type="submit" v-if="isEditing">Actualizar expediente</Button>
    </form>



            <form  class="flex space-x-4 mt-5" @submit.prevent="submit">
                <div class="w-40 space-y-2">
                    <Label for="newDate">Fecha del nuevo pase:</Label>
                    <Input
                    type="date"
                    id="newDate"
                    v-model="props.date"
                    />
                </div>
                <div class="w-80 space-y-2">
                    <Label for="area">Area destinataria:</Label>
                    <Select id="area" v-model="recordForm.destination">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Selecciona un area" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="area in areas" :key="area.id" :value="area.name">
                            {{ area.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                </div>
                <div class="w-80 space-y-2">
                    <Label for="newPase">Descripción del nuevo pase:</Label>
                    <Input
                    type="text"
                    id="newPase"
                    v-model="recordForm.new_pase"
                    />
                </div>
                <Button class="mt-5" :disabled="recordForm.processing" type="submit" >Agregar nuevo pase</Button>
            </form>



            <Label class="mt-5" for="date">Historial de pases:</Label>
                <ul v-for="pase in history" :key="pase.id" class="ml-4 mt-2 list-disc text-sm text-muted-foreground">
                    <li>{{ pase.moved_at }} Enviado a {{ areas[pase.to_area_id].name }}: {{ pase.associated_doc }} <span v-if="pase.id === history.length-1" >(Actual)</span> </li>
                </ul>
    </section>
</template>
