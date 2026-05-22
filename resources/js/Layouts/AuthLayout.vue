<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import ApplicationLogo from '@/Components/ui/ApplicationLogo.vue';

defineProps({
  title: String,
  subtitle: String,
  actionText: String,
  actionLabel: String,
  actionHref: String,
})
</script>

<template>
    <Head :title="title" />
    <div class="mx-auto min-h-screen w-full grid-cols-1 lg:grid-cols-[1fr_420px]">
        <div class="hidden items-center justify-between gap-6 border-b border-border bg-card p-6 lg:flex">
            <Link href="/login" class="flex items-center gap-3 text-lg font-semibold">
                <ApplicationLogo />
            </Link>
            <ThemeToggle />
        </div>



      <main class="flex min-h-screen items-center justify-center px-6 py-10">
        <div class="w-full max-w-sm">
          <div class="rounded-lg border border-border bg-card p-6 text-card-foreground shadow-sm">
            <div class="mb-6 space-y-2">
              <h2 class="text-2xl font-semibold">{{ title }}</h2>
              <p class="text-sm text-muted-foreground">{{ subtitle }}</p>
            </div>

            <slot />

            <p class="mt-6 text-center text-sm text-muted-foreground">
              {{ actionText }}
              <Link :href="actionHref" class="font-medium text-primary underline-offset-4 hover:underline">
                {{ actionLabel }}
              </Link>
            </p>
          </div>
        </div>
      </main>
    </div>

</template>
