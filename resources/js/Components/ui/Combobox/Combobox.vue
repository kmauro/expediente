<script setup lang="ts">
import { computed, ref } from 'vue'
import { Check, ChevronsUpDown, Plus } from 'lucide-vue-next'

import { cn } from '@/lib/utils'
import { Button } from '@/Components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/Components/ui/command'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/Components/ui/popover'


type Option = {
  id: number
  name: string
  normalized_name: string
}

const props = defineProps<{
  modelValue: number | null
  newValue?: string | null
  options: Option[]
}>()

const emit = defineEmits<{
  'update:modelValue': [value: number | null]
  'update:newValue': [value: string | null]
}>()

const open = ref(false)
const search = ref('')


function normalizeString(value: string) {
  return value
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
}


const selectedOption = computed(() => {
  return props.options.find(option => option.id === props.modelValue)
})

const normalizedSearch = computed(() => normalizeString(search.value))


const filteredOptions = computed(() => {
  if (!normalizedSearch.value) {
    return props.options
  }

  return props.options.filter(option =>
    option.normalized_name.includes(normalizedSearch.value)
  )
})


const exactArea = computed(() => {
  if (!normalizedSearch.value) {
    return null
  }

  return props.options.find(option =>
    option.normalized_name === normalizedSearch.value
  ) ?? null
})

const canCreate = computed(() => {
  return Boolean(normalizedSearch.value && !exactArea.value)
})

const label = computed(() => {
  if (selectedOption.value) {
    return selectedOption.value.name
  }

  if (props.newValue) {
    return props.newValue
  }

  return 'Seleccionar'
})

function selectOption(option: Option) {
  emit('update:modelValue', option.id)
  emit('update:newValue', null)
  search.value = ''
  open.value = false
}

function createOption() {
  const value = search.value.trim()

  if (!value) {
    return
  }

  emit('update:modelValue', null)
  emit('update:newValue', value)

  search.value = value
  open.value = false
}
</script>

<template>

  <Popover v-model:open="open">
    <PopoverTrigger as-child>
      <Button
        type="button"
        variant="outline"
        role="combobox"
        :aria-expanded="open"
        class="w-full justify-between"
      >
        <span class="truncate">
          {{ label }}
        </span>

        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
      </Button>
    </PopoverTrigger>

    <PopoverContent class="w-[--radix-popover-trigger-width] p-0">
        <Command
        ignore-filter
        >
            <CommandInput
            v-model="search"
            placeholder="Buscar o agregar opción..."
            />

            <CommandList>
                <CommandEmpty v-if="!canCreate">
                    No se encontraron resultados.
                </CommandEmpty>

                <CommandGroup>
                    <CommandItem
                    v-for="option in filteredOptions"
                    :key="option.id"
                    :value="String(option.id)"
                    @select="selectOption(option)"
                    >
                    <Check
                    :class="cn(
                        'mr-2 h-4 w-4',
                        modelValue === option.id ? 'opacity-100' : 'opacity-0'
                    )"
                    />
                    {{ option.name }}
                    </CommandItem>

                    <CommandItem
                    v-if="canCreate"
                    :value="`create-${normalizedSearch}`"
                    @select="createOption"
                    >
                    <Plus class="mr-2 h-4 w-4" />
                    Agregar "{{ search.trim() }}"
                    </CommandItem>
                </CommandGroup>
            </CommandList>
        </Command>
    </PopoverContent>
  </Popover>
</template>
