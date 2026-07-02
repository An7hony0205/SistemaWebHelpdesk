<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import HdCard from '../components/ui/HdCard.vue';
import HdButton from '../components/ui/HdButton.vue';
import HdToggle from '../components/ui/HdToggle.vue';

const settings = ref({
  openai_api_key: '',
  is_triage_enabled: false,
  is_sentiment_enabled: false
});
const loading = ref(true);
const saving = ref(false);
const showKey = ref(false);

const fetchSettings = async () => {
  loading.value = true;
  try {
    const response = await api.get('/ai/settings');
    settings.value = {
      ...response.data,
      openai_api_key: response.data.openai_api_key ? '********' : ''
    };
  } catch (error) {
    console.error('Error fetching AI settings:', error);
  } finally {
    loading.value = false;
  }
};

const saveSettings = async () => {
  saving.value = true;
  try {
    const payload = {
      is_triage_enabled: settings.value.is_triage_enabled,
      is_sentiment_enabled: settings.value.is_sentiment_enabled
    };
    
    // Solo enviar la API Key si fue modificada
    if (settings.value.openai_api_key && settings.value.openai_api_key !== '********') {
      payload.openai_api_key = settings.value.openai_api_key;
    }

    await api.put('/ai/settings', payload);
    alert('Configuración guardada exitosamente.');
    fetchSettings();
  } catch (error) {
    alert('Error al guardar la configuración.');
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchSettings();
});
</script>

<template>
  <div class="space-y-6 max-w-4xl mx-auto font-body">
    <div>
      <h2 class="text-2xl font-bold font-brand text-content flex items-center">
        <svg class="w-6 h-6 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        Inteligencia Artificial (IA)
      </h2>
      <p class="text-sm text-muted mt-1">Configura las capacidades de IA para potenciar tu HelpDesk.</p>
    </div>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>

    <HdCard v-else :noPadding="true">
      <div class="p-6 space-y-6">
        
        <!-- API Key Provider -->
        <div class="space-y-3 pb-6 border-b border-subtle">
          <h3 class="text-lg font-medium font-brand text-content">Proveedor de IA (OpenAI)</h3>
          <p class="text-sm text-muted">Ingresa tu API Key de OpenAI para habilitar las funcionalidades impulsadas por LLMs.</p>
          
          <div>
            <label class="block text-sm font-medium text-muted mb-1">OpenAI API Key</label>
            <div class="flex rounded-md shadow-sm">
              <input 
                :type="showKey ? 'text' : 'password'" 
                v-model="settings.openai_api_key"
                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-subtle bg-black/5 dark:bg-white/5 text-content focus:ring-primary focus:border-primary sm:text-sm"
                placeholder="sk-..."
              >
              <button 
                type="button" 
                @click="showKey = !showKey"
                class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-subtle bg-surface-elevated text-muted text-sm hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
              >
                {{ showKey ? 'Ocultar' : 'Mostrar' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Capacidades -->
        <div class="space-y-6">
          <h3 class="text-lg font-medium font-brand text-content">Capacidades del Sistema</h3>
          
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <HdToggle v-model="settings.is_triage_enabled" />
            </div>
            <div class="ml-3 text-sm">
              <label class="font-medium text-content cursor-pointer" @click="settings.is_triage_enabled = !settings.is_triage_enabled">Auto-Triaje de Tickets</label>
              <p class="text-muted">La IA leerá el contenido de los tickets nuevos y sugerirá automáticamente la Categoría y Prioridad adecuada.</p>
            </div>
          </div>

          <div class="flex items-start">
            <div class="flex items-center h-5">
              <HdToggle v-model="settings.is_sentiment_enabled" />
            </div>
            <div class="ml-3 text-sm">
              <label class="font-medium text-content cursor-pointer" @click="settings.is_sentiment_enabled = !settings.is_sentiment_enabled">Análisis de Sentimiento</label>
              <p class="text-muted">Analiza el tono de los mensajes de los clientes para identificar frustración y escalar preventivamente.</p>
            </div>
          </div>

        </div>

      </div>
      
      <div class="bg-surface-elevated px-6 py-4 flex justify-end border-t border-subtle">
        <HdButton 
          @click="saveSettings" 
          :disabled="saving"
          variant="primary"
        >
          {{ saving ? 'Guardando...' : 'Guardar Configuración' }}
        </HdButton>
      </div>
    </HdCard>
  </div>
</template>
