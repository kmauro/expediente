<script setup>
import { CollapsibleContent } from "reka-ui";

const props = defineProps({
  forceMount: { type: Boolean, required: false },
  asChild: { type: Boolean, required: false },
  as: { type: null, required: false },
});
</script>

<template>
  <CollapsibleContent
    data-slot="collapsible-content"
    class="collapsible-content-animated"
    v-bind="props"
  >
    <slot />
  </CollapsibleContent>
</template>

<style scoped>
.collapsible-content-animated {
  overflow: hidden;
  max-height: 0;
  opacity: 0;
  transition: max-height 0.25s ease, opacity 0.25s ease, transform 0.25s ease;
}

.collapsible-content-animated[data-state='open'] {
  max-height: 480px;
  opacity: 1;
}

.collapsible-content-animated[data-state='closed'] {
  max-height: 0;
  opacity: 0;
}
</style>
