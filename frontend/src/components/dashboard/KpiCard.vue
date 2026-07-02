<script setup>
import HdCard from '../ui/HdCard.vue';

defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  }
});
</script>

<template>
  <HdCard class="h-full flex flex-col justify-center transition-all hover:border-primary/50">
    <h3 class="text-sm font-medium font-brand text-muted uppercase tracking-wider mb-2">
      {{ title }}
    </h3>
    
    <div
      v-if="loading"
      class="animate-pulse flex space-x-4"
    >
      <div class="h-8 bg-black/10 dark:bg-white/10 rounded w-1/2" />
    </div>
    
    <div
      v-else-if="error"
      class="text-sm text-danger font-medium"
    >
      {{ error }}
    </div>
    
    <div v-else>
      <div class="text-3xl font-bold font-brand text-content">
        {{ value }}
      </div>
      <div
        v-if="subtitle"
        class="text-sm text-muted mt-1"
      >
        {{ subtitle }}
      </div>
    </div>
  </HdCard>
</template>
