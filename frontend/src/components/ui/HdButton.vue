<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'ghost', 'danger'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'button'
  }
});

const baseClasses = "inline-flex items-center justify-center font-brand font-medium transition-normal shadow-base focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed";

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'px-3 py-1.5 text-sm rounded-sm';
    case 'md': return 'px-4 py-2 text-base rounded-md';
    case 'lg': return 'px-6 py-3 text-lg rounded-lg';
    default: return 'px-4 py-2 text-base rounded-md';
  }
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary': 
      return 'bg-primary text-inverse hover:opacity-90 shadow-raised focus:ring-primary';
    case 'secondary': 
      return 'bg-surface-elevated text-content hover:bg-black/5 dark:hover:bg-white/5 border border-subtle focus:ring-secondary';
    case 'outline': 
      return 'bg-transparent text-primary border border-primary hover:bg-primary/10 focus:ring-primary';
    case 'ghost': 
      return 'bg-transparent text-content hover:bg-black/5 dark:hover:bg-white/5 shadow-none border-transparent focus:ring-subtle';
    case 'danger': 
      return 'bg-danger text-inverse hover:opacity-90 shadow-raised focus:ring-danger';
    default: 
      return 'bg-primary text-inverse hover:opacity-90 shadow-raised focus:ring-primary';
  }
});
</script>

<template>
  <button 
    :type="type" 
    :disabled="disabled" 
    :class="[baseClasses, sizeClasses, variantClasses]"
  >
    <slot />
  </button>
</template>
