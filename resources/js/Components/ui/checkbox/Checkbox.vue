<script setup>
import { Check } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

defineOptions({
  inheritAttrs: false,
})

const props = defineProps({
  modelValue: { type: Boolean, required: false, default: false },
  class: {
    type: [Boolean, null, String, Object, Array],
    required: false,
    skipCheck: true,
  },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <span class="relative inline-flex size-4 shrink-0">
    <input
      v-bind="$attrs"
      data-slot="checkbox"
      type="checkbox"
      :checked="modelValue"
      :class="cn(
        'peer size-4 shrink-0 appearance-none rounded-sm border border-input bg-background shadow-xs transition-[color,box-shadow] outline-none',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        'checked:border-primary checked:bg-primary disabled:cursor-not-allowed disabled:opacity-50',
        props.class,
      )"
      @change="emit('update:modelValue', $event.target.checked)"
    >
    <Check class="pointer-events-none absolute left-0 top-0 hidden size-4 p-0.5 text-primary-foreground peer-checked:block" />
  </span>
</template>
