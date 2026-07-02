<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import HdCard from '../components/ui/HdCard.vue';
import HdTable from '../components/ui/HdTable.vue';
import HdBadge from '../components/ui/HdBadge.vue';

const logs = ref([]);
const loading = ref(true);

const fetchLogs = async () => {
  try {
    const response = await api.get('/notification-logs');
    logs.value = response.data.data; // paginated
  } catch (error) {
    console.error('Error fetching logs:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchLogs();
});

const columns = [
  { key: 'created_at', label: 'Fecha' },
  { key: 'user', label: 'Destinatario' },
  { key: 'event', label: 'Evento / Canal' },
  { key: 'status', label: 'Estado' },
  { key: 'retry_count', label: 'Reintentos' }
];

const getStatusVariant = (status) => {
  if (status === 'sent') return 'success';
  if (status === 'failed') return 'danger';
  return 'warning';
};
</script>

<template>
  <div class="space-y-6 font-body">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold font-brand text-content">Auditoría de Notificaciones</h2>
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>
    
    <HdCard v-else :noPadding="true">
      <HdTable :columns="columns" :items="logs">
        <template #cell(created_at)="{ value }">
          <span class="text-muted">{{ new Date(value).toLocaleString() }}</span>
        </template>
        <template #cell(user)="{ item }">
          <div class="text-content font-medium">{{ item.user?.name || 'Desconocido' }}</div>
          <div class="text-xs text-muted">{{ item.user?.email }}</div>
        </template>
        <template #cell(event)="{ item }">
          <div class="font-medium text-content">{{ item.event_name }}</div>
          <div class="capitalize text-xs text-muted">{{ item.channel }}</div>
        </template>
        <template #cell(status)="{ item }">
          <HdBadge :variant="getStatusVariant(item.status)">
            {{ item.status }}
          </HdBadge>
          <div v-if="item.error_message" class="text-xs text-danger mt-1 truncate max-w-xs" :title="item.error_message">
            {{ item.error_message }}
          </div>
        </template>
      </HdTable>
    </HdCard>
  </div>
</template>
