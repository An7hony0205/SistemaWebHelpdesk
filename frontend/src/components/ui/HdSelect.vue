<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  }
});

defineEmits(['update:modelValue']);
</script>

<template>
  <div class="w-full">
    <label v-if="label" class="block text-sm font-medium text-muted mb-1">
      {{ label }} <span v-if="required" class="text-danger">*</span>
    </label>
    <div class="relative">
      <select
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        :required="required"
        :disabled="disabled"
        :class="[
          'block w-full rounded-md shadow-sm sm:text-sm transition-normal font-body',
          'bg-surface-elevated text-content placeholder-muted',
          'focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary',
          disabled ? 'opacity-50 cursor-not-allowed' : '',
          error ? 'border-danger focus:ring-danger focus:border-danger' : 'border-subtle'
        ]"
      >
        <slot>
          <option v-for="option in options" :key="option.value || option.id" :value="option.value || option.id">
            {{ option.label || option.name }}
          </option>
        </slot>
      </select>
    </div>
    <p v-if="error" class="mt-1 text-sm text-danger">{{ error }}</p>
  </div>
</template>
