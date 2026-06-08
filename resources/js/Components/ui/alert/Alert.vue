<script setup>
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/Components/ui/alert-dialog'
import { Button } from '@/Components/ui/button';
import { CircleXIcon } from 'lucide-vue-next';

defineProps({
    label : String,
    title: String,
    description: String,
    variant: String,
})

</script>

<template>
  <AlertDialog>
    <AlertDialogTrigger>
        <Button :variant=" variant"><CircleXIcon class="h-4 w-4" /> {{ label }}</Button>
    </AlertDialogTrigger>
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>{{title}}</AlertDialogTitle>
        <AlertDialogDescription>
          {{description}}
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>Cancelar</AlertDialogCancel>
        <AlertDialogAction @click="$emit('accept')">Continuar</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
