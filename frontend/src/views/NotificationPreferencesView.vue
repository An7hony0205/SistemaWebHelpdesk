<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const preferences = ref([]);
const loading = ref(true);

const availableEvents = ['TicketCreated', 'TicketAssigned', 'TicketResolved', 'SlaBreached'];
const availableChannels = ['email']; // Preparado para añadir 'slack', 'whatsapp'

const userPreferences = ref({});

const fetchPreferences = async () => {
  try {
    const response = await api.get('/notification-preferences');
    preferences.value = response.data;
    
    // Initialize model
    availableEvents.forEach(event => {
      const pref = preferences.value.find(p => p.event_name === event);
      userPreferences.value[event] = pref ? pref.channels : ['email'];
    });
  } catch (error) {
    console.error('Error fetching preferences:', error);
  } finally {
    loading.value = false;
  }
};

const savePreference = async (eventName) => {
  try {
    await api.post('/notification-preferences', {
      event_name: eventName,
      channels: userPreferences.value[eventName]
    });
    // Mostrar un pequeño toast o notificación de éxito (MVP: console log)
    console.log('Preferencia guardada');
  } catch (error) {
    console.error('Error saving preference:', error);
  }
};

onMounted(() => {
  fetchPreferences();
});
</script>

<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-content">
        Mis Preferencias de Notificación
      </h2>
    </div>

    <div
      v-if="loading"
      class="flex justify-center py-8"
    >
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
    </div>
    
    <div
      v-else
      class="bg-surface rounded-lg shadow p-6"
    >
      <div class="space-y-6">
        <div
          v-for="eventName in availableEvents"
          :key="eventName"
          class="border-b pb-4 last:border-0 last:pb-0"
        >
          <h3 class="text-lg font-medium text-content">
            {{ eventName }}
          </h3>
          <p class="text-sm text-muted mb-4">
            Selecciona los canales por los que deseas recibir notificaciones cuando ocurra este evento.
          </p>
          
          <div class="flex items-center space-x-6">
            <label
              v-for="channel in availableChannels"
              :key="channel"
              class="inline-flex items-center"
            >
              <input 
                v-model="userPreferences[eventName]" 
                type="checkbox" 
                :value="channel"
                class="rounded border-subtle text-primary focus:ring-primary h-4 w-4"
                @change="savePreference(eventName)"
              >
              <span class="ml-2 text-sm text-muted capitalize">{{ channel }}</span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
