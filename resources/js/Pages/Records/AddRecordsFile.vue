<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { useForm, usePage } from '@inertiajs/vue3';
import NotificationToast from '@/Components/NotificationToast.vue';
import { ref, computed } from 'vue';


defineOptions({
  layout: AppLayout,
})

const showMessage = ref(false);
const page = usePage();
const successMessage = computed(() => page.props.flash?.success ?? null);
const errorMessage = computed(() => page.props.flash?.error ?? null);


const form = useForm({
    file: null
})
const fileInput = ref(null);

function submit() {
  form.post('/records/add', {
    onFinish: resetAndShow,
  })
}

function resetAndShow(){
    form.reset('file');
    form.file = null;
    if (fileInput.value) fileInput.value.value = null;
    showMessage.value = true;
    setTimeout(() => {
        showMessage.value = false;
    }, 3000);
}
</script>

<template>
    <NotificationToast :show="showMessage" :success="successMessage" :error="errorMessage"/>
    <div class="flex items-center gap-2">
        <span class="text-lg font-semibold">Agregar expedientes</span>
    </div>
    <div class="flex w-full max-w-lg items-center space-x-2">
        <form @submit.prevent="submit">
            <input ref="fileInput" type="file" @change="e => form.file = e.target.files[0]" />
            <Button type="submit" :disabled="!form.file"> Aceptar </Button>
        </form>
    </div>
</template>
