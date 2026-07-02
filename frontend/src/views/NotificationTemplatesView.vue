<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import HdCard from '../components/ui/HdCard.vue';
import HdButton from '../components/ui/HdButton.vue';
import HdTable from '../components/ui/HdTable.vue';

const templates = ref([]);
const loading = ref(true);

const fetchTemplates = async () => {
  try {
    const response = await api.get('/notification-templates');
    templates.value = response.data;
  } catch (error) {
    console.error('Error fetching templates:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchTemplates();
});

const columns = [
  { key: 'event_name', label: 'Evento' },
  { key: 'channel', label: 'Canal' },
  { key: 'locale', label: 'Idioma' },
  { key: 'version', label: 'Versión' },
  { key: 'actions', label: 'Acciones' }
];
</script>

<template>
  <div class="space-y-6 font-body">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold font-brand text-content">Plantillas de Notificación</h2>
      <HdButton variant="primary">
        Nueva Plantilla
      </HdButton>
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>
    
    <HdCard v-else :noPadding="true">
      <HdTable :columns="columns" :items="templates">
        <template #cell(event_name)="{ value }">
          <span class="font-medium font-brand text-content">{{ value }}</span>
        </template>
        <template #cell(channel)="{ value }">
          <span class="text-muted capitalize">{{ value }}</span>
        </template>
        <template #cell(locale)="{ value }">
          <span class="text-muted uppercase">{{ value }}</span>
        </template>
        <template #cell(version)="{ value }">
          <span class="text-muted">v{{ value }}</span>
        </template>
        <template #cell(actions)="{ item }">
          <HdButton variant="ghost" size="sm" class="text-primary mr-2">Preview</HdButton>
          <HdButton variant="ghost" size="sm" class="text-primary">Editar</HdButton>
        </template>
      </HdTable>
    </HdCard>
  </div>
</template>
