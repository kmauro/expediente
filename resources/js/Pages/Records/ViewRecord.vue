<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Edit, CircleXIcon } from 'lucide-vue-next';
import Input from '@/Components/ui/input/Input.vue';
import Button from '@/Components/ui/button/Button.vue';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';
import Label from '@/Components/ui/label/Label.vue';
import { Combobox } from '@/Components/ui/Combobox';
import { cn } from '@/lib/utils';
import Alert from '@/Components/ui/alert/Alert.vue';
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
    types: Object,
    states: Object,
    counterparties: Object,
    areas: Object,
})

const record = props.record

const isEditing = ref(false);

const lastMovement = props.history.find(movement => movement.id === record.last_movement_id)

const last_movement_label = movementLabel(lastMovement);

const recordForm = useForm({
    id: record.id,
    number: record.number,
    title: record.title,
    state_id: record.state_id,
    date: record.date,
    pases: record.pases,
    type_id: record.type_id,
    counterparty_id: record.counterparty_id,
})

const deleteForm = useForm({
    record_id:record.id
})

const movementForm = useForm({
    id: record.id,
    date: props.date,
    area_id: null,
    area_name: null,
    associated_doc : null,
})

function movementLabel(pase){
    return pase.moved_at + ' enviado a ' + props.areas[pase.to_area_id - 1].name + ': ' + pase.associated_doc;
}

function submitUpdate() {
    recordForm.post('/record/edit', {
        onSuccess: () => {
            isEditing.value = false
        },
    });
}

function submitMovement(){
    movementForm.post('/record/addmovement', {
        preserveState: false,
        onSuccess: () => {
            movementForm.id = record.id,
            movementForm.date = props.date,
            movementForm.area_id = null,
            movementForm.area_name = null,
            movementForm.associated_doc = null
        },
    });
}

function deleteRecord(){
    deleteForm.post('/record/delete');
}

</script>

<template>
    <NotificationToast />
    <section class="border p-4 rounded-lg shadow-md bg-background">
        <div class="flex items-center justify-between mb-4">
            <div class="mb-6 space-y-2">
              <h2 class="text-2xl font-semibold">{{ record.number }}</h2>
              <p class="text-sm text-muted-foreground">Fecha de creación: {{ record.created_at }}</p>
              <Alert v-if="isEditing"
                    @accept="deleteRecord"
                    :label="'Borrar expediente'"
                    :variant="'destructive'"
                    :title="'Eliminar expediente?'"
                    :description="'Esta acción no se puede deshacer. Eliminará la información de este expediente de forma permanente.'"
                    />
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

        <form @submit.prevent="submitUpdate" class="mt-4 space-y-4 w-full max-w-lg">
            <Input
            type="number"
            id="id"
            v-model="recordForm.id"
            hidden
            />

            <Label for="title">Titulo de expediente:</Label>
            <Input
            type="text"
            id="title"
            v-model="recordForm.title"
            :disabled = "!isEditing"
            :class="{ 'truncate' : !isEditing }"
            :aria-invalid="Boolean(recordForm.errors.title)"
            />
            <p v-if="recordForm.errors.title" class="text-sm text-destructive">
                {{ recordForm.errors.title }}
            </p>

            <Label for="state">Estado:</Label>
            <Select id="state" v-model="recordForm.state_id" :disabled="!isEditing">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un estado" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="state in states" :key="state.id" :value="state.id">
                        {{ state.name }}
                    </SelectItem>
                </SelectContent>
            </Select>

            <Label for="type">Objeto:</Label>
            <Select id="type" v-model="recordForm.type_id" :disabled="!isEditing">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un objeto" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="type in types" :key="type.id" :value="type.id">
                        {{ type.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="recordForm.errors.type_id" class="text-sm text-destructive">
                {{ recordForm.errors.type_id }}
            </p>

            <Label for="counterparty">Proveedor/Programa:</Label>
            <Select id="counterparty" v-model="recordForm.counterparty_id" :disabled="!isEditing">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un proveedor o programa" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="counterparty in counterparties" :key="counterparty.id" :value="counterparty.id">
                        {{ counterparty.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="recordForm.errors.counterparty_id" class="text-sm text-destructive">
                {{ recordForm.errors.counterparty_id }}
            </p>

            <Label for="pases">Ultimo pase:</Label>
            <span class="text-muted-foreground opacity-50 " :class="cn('selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-sm shadow-xs transition-[color,box-shadow] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 pt-2')">
            {{last_movement_label}}
            </span>
            <Button  :disabled="recordForm.processing" type="submit" v-if="isEditing">Actualizar expediente</Button>
        </form>

        <form  class="flex space-x-4 mt-5" @submit.prevent="submitMovement">
            <div class="w-40 space-y-2">
                <Label for="newDate">Fecha del nuevo pase:</Label>
                <Input
                type="date"
                id="newDate"
                v-model="movementForm.date"
                />
                <p v-if="movementForm.errors.date" class="text-sm text-destructive">
                {{ movementForm.errors.date }}
            </p>
            </div>

            <div class="w-80 space-y-2">
                <Label>Area destinataria:</Label>
                <Combobox
                id="area"
                v-model="movementForm.area_id"
                v-model:new-value="movementForm.area_name"
                :options="areas"
                />
                <p v-if="movementForm.errors.area_id" class="text-sm text-destructive">
                    {{ movementForm.errors.area_id }}
                </p>
                <p v-if="movementForm.errors.area_name" class="text-sm text-destructive">
                    {{ movementForm.errors.area_name }}
                </p>
            </div>
            <div class="w-80 space-y-2">
                <Label for="newPase">Descripción del nuevo pase:</Label>
                <Input
                type="text"
                id="newPase"
                v-model="movementForm.associated_doc"
                />
                <p v-if="movementForm.errors.associated_doc" class="text-sm text-destructive">
                    {{ movementForm.errors.associated_doc }}
                </p>
            </div>

            <Button class="mt-5" :disabled="movementForm.processing" type="submit" >Agregar nuevo pase</Button>
        </form>



        <Label class="mt-5" for="date">Historial de pases:</Label>
            <ul
            v-for="pase in props.history"
            :key="pase.id"
            class="ml-4 mt-2 text-sm"
            >
                <li :class="{'text-muted-foreground' : pase.id != record.last_movement_id,
                    'font-semibold' : pase.id === record.last_movement_id
                }" > {{ movementLabel(pase) }}   </li>
            </ul>
    </section>
</template>
