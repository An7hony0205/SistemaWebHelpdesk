<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import api from '../services/api';
import KpiCard from '../components/dashboard/KpiCard.vue';
import KpiDoughnutChart from '../components/dashboard/KpiDoughnutChart.vue';
import HdButton from '../components/ui/HdButton.vue';

const layout = ref([]);
const metrics = ref({});
const loadingLayout = ref(true);
const loadingMetrics = ref(true);
let refreshInterval = null;

const fetchLayout = async () => {
  try {
    const response = await api.get('/dashboard/layout');
    layout.value = response.data.widgets_layout || [];
  } catch (error) {
    console.error('Error fetching dashboard layout:', error);
  } finally {
    loadingLayout.value = false;
  }
};

const fetchMetrics = async () => {
  if (layout.value.length === 0) return;
  
  loadingMetrics.value = true;
  try {
    const widgetIds = layout.value.map(w => w.id);
    const response = await api.post('/dashboard/metrics', {
      widgets: widgetIds,
      period: 'month'
    });
    metrics.value = response.data;
  } catch (error) {
    console.error('Error fetching dashboard metrics:', error);
  } finally {
    loadingMetrics.value = false;
  }
};

onMounted(async () => {
  await fetchLayout();
  await fetchMetrics();
  
  // Polling cada 3 minutos (180000 ms)
  refreshInterval = setInterval(fetchMetrics, 180000);
});

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval);
});
</script>

<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold font-brand text-content">
        Dashboard Ejecutivo
      </h2>
      <div class="flex space-x-2">
        <HdButton
          variant="secondary"
          size="sm"
          @click="fetchMetrics"
        >
          <svg
            v-if="loadingMetrics"
            class="animate-spin -ml-1 mr-2 h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
          ><circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          /><path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          /></svg>
          <svg
            v-else
            class="-ml-1 mr-2 h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          ><path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
          /></svg>
          {{ loadingMetrics ? 'Actualizando...' : 'Actualizar' }}
        </HdButton>
      </div>
    </div>

    <div
      v-if="loadingLayout"
      class="flex justify-center py-12"
    >
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
    </div>

    <div
      v-else
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
    >
      <template
        v-for="widget in layout"
        :key="widget.id"
      >
        <!-- Tickets Count Widget -->
        <template v-if="widget.id === 'tickets_count'">
          <KpiCard 
            title="Tickets Abiertos" 
            :value="metrics['tickets_count']?.open || 0" 
            :loading="loadingMetrics"
            :error="metrics['tickets_count']?.error"
          />
          <KpiCard 
            title="Tickets Cerrados" 
            :value="metrics['tickets_count']?.closed || 0" 
            :loading="loadingMetrics"
            :error="metrics['tickets_count']?.error"
          />
        </template>
        
        <!-- SLA Compliance Widget -->
        <KpiCard 
          v-if="widget.id === 'sla_compliance'"
          title="Cumplimiento SLA" 
          :value="(metrics['sla_compliance']?.compliance_rate || 0) + '%'"
          :subtitle="metrics['sla_compliance'] ? `${metrics['sla_compliance'].breached} incumplidos` : ''"
          :loading="loadingMetrics"
          :error="metrics['sla_compliance']?.error"
        />
        
        <!-- Tickets by Status Widget -->
        <div
          v-if="widget.id === 'tickets_by_status'"
          class="col-span-1 md:col-span-2 lg:col-span-2"
        >
          <KpiDoughnutChart 
            title="Distribución por Estado" 
            :labels="metrics['tickets_by_status']?.labels || []"
            :data="metrics['tickets_by_status']?.data || []"
            :loading="loadingMetrics"
            :error="metrics['tickets_by_status']?.error"
          />
        </div>
      </template>
    </div>
  </div>
</template>
