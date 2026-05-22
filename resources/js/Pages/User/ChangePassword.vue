<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { useForm } from '@inertiajs/vue3'
import { Edit, CircleXIcon } from 'lucide-vue-next'
import { ref } from 'vue'

const passForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
})

const updatePassword = () => {
    passForm.post('/profile/updatePassword');
}
defineProps({
    user: Object,
})
defineOptions({
    layout: AppLayout,
})

const changingPass = ref(false);

</script>


<template>
    <section class="mt-6 border p-4 rounded-lg shadow-md bg-background">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold mb-4">Cambiar Contraseña</h2>
            <Button variant="outline" @click="changingPass = !changingPass" class="ml-auto">
                <Edit v-if="!changingPass" class="size-4" />
                <CircleXIcon v-else class="size-4" />
            </Button>
        </div>
        <form @submit.prevent="updatePassword" class="space-y-4">
            <Label for="current_password">Contraseña Actual:</Label>
            <Input
            type="password"
            id="current_password"
            v-model="passForm.current_password"
            :disabled = "!changingPass"
            :aria-invalid="Boolean(passForm.errors.current_password)"
            />
            <p v-if="passForm.errors.current_password" class="text-sm text-destructive">
                {{ passForm.errors.current_password }}
            </p>
            <Label for="new_password">Nueva Contraseña:</Label>
            <Input
            type="password"
            id="new_password"
            v-model="passForm.new_password"
            :disabled = "!changingPass"
            :aria-invalid="Boolean(passForm.errors.new_password)"
            />
            <p v-if="passForm.errors.new_password" class="text-sm text-destructive">
                {{ passForm.errors.new_password }}
            </p>
            <Label for="new_password_confirmation">Confirmar Nueva Contraseña:</Label>
            <Input
            type="password"
            id="new_password_confirmation"
            v-model="passForm.new_password_confirmation"
            :disabled = "!changingPass"
            :aria-invalid="Boolean(passForm.errors.new_password_confirmation)"
            />
            <p v-if="passForm.errors.new_password_confirmation" class="text-sm text-destructive">
                {{ passForm.errors.new_password_confirmation }}
            </p>
            <Button :disabled="passForm.processing" v-if="changingPass">Actualizar Contraseña</Button>

        </form>
    </section>
</template>
