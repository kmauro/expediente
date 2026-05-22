<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import Button from '@/Components/ui/button/Button.vue';
import { ref } from 'vue';
import { Edit, CircleXIcon } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import { NativeSelect, NativeSelectOption } from '@/Components/ui/native-select';
import ChangePassword from './ChangePassword.vue';
import NotificationToast from '@/Components/ui/notification-toast/NotificationToast.vue';

defineOptions({
    layout: AppLayout,
})

const props = defineProps({
    user: Object,
    roles: Array,
    isCurrentUser: Boolean,
    isAdmin: Boolean,
})

const isEditing = ref(false);

const profileForm = useForm({
    id : props.user.id,
    name : props.user.name,
    email : props.user.email,
    surname : props.user.surname,
    username : props.user.username,
    phone : props.user.phone,
    role_id : props.user.role_id,
})

const updateProfile = () => {
    profileForm.post('/profile');
}

</script>

<template>
    <NotificationToast />
    <section class="border p-4 rounded-lg shadow-md bg-background">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Información del Perfil</h2>
            <Button variant="outline" @click="isEditing = !isEditing" v-if="isCurrentUser" class="ml-auto"
            :class="{
                'bg-primary text-white hover:bg-destructive/90' : isEditing,

            }">
                <Edit v-if="!isEditing" class="size-4" />
                <CircleXIcon v-else class="size-4" />
            </Button>
        </div>
        <form @submit.prevent="updateProfile" class="mt-4 space-y-4">
            <Label for="username">Nombre de Usuario:</Label>
            <Input
            type="text"
            id="username"
            v-model="profileForm.username"
            disabled
            :aria-invalid="Boolean(profileForm.errors.username)"
            />
            <p v-if="profileForm.errors.username" class="text-sm text-destructive">
                {{ profileForm.errors.username }}
            </p>

            <Label v-if="isAdmin" for="role">Rol:</Label>
            <NativeSelect v-if="isAdmin" id="role" v-model="profileForm.role_id" :aria-invalid="Boolean(profileForm.errors.role_id)">
                <NativeSelectOption v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.role_name }}
                </NativeSelectOption>

            </NativeSelect>
            <p v-if="profileForm.errors.role_id" class="text-sm text-destructive">
                {{ profileForm.errors.role_id }}
            </p>

            <Label for="name">Nombre:</Label>
            <Input
            type="text"
            id="name"
            v-model="profileForm.name"
            :disabled = "!isEditing"
            :aria-invalid="Boolean(profileForm.errors.name)"
            />
            <p v-if="profileForm.errors.name" class="text-sm text-destructive">
                {{ profileForm.errors.name }}
            </p>

            <Label for="surname">Apellido:</Label>
            <Input
            type="text"
            id="surname"
            v-model="profileForm.surname"
            :disabled = "!isEditing"
            :aria-invalid="Boolean(profileForm.errors.surname)"
            />
            <p v-if="profileForm.errors.surname" class="text-sm text-destructive">
                {{ profileForm.errors.surname }}
            </p>

            <Label for="phone">Telefono:</Label>
            <Input
            type="text"
            id="phone"
            v-model="profileForm.phone"
            :disabled = "!isEditing"
            :aria-invalid="Boolean(profileForm.errors.phone)"
            />
            <p v-if="profileForm.errors.phone" class="text-sm text-destructive">
                {{ profileForm.errors.phone }}
            </p>

            <Label for="email">Correo Electrónico:</Label>
            <Input
            type="email"
            id="email"
            v-model="profileForm.email"
            :disabled = "!isEditing"
            :aria-invalid="Boolean(profileForm.errors.email)"
            />
            <p v-if="profileForm.errors.email" class="text-sm text-destructive">
                {{ profileForm.errors.email }}
            </p>
            <Button :disabled="profileForm.processing" v-if="isEditing || !isCurrentUser">Actualizar Perfil</Button>
        </form>
    </section>

    <ChangePassword v-if="isCurrentUser" />

</template>
