<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Input from '@/Components/ui/input/Input.vue';
import Button from '@/Components/ui/button/Button.vue';
import Label from '@/Components/ui/label/Label.vue';
import Combobox from '@/Components/ui/Combobox/Combobox.vue';
import { useForm } from '@inertiajs/vue3';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';


const props = defineProps({
    title: String,
    date: String,
    areas: Object,
    types: Object,
    states: Object,
    counterparties: Object,
})

defineOptions({
    layout: AppLayout,
})

const fileForm = useForm({
    title: '',
    number : '',
    state_id: null,
    type_id: null,
    type_name: null,
    counterparty_id: null,
    counterparty_name : null,
    created_at: props.date,
    area_id: null,
    area_name: null,
    associated_doc: '',
})

function submit() {
    fileForm.post('/record/add',{
        onSuccess: () => {
            reset();
            fileForm.errors ?? show();
        },
    }, {
        onError: () => show()
    });
}

function reset(){
    fileForm.title= ''
    fileForm.number = ''
    fileForm.state_id= null
    fileForm.type_id= null
    fileForm.type_name= null
    fileForm.counterparty_id= null
    fileForm.counterparty_name = null
    fileForm.created_at= props.date
    fileForm.area_id= null
    fileForm.area_name= null
    fileForm.associated_doc= ''
}
</script>

<template>
    <NotificationToast />
    <div class="flex flex-col w-200 ml-3 items-left justify-left">
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <Label for="title">Titulo de expediente:</Label>
            <Input
            type="text"
            id="title"
            v-model="fileForm.title"
            :aria-invalid="Boolean(fileForm.errors.title)"
            />
            <p v-if="fileForm.errors.title" class="text-sm text-destructive">
                {{ fileForm.errors.title }}
            </p>

            <Label for="number">Numero de expediente:</Label>
            <Input
            type="text"
            id="number"
            v-model="fileForm.number"
            :aria-invalid="Boolean(fileForm.errors.number)"
            />
            <p v-if="fileForm.errors.number" class="text-sm text-destructive">
                {{ fileForm.errors.number }}
            </p>

            <Label for="state">Estado:</Label>
            <Select id="state" v-model="fileForm.state_id" >
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona un estado" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="state in states" :key="state.id" :value="state.id">
                        {{ state.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="fileForm.errors.state_id" class="text-sm text-destructive">
                {{ fileForm.errors.state_id }}
            </p>

            <Label for="type">Objeto:</Label>
            <Combobox
            id="type"
            v-model="fileForm.type_id"
            v-model:new-value="fileForm.type_name"
            :options="types"
            />
            <p v-if="fileForm.errors.type_id" class="text-sm text-destructive">
                {{ fileForm.errors.type_id }}
            </p>
            <p v-if="fileForm.errors.type_name" class="text-sm text-destructive">
                {{ fileForm.errors.type_name }}
            </p>


            <Label for="counterparty">Proveedor/Programa:</Label>
            <Combobox
            id="counterparty"
            v-model="fileForm.counterparty_id"
            v-model:new-value="fileForm.counterparty_name"
            :options="counterparties"
            />
            <p v-if="fileForm.errors.counterparty_id" class="text-sm text-destructive">
                {{ fileForm.errors.counterparty_id }}
            </p>
            <p v-if="fileForm.errors.counterparty_name" class="text-sm text-destructive">
                {{ fileForm.errors.counterparty_name }}
            </p>

            <div class="flex space-x-4 mt-5">
                <div class="w-40 space-y-2">
                    <Label for="newDate">Fecha del ultimo pase:</Label>
                    <Input
                    type="date"
                    id="newDate"
                    v-model="fileForm.created_at"
                    :aria-invalid="Boolean(fileForm.errors.created_at)"
                    />
                    <p v-if="fileForm.errors.created_at" class="text-sm text-destructive">
                        {{ fileForm.errors.created_at }}
                    </p>
                </div>

                <div class="w-80 space-y-2">
                    <Label>Area destinataria:</Label>
                    <Combobox
                    id="area"
                    v-model="fileForm.area_id"
                    v-model:new-value="fileForm.area_name"
                    :options="areas"
                    />
                    <p v-if="fileForm.errors.area_id" class="text-sm text-destructive">
                        {{ fileForm.errors.area_id }}
                    </p>
                    <p v-if="fileForm.errors.area_name" class="text-sm text-destructive">
                        {{ fileForm.errors.area_name }}
                    </p>
                </div>

                <div class="w-80 space-y-2">
                    <Label for="doc">Descripción del ultimo pase:</Label>
                    <Input
                    type="text"
                    id="doc"
                    v-model="fileForm.associated_doc"
                    :aria-invalid="Boolean(fileForm.errors.associated_doc)"
                    />
                    <p v-if="fileForm.errors.associated_doc" class="text-sm text-destructive">
                        {{ fileForm.errors.associated_doc }}
                    </p>
                </div>
            </div>
            <Button :disabled="fileForm.processing" type="submit" >Agregar expediente</Button>
        </form>
    </div>
</template>
