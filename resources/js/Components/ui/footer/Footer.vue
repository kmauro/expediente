<script setup>
defineProps({
  isAuth: Boolean,
})
</script>

<template>
  <footer class="border-t-[3px] border-[#e7ba61] bg-[#232d4f]" :class="isAuth ? 'h-16' : 'h-10'" />
</template>
