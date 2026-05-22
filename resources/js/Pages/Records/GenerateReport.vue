<script setup>
import Input from '@/Components/ui/input/Input.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { useForm, usePage } from '@inertiajs/vue3';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';

defineOptions({
    layout: AppLayout,
})

defineProps({
    title: String,
    date: String,
})

const page = usePage()
const form = useForm({
    date: page.props.date || '',
})

function submit() {
    form.post('/report/download', {
        preserveState: true,
        onSuccess: () => {
        },
        onError: () => {
        }
    })
}
</script>

<template>
<NotificationToast />
<div class="space-y-2 flex" >
    <form @submit.prevent="submit" class="space-y-2">
        <Label>Seleccione fecha</Label>
        <Input type="date" v-model="form.date" />

        <Button type="submit" >Descargar</Button>
    </form>
</div>


</template>
