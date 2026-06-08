<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { useForm } from '@inertiajs/vue3';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';
import { ref } from 'vue';
import { cn } from '@/lib/utils';


defineOptions({
  layout: AppLayout,
})

const props = defineProps({
  records: {
    type: Array,
    default: () => [],
  },
})

const records = ref(props.records);

const form = useForm({
    file: null
})

const fileInput = ref(null);

function submit() {
  form.post('/file/upload', {
    forceFormData: true,
    onSuccess: reset,
  })
}

function reset(){
    form.reset('file');
    form.file = null;
    if (fileInput.value) fileInput.value.value = null;
}
</script>

<template>
    <NotificationToast />
    <div class="flex items-center gap-2">
        <span class="text-lg font-semibold">Agregar expedientes</span>
    </div>
    <div class="flex w-full max-w-lg items-center space-y-2">
        <form @submit.prevent="submit" class="space-y-2">
            <input ref="fileInput" type="file" accept=".xlsx,.xls" @change="e => form.file = e.target.files[0]" :class="cn('selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 min-w-0 rounded-md border bg-transparent px-3 py-1 text-sm shadow-xs transition-[color,box-shadow] outline-none pt-2')"/>
            <p v-if="form.errors.file" class="text-sm text-destructive">{{ form.errors.file }}</p>
            <Button type="submit" :disabled="!form.file || form.processing"> Aceptar </Button>
        </form>
    </div>
    <div v-if="records.length > 0" class="mt-6">
        <span class="text-lg font-semibold">Registros detectados:</span>
        <ul class="mt-2 list-disc pl-5">
            <li v-for="(record, index) in records" :key="index">
                {{ record.title }}
            </li>
        </ul>
    </div>
</template>
