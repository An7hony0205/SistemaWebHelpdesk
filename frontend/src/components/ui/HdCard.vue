<script setup>
const props = defineProps({
  noPadding: {
    type: Boolean,
    default: false
  }
});
</script>

<template>
  <div class="bg-surface rounded-lg border border-subtle shadow-base overflow-hidden">
    <!-- Header Slot -->
    <div
      v-if="$slots.header"
      class="px-6 py-4 border-b border-subtle bg-surface-elevated"
    >
      <slot name="header" />
    </div>
    
    <!-- Body -->
    <div :class="[noPadding ? '' : 'p-6']">
      <slot />
    </div>

    <!-- Footer Slot -->
    <div
      v-if="$slots.footer"
      class="px-6 py-4 border-t border-subtle bg-surface-elevated"
    >
      <slot name="footer" />
    </div>
  </div>
</template>
