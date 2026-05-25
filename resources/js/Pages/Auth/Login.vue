<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Button } from '@/Components/ui/button'
import { Checkbox } from '@/Components/ui/checkbox'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { useForm } from '@inertiajs/vue3'
import { LogIn } from 'lucide-vue-next'

defineProps({
  title: String,
})

const form = useForm({
  username: '',
  password: '',
  remember: false,
}).dontRemember('password')

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <AuthLayout
    :title="title"
    subtitle="Ingresa tus credenciales para continuar."
    action-text="No tienes cuenta?"
    action-label="Crear cuenta"
    action-href="/signup"
  >
    <form class="space-y-5" @submit.prevent="submit">
      <div class="space-y-2">
        <Label for="username">Nombre de usuario</Label>
        <Input
          id="username"
          v-model="form.username"
          type="text"
          name="username"
          autocomplete="email"
          autofocus
          :aria-invalid="Boolean(form.errors.username)"
          placeholder="nombre de usuario"
        />
        <p v-if="form.errors.username" class="text-sm text-destructive">{{ form.errors.username }}</p>
      </div>

      <div class="space-y-2">
        <Label for="password">Password</Label>
        <Input
          id="password"
          v-model="form.password"
          type="password"
          name="password"
          autocomplete="current-password"
          :aria-invalid="Boolean(form.errors.password)"
          placeholder="Tu password"
        />
        <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
      </div>

      <Button type="submit" class="w-full" :disabled="form.processing">
        <LogIn class="size-4" />
        {{ form.processing ? 'Ingresando...' : 'Ingresar' }}
      </Button>
    </form>
  </AuthLayout>
</template>
