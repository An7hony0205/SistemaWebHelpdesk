<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import HdButton from '../components/ui/HdButton.vue';
import HdCard from '../components/ui/HdCard.vue';
import HdTable from '../components/ui/HdTable.vue';
import HdBadge from '../components/ui/HdBadge.vue';

const router = useRouter();
const activeTab = ref('apikeys');
const apiKeys = ref([]);
const webhooks = ref([]);
const loading = ref(true);

const fetchApiKeys = async () => {
  try {
    const response = await api.get('/integrations/api-keys');
    apiKeys.value = response.data;
  } catch (error) {
    console.error('Error fetching API keys:', error);
  }
};

const fetchWebhooks = async () => {
  try {
    const response = await api.get('/integrations/webhooks');
    webhooks.value = response.data;
  } catch (error) {
    console.error('Error fetching webhooks:', error);
  }
};

const fetchData = async () => {
  loading.value = true;
  await Promise.all([fetchApiKeys(), fetchWebhooks()]);
  loading.value = false;
};

const generateApiKey = async () => {
  const name = prompt('Nombre para la nueva API Key (ej. Script de Servidor):');
  if (!name) return;
  
  try {
    const response = await api.post('/integrations/api-keys', { name });
    alert(`IMPORTANTE: Copia este token ahora. No volverá a mostrarse.\n\nToken: ${response.data.plain_text_token}`);
    fetchApiKeys();
  } catch (error) {
    alert('Error al generar API Key');
  }
};

const deleteApiKey = async (id) => {
  if (confirm('¿Estás seguro de revocar esta API Key?')) {
    try {
      await api.delete(`/integrations/api-keys/${id}`);
      fetchApiKeys();
    } catch (error) {
      alert('Error revoking key');
    }
  }
};

const createWebhook = async () => {
  const url = prompt('URL del Webhook (ej. https://tu-sistema.com/webhook):');
  if (!url) return;
  
  try {
    await api.post('/integrations/webhooks', {
      url,
      description: 'Webhook creado desde panel',
      events: ['ticket.created'] // Default para MVP
    });
    fetchWebhooks();
  } catch (error) {
    alert('Error creando Webhook. Asegúrate que sea una URL válida.');
  }
};

onMounted(() => {
  fetchData();
});

const apiKeyColumns = [
  { key: 'name', label: 'Nombre' },
  { key: 'created_at', label: 'Creado' },
  { key: 'actions', label: 'Acciones' }
];

const webhookColumns = [
  { key: 'url', label: 'URL Destino' },
  { key: 'events', label: 'Eventos (Suscripciones)' },
  { key: 'secret', label: 'HMAC Secret' }
];
</script>

<template>
  <div class="space-y-6 font-body">
    <div>
      <h2 class="text-2xl font-bold font-brand text-content">
        Integraciones & Desarrolladores
      </h2>
      <p class="text-sm text-muted mt-1">
        Conecta tu HelpDesk con sistemas externos mediante API y Webhooks.
      </p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-subtle">
      <nav class="-mb-px flex space-x-8 font-brand">
        <button 
          :class="['whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors', activeTab === 'apikeys' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-content hover:border-subtle']"
          @click="activeTab = 'apikeys'"
        >
          API Keys (Inbound)
        </button>
        <button 
          :class="['whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors', activeTab === 'webhooks' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-content hover:border-subtle']"
          @click="activeTab = 'webhooks'"
        >
          Webhooks (Outbound)
        </button>
      </nav>
    </div>

    <div
      v-if="loading"
      class="flex justify-center py-12"
    >
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
    </div>

    <!-- API Keys Tab -->
    <div
      v-else-if="activeTab === 'apikeys'"
      class="space-y-4"
    >
      <div class="flex justify-end">
        <HdButton
          variant="primary"
          @click="generateApiKey"
        >
          Generar API Key
        </HdButton>
      </div>

      <HdCard :no-padding="true">
        <HdTable
          :columns="apiKeyColumns"
          :items="apiKeys"
        >
          <template #cell(name)="{ value }">
            <span class="font-medium text-content">{{ value }}</span>
          </template>
          <template #cell(created_at)="{ value }">
            {{ new Date(value).toLocaleDateString() }}
          </template>
          <template #cell(actions)="{ item }">
            <HdButton
              variant="ghost"
              size="sm"
              class="text-danger"
              @click="deleteApiKey(item.id)"
            >
              Revocar
            </HdButton>
          </template>
        </HdTable>
      </HdCard>
    </div>

    <!-- Webhooks Tab -->
    <div
      v-else-if="activeTab === 'webhooks'"
      class="space-y-4"
    >
      <div class="flex justify-end">
        <HdButton
          variant="primary"
          @click="createWebhook"
        >
          Añadir Webhook
        </HdButton>
      </div>

      <HdCard :no-padding="true">
        <HdTable
          :columns="webhookColumns"
          :items="webhooks"
        >
          <template #cell(url)="{ value }">
            <span class="font-medium text-content">{{ value }}</span>
          </template>
          <template #cell(events)="{ value }">
            <div class="flex flex-wrap gap-1">
              <HdBadge
                v-for="ev in value"
                :key="ev.id"
                variant="neutral"
              >
                {{ ev.event_name }}
              </HdBadge>
            </div>
          </template>
          <template #cell(secret)="{ value }">
            <span class="font-mono text-xs">{{ value }}</span>
          </template>
        </HdTable>
      </HdCard>
    </div>
  </div>
</template>
