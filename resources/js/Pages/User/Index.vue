<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Link } from '@inertiajs/vue3';
defineOptions({
  layout: AppLayout,
})

defineProps({
  title: String,
  users: Array,
})
</script>

<template>
    <div class="space-y-6">
        <section v-for="user in users" :key="user.id" class="space-y-4 rounded-lg border border-border bg-card p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">{{ user.name }} {{ user.surname }}</h3>

                    <Link :href="`/users/${user.id}`" class="border border-border font-bold bg-primary text-primary-foreground hover:bg-primary/80 px-3 py-1 text-sm rounded-lg">
                        <p>Ver mas</p>
                    </Link>


            </div>
            <Label for="email">Email</Label>
            <Input
            id="email"
            v-model="user.email"
            disabled
            />
            <Label for="role">Rol</Label>
            <Input
            id="role"
            v-model="user.role.role_name"
            disabled
            />
        </section>
    </div>
</template>
