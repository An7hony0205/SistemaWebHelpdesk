<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

defineEmits(['update:modelValue']);
</script>

<template>
  <div class="flex items-start">
    <div class="flex items-center h-5">
      <button 
        type="button" 
        :class="[
          modelValue ? 'bg-primary' : 'bg-surface-elevated',
          disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
          'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-base'
        ]" 
        :disabled="disabled"
        @click="$emit('update:modelValue', !modelValue)"
      >
        <span class="sr-only">Toggle</span>
        <span 
          aria-hidden="true" 
          :class="[
            modelValue ? 'translate-x-5' : 'translate-x-0',
            'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200'
          ]"
        ></span>
      </button>
    </div>
    <div v-if="label || description" class="ml-3 text-sm">
      <label v-if="label" class="font-medium text-content cursor-pointer" @click="!disabled && $emit('update:modelValue', !modelValue)">
        {{ label }}
      </label>
      <p v-if="description" class="text-muted">{{ description }}</p>
    </div>
  </div>
</template>
