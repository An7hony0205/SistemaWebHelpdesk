<script setup>
import { ref, computed, watch } from 'vue';
import { usePreferencesStore } from '../../stores/preferences';
import HdSelect from '../ui/HdSelect.vue';
import HdToggle from '../ui/HdToggle.vue';
import HdButton from '../ui/HdButton.vue';

const store = usePreferencesStore();

const localSettings = ref({ ...store.tickets });
const isSaving = ref(false);

watch(() => store.tickets, (newVal) => {
  localSettings.value = { ...newVal };
}, { deep: true });

const updateSetting = (key, value) => {
  localSettings.value[key] = value;
};

const applyChanges = async () => {
  isSaving.value = true;
  try {
    await store.updateCategory('tickets', { ...localSettings.value });
  } catch (e) {
    console.error(e);
  } finally {
    isSaving.value = false;
  }
};

const resetToDefault = async () => {
  if(confirm('¿Restablecer preferencias de tickets?')) {
    await store.resetCategory('tickets');
  }
};

const itemsPerPageOptions = [
  { value: 10, label: '10 Tickets' },
  { value: 15, label: '15 Tickets' },
  { value: 25, label: '25 Tickets' },
  { value: 50, label: '50 Tickets' }
];
</script>

<template>
  <div class="font-body">
    <h2 class="text-xl font-medium font-brand text-content mb-6">Preferencias de Tickets</h2>
    
    <div class="space-y-8">
      <!-- Vista Predeterminada -->
      <section>
        <h3 class="text-sm font-medium text-muted mb-4">Vista Predeterminada</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <button 
            @click="updateSetting('default_view', 'table')"
            :class="[localSettings.default_view === 'table' ? 'border-primary ring-2 ring-primary/20' : 'border-subtle hover:border-black/20 dark:hover:border-white/20']"
            class="relative rounded-lg border bg-surface p-4 text-left shadow-sm focus:outline-none transition-all"
          >
            <div class="flex items-center justify-between">
              <span class="block text-sm font-medium text-content">Tabla Compacta</span>
              <span v-if="localSettings.default_view === 'table'" class="text-primary text-xl">✓</span>
            </div>
            <p class="text-xs text-muted mt-1">Ideal para visualizar muchos tickets rápidamente.</p>
          </button>

          <button 
            @click="updateSetting('default_view', 'kanban')"
            :class="[localSettings.default_view === 'kanban' ? 'border-primary ring-2 ring-primary/20' : 'border-subtle hover:border-black/20 dark:hover:border-white/20']"
            class="relative rounded-lg border bg-surface p-4 text-left shadow-sm focus:outline-none transition-all"
          >
            <div class="flex items-center justify-between">
              <span class="block text-sm font-medium text-content">Vista Tablero (Kanban)</span>
              <span v-if="localSettings.default_view === 'kanban'" class="text-primary text-xl">✓</span>
            </div>
            <p class="text-xs text-muted mt-1">Ideal para organizar flujos de trabajo visualmente.</p>
          </button>
        </div>
      </section>

      <!-- Paginación -->
      <section class="border-t border-subtle pt-6">
        <h3 class="text-sm font-medium text-muted mb-4">Paginación</h3>
        <div class="md:w-1/3">
          <HdSelect 
            label="Tickets por página"
            :modelValue="localSettings.items_per_page"
            :options="itemsPerPageOptions"
            @update:modelValue="updateSetting('items_per_page', Number($event))"
          />
        </div>
      </section>

      <!-- Automatización en UI -->
      <section class="border-t border-subtle pt-6 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-medium text-muted">Mostrar Tickets Cerrados</h3>
            <p class="text-xs text-muted">Incluir los tickets finalizados en los listados por defecto.</p>
          </div>
          <HdToggle 
            :modelValue="localSettings.show_closed"
            @update:modelValue="updateSetting('show_closed', $event)"
          />
        </div>

        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-medium text-muted">Navegación Automática</h3>
            <p class="text-xs text-muted">Al cerrar un ticket, abrir automáticamente el siguiente de la lista.</p>
          </div>
          <HdToggle 
            :modelValue="localSettings.auto_open_next"
            @update:modelValue="updateSetting('auto_open_next', $event)"
          />
        </div>
      </section>

      <!-- Botones de Acción -->
      <section class="border-t border-subtle pt-6 flex justify-end space-x-3">
        <HdButton 
          @click="resetToDefault"
          variant="ghost"
        >
          Restablecer
        </HdButton>
        <HdButton 
          @click="applyChanges"
          :disabled="isSaving"
          variant="primary"
        >
          {{ isSaving ? 'Guardando...' : 'Aplicar' }}
        </HdButton>
      </section>
    </div>
  </div>
</template>
