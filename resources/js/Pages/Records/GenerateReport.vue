<script setup>
import Input from '@/Components/ui/input/Input.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import NotificationToast from '@/Components/NotificationToast.vue';

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

const showMessage = ref(false)
const successMessage = computed(() => page.props.flash?.success ?? null);
const errorMessage = computed(() => page.props.flash?.error ?? null);


function submit() {
    form.post('/report/download', {
        preserveState: true,
        onSuccess: () => {
            showMessage.value = true;
            setTimeout(() => {
                showMessage.value = false;
            }, 3000);
        },
        onError: () => {
            showMessage.value = true;
            setTimeout(() => {
                showMessage.value = false;
            }, 3000);
        }
    })
}

</script>

<template>

<NotificationToast :show="showMessage" :success="successMessage" :error="errorMessage"/>


<div class="space-y-2 flex" >
    <form @submit.prevent="submit" class="space-y-2">
        <Label>Seleccione fecha</Label>
        <Input type="date" v-model="form.date" />

        <Button type="submit" >Descargar</Button>
    </form>
</div>


</template>
