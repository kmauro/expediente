<script setup>
import { computed } from 'vue'
import {
  ChevronDown,
  LogOut,
  Plus,
  Settings,
  User,
  ListIcon,
  Home,
  FileText,
  ArrowUpToLineIcon,
  BookCheckIcon
} from 'lucide-vue-next'
import { Link, usePage } from '@inertiajs/vue3'
import { isAdmin } from '@/Composables/isAdmin'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
} from '../dropdown-menu'
import ApplicationLogo from '../application-logo/ApplicationLogo.vue'

defineProps({
  sidebarOpen: Boolean,
})

defineEmits(['toggle-sidebar'])

const page = usePage()

const items = [
      { label: 'Inicio', icon: Home, href: '/homepage' },
      { label: 'Expedientes', icon: FileText, href: '/records' },
      { label: 'Agregar', icon: Plus, href: '/record/add'},
      { label: 'Cargar Archivo', icon: ArrowUpToLineIcon, href: '/file/add'},
      { label: 'Generar reporte', icon: BookCheckIcon, href: '/report/generate'}
]



const user = computed(() => page.props.auth?.user ?? { name: 'Usuario', email: '' , surname: '', username: '', phone: '' })
const userInitials = computed(() => {
  return user.value.name?.charAt(0).toUpperCase() +  user.value.surname?.charAt(0).toUpperCase() || 'U'
})
</script>

<template>
  <aside class="min-h-full w-80 shrink-0 flex-col gap-0  bg-background text-sidebar-foreground lg:flex">
    <Link href="/homepage" view-transition replace class="flex items-center gap-3 border-b border-sidebar-border p-6 pb-3">
        <ApplicationLogo />

    </Link>

    <div class="flex-1 overflow-y-auto p-6 border-r border-sidebar-border">
      <div class="space-y-4">
        <Link
            v-for="item in items"
            :key="item.label"
            view-transition
            :href="item.href || '#'"
            :class="{
            'bg-primary text-white font-semibold': $page.url.startsWith(item.href),
            'hover:bg-primary/10': $page.url !== item.href,
            }"
            class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-sidebar-foreground"
        >
            <component :is="item.icon" class="h-4 w-4" />
            <span>{{ item.label }}</span>
        </Link>
      </div>
    </div>

    <div class="space-y-4 border-t border-sidebar-border p-6 border-r border-sidebar-border">
      <DropdownMenu>
        <DropdownMenuTrigger class="flex w-full items-center gap-3 rounded-lg px-3 py-2 hover:bg-primary/10">
          <div class="grid h-9 w-9 place-items-center rounded-full bg-primary text-xs font-semibold text-primary-foreground">
            {{ userInitials }}
          </div>
          <div class="min-w-0 flex-1 text-left">
            <p class="truncate text-sm font-semibold">{{ user.name + ' ' + user.surname }}</p>
            <p class="truncate text-xs text-muted-foreground">{{ user.email }}</p>
          </div>
          <ChevronDown class="h-4 w-4" />
        </DropdownMenuTrigger>

        <DropdownMenuContent class="w-56">
          <DropdownMenuItem class="cursor-pointer">
            <Link href="/profile" view-transition replace class="flex w-full items-center rounded-lg text-sm text-sidebar-foreground">
              <User class="mr-2 h-4 w-4" />
              <span>Informacion</span>
            </Link>
          </DropdownMenuItem>

          <DropdownMenuItem v-if="isAdmin(user.id)" class="cursor-pointer">
            <Link href="/users" view-transition replace class="flex w-full items-center rounded-lg text-sm text-sidebar-foreground">
              <ListIcon class="mr-2 h-4 w-4" />
              <span>Usuarios</span>
            </Link>
          </DropdownMenuItem>

          <DropdownMenuItem class="cursor-pointer">
            <Link href="/settings" view-transition replace class="flex w-full items-center rounded-lg text-sm text-sidebar-foreground">
                <Settings class="mr-2 h-4 w-4" />
                <span>Ajustes</span>
            </Link>
          </DropdownMenuItem>

          <DropdownMenuSeparator />

          <DropdownMenuItem as-child class="cursor-pointer">
            <Link href="/logout" method="post" as="button" class="w-full text-red-400 rounded-lg text-sm">
              <LogOut class="mr-2 h-4 w-4" />
              <span>Cerrar sesion</span>
            </Link>
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </aside>
</template>
