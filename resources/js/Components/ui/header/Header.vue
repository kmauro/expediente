<script setup>
import logo from '../../../../imgs/logo_argentina-azul.svg'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <header class="border-b-[3px] border-[#e7ba61] bg-white px-4 py-3 sm:px-6">
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4" aria-label="Navegación institucional">
      <Link href="/" aria-label="Ir al inicio">
        <img :src="logo" alt="Argentina.gob.ar" class="h-auto w-52 sm:w-[254px]" width="254" height="50" />
      </Link>

      <a
        href="https://mi.argentina.gob.ar"
        target="_blank"
        rel="noopener noreferrer"
        class="rounded-lg bg-[#242c4f] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#242c4f]/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
      >
        Mi Argentina
      </a>
    </nav>
  </header>
</template>
