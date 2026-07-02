<script setup>
import { ref, computed, watch } from 'vue';
import { usePreferencesStore } from '../../stores/preferences';
import HdToggle from '../ui/HdToggle.vue';
import HdButton from '../ui/HdButton.vue';
import HdInput from '../ui/HdInput.vue';

const store = usePreferencesStore();

const localSettings = ref({ ...store.notifications });
const isSaving = ref(false);

watch(() => store.notifications, (newVal) => {
  localSettings.value = { ...newVal };
}, { deep: true });

const updateSetting = (key, value) => {
  localSettings.value[key] = value;
};

const applyChanges = async () => {
  isSaving.value = true;
  try {
    await store.updateCategory('notifications', { ...localSettings.value });
  } catch (e) {
    console.error(e);
  } finally {
    isSaving.value = false;
  }
};

const resetToDefault = async () => {
  if(confirm('¿Restablecer preferencias de notificaciones?')) {
    await store.resetCategory('notifications');
  }
};
</script>

<template>
  <div class="font-body">
    <h2 class="text-xl font-medium font-brand text-content mb-6">
      Preferencias de Notificación
    </h2>
    
    <div class="space-y-8">
      <!-- Canales de Notificación -->
      <section>
        <h3 class="text-sm font-medium text-muted mb-4">
          Canales de Recepción
        </h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 bg-surface border border-subtle rounded-lg shadow-base">
            <div>
              <p class="text-sm font-medium font-brand text-content">
                Notificaciones en la App
              </p>
              <p class="text-xs text-muted">
                Recibir alertas push y notificaciones en la campana superior.
              </p>
            </div>
            <HdToggle 
              :model-value="localSettings.in_app"
              @update:model-value="updateSetting('in_app', $event)"
            />
          </div>
          
          <div class="flex items-center justify-between p-4 bg-surface border border-subtle rounded-lg shadow-base">
            <div>
              <p class="text-sm font-medium font-brand text-content">
                Correos Electrónicos
              </p>
              <p class="text-xs text-muted">
                Enviar alertas a tu dirección de correo registrada.
              </p>
            </div>
            <HdToggle 
              :model-value="localSettings.email"
              @update:model-value="updateSetting('email', $event)"
            />
          </div>
        </div>
      </section>

      <!-- Frecuencia -->
      <section class="border-t border-subtle pt-6">
        <h3 class="text-sm font-medium text-muted mb-4">
          Frecuencia de Correos
        </h3>
        <div class="space-y-4">
          <div class="flex items-center">
            <input 
              id="freq_instant" 
              type="radio" 
              value="instant" 
              :checked="localSettings.frequency === 'instant'"
              class="h-4 w-4 text-primary focus:ring-primary border-subtle bg-black/10 dark:bg-white/10"
              @change="updateSetting('frequency', 'instant')"
            >
            <label
              for="freq_instant"
              class="ml-3 block text-sm font-medium text-content"
            >
              Inmediata (Recomendado para Soporte)
            </label>
          </div>
          <div class="flex items-center">
            <input 
              id="freq_daily" 
              type="radio" 
              value="daily" 
              :checked="localSettings.frequency === 'daily'"
              class="h-4 w-4 text-primary focus:ring-primary border-subtle bg-black/10 dark:bg-white/10"
              @change="updateSetting('frequency', 'daily')"
            >
            <label
              for="freq_daily"
              class="ml-3 block text-sm font-medium text-content"
            >
              Resumen Diario (Un solo correo al final del día)
            </label>
          </div>
        </div>
      </section>

      <!-- Horas de Silencio (Do Not Disturb) -->
      <section class="border-t border-subtle pt-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-sm font-medium text-muted">
              Horas de Silencio (DND)
            </h3>
            <p class="text-xs text-muted">
              Pausar todas las notificaciones no críticas en un rango horario.
            </p>
          </div>
          <HdToggle 
            :model-value="localSettings.quiet_hours_enabled"
            @update:model-value="updateSetting('quiet_hours_enabled', $event)"
          />
        </div>

        <div
          v-if="localSettings.quiet_hours_enabled"
          class="grid grid-cols-2 gap-4 mt-4 bg-black/5 dark:bg-white/5 p-4 rounded-lg border border-subtle"
        >
          <div>
            <HdInput 
              label="Inicio (Silenciar desde)"
              type="time" 
              :model-value="localSettings.quiet_hours_start"
              @update:model-value="updateSetting('quiet_hours_start', $event)"
            />
          </div>
          <div>
            <HdInput 
              label="Fin (Reanudar a las)"
              type="time" 
              :model-value="localSettings.quiet_hours_end"
              @update:model-value="updateSetting('quiet_hours_end', $event)"
            />
          </div>
        </div>
      </section>

      <!-- Botones de Acción -->
      <section class="border-t border-subtle pt-6 flex justify-end space-x-3">
        <HdButton 
          variant="ghost"
          @click="resetToDefault"
        >
          Restablecer
        </HdButton>
        <HdButton 
          :disabled="isSaving"
          variant="primary"
          @click="applyChanges"
        >
          {{ isSaving ? 'Guardando...' : 'Aplicar' }}
        </HdButton>
      </section>
    </div>
  </div>
</template>
