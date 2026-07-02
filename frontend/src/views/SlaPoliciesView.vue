<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import HdButton from '../components/ui/HdButton.vue';
import HdCard from '../components/ui/HdCard.vue';
import HdTable from '../components/ui/HdTable.vue';
import HdBadge from '../components/ui/HdBadge.vue';

const policies = ref([]);
const loading = ref(true);

const fetchPolicies = async () => {
  try {
    const response = await api.get('/sla-policies');
    policies.value = response.data;
  } catch (error) {
    console.error('Error fetching SLA policies:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchPolicies();
});

const columns = [
  { key: 'name', label: 'Nombre' },
  { key: 'status', label: 'Estado' },
  { key: 'conditions', label: 'Condiciones' },
  { key: 'actions', label: 'Acciones' }
];
</script>

<template>
  <div class="space-y-6 font-body">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold font-brand text-content">
        Políticas de SLA
      </h2>
      <HdButton variant="primary">
        Crear Política
      </HdButton>
    </div>

    <div
      v-if="loading"
      class="flex justify-center py-8"
    >
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
    </div>
    
    <HdCard
      v-else
      :no-padding="true"
    >
      <HdTable
        :columns="columns"
        :items="policies"
      >
        <template #cell(name)="{ item }">
          <div class="text-sm font-medium font-brand text-content">
            {{ item.name }}
          </div>
          <div class="text-sm text-muted">
            {{ item.description }}
          </div>
        </template>
        <template #cell(status)="{ item }">
          <HdBadge :variant="item.is_active ? 'success' : 'neutral'">
            {{ item.is_active ? 'Activa' : 'Inactiva' }}
          </HdBadge>
        </template>
        <template #cell(conditions)="{ item }">
          <span class="text-muted">{{ item.conditions ? 'Configuradas' : 'Por Defecto' }}</span>
        </template>
        <template #cell(actions)>
          <HdButton
            variant="ghost"
            size="sm"
            class="text-primary"
          >
            Editar
          </HdButton>
        </template>
      </HdTable>
    </HdCard>
  </div>
</template>
