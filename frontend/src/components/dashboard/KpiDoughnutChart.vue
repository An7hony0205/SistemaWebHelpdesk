<script setup>
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import HdCard from '../ui/HdCard.vue';

ChartJS.register(ArcElement, Tooltip, Legend);
ChartJS.defaults.color = 'var(--color-muted)';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  labels: {
    type: Array,
    default: () => []
  },
  data: {
    type: Array,
    default: () => []
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

const chartData = computed(() => ({
  labels: props.labels,
  datasets: [
    {
      backgroundColor: ['#f97316', '#3b82f6', '#10b981', '#ef4444', '#64748b'], // orange, blue, emerald, red, slate
      borderColor: 'var(--color-surface)',
      borderWidth: 2,
      data: props.data
    }
  ]
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom'
    }
  }
};
</script>

<template>
  <HdCard class="h-full min-h-[300px] flex flex-col transition-all hover:border-primary/50">
    <h3 class="text-sm font-medium font-brand text-muted uppercase tracking-wider mb-4">
      {{ title }}
    </h3>
    
    <div
      v-if="loading"
      class="flex-grow flex items-center justify-center"
    >
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
    </div>
    
    <div
      v-else-if="error"
      class="flex-grow flex items-center justify-center text-sm text-danger font-medium"
    >
      {{ error }}
    </div>
    
    <div
      v-else-if="data.length === 0 || data.every(v => v === 0)"
      class="flex-grow flex items-center justify-center text-sm text-muted"
    >
      No hay datos para mostrar
    </div>
    
    <div
      v-else
      class="flex-grow relative"
    >
      <Doughnut
        :data="chartData"
        :options="chartOptions"
      />
    </div>
  </HdCard>
</template>
