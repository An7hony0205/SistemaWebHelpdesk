<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'neutral',
    validator: (value) => ['success', 'warning', 'danger', 'info', 'neutral'].includes(value)
  }
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'success':
      return 'bg-success/20 text-success border border-success/30';
    case 'warning':
      return 'bg-warning/20 text-warning border border-warning/30';
    case 'danger':
      return 'bg-danger/20 text-danger border border-danger/30';
    case 'info':
      return 'bg-info/20 text-info border border-info/30';
    case 'neutral':
    default:
      return 'bg-surface-elevated text-muted border border-subtle';
  }
});
</script>

<template>
  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium font-brand', variantClasses]">
    <slot />
  </span>
</template>
