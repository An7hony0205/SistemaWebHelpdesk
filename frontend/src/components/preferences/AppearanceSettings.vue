<script setup>
import { ref, computed, watch } from 'vue';
import { usePreferencesStore } from '../../stores/preferences';
import HdToggle from '../ui/HdToggle.vue';
import HdButton from '../ui/HdButton.vue';

const store = usePreferencesStore();

// Local state for edits
const localSettings = ref({ ...store.appearance });
const isSaving = ref(false);

// Update local state when store changes (e.g. after reset)
watch(() => store.appearance, (newVal) => {
  localSettings.value = { ...newVal };
}, { deep: true });

const updateSetting = (key, value) => {
  localSettings.value[key] = value;
};

const applyChanges = async () => {
  isSaving.value = true;
  try {
    await store.updateCategory('appearance', { ...localSettings.value });
  } catch (e) {
    console.error(e);
  } finally {
    isSaving.value = false;
  }
};

const resetToDefault = async () => {
  if(confirm('¿Estás seguro de restablecer las preferencias de apariencia a los valores por defecto?')) {
    await store.resetCategory('appearance');
  }
};
</script>

<template>
  <div class="font-body">
    <h2 class="text-xl font-medium font-brand text-content mb-6">Apariencia</h2>
    
    <div class="space-y-8">
      <!-- Tema -->
      <section>
        <h3 class="text-sm font-medium text-muted mb-4">Tema de Interfaz</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <button 
            @click="updateSetting('theme', 'light')"
            :class="[localSettings.theme === 'light' ? 'border-primary ring-2 ring-primary/20' : 'border-subtle hover:border-black/20 dark:hover:border-white/20']"
            class="relative rounded-lg border bg-surface p-4 text-left shadow-sm focus:outline-none transition-all"
          >
            <div class="flex items-center justify-between">
              <span class="block text-sm font-medium text-content">Claro</span>
              <span v-if="localSettings.theme === 'light'" class="text-primary text-xl">✓</span>
            </div>
            <div class="mt-2 w-full h-12 bg-gray-100 rounded border border-gray-200"></div>
          </button>

          <button 
            @click="updateSetting('theme', 'dark')"
            :class="[localSettings.theme === 'dark' ? 'border-primary ring-2 ring-primary/20' : 'border-subtle hover:border-black/20 dark:hover:border-white/20']"
            class="relative rounded-lg border bg-surface p-4 text-left shadow-sm focus:outline-none transition-all"
          >
            <div class="flex items-center justify-between">
              <span class="block text-sm font-medium text-content">Oscuro</span>
              <span v-if="localSettings.theme === 'dark'" class="text-primary text-xl">✓</span>
            </div>
            <div class="mt-2 w-full h-12 bg-background rounded border border-subtle"></div>
          </button>

          <button 
            @click="updateSetting('theme', 'system')"
            :class="[localSettings.theme === 'system' ? 'border-primary ring-2 ring-primary/20' : 'border-subtle hover:border-black/20 dark:hover:border-white/20']"
            class="relative rounded-lg border bg-surface p-4 text-left shadow-sm focus:outline-none transition-all"
          >
            <div class="flex items-center justify-between">
              <span class="block text-sm font-medium text-content">Sistema</span>
              <span v-if="localSettings.theme === 'system'" class="text-primary text-xl">✓</span>
            </div>
            <div class="mt-2 w-full h-12 bg-gradient-to-r from-gray-100 to-slate-900 rounded border border-subtle"></div>
          </button>
        </div>
      </section>

      <!-- Color Primario -->
      <section class="border-t border-subtle pt-6">
        <h3 class="text-sm font-medium text-muted mb-4">Color de Acento</h3>
        <div class="flex items-center space-x-4">
          <input 
            type="color" 
            :value="localSettings.primary_color"
            @change="updateSetting('primary_color', $event.target.value)"
            class="h-10 w-20 cursor-pointer rounded border border-subtle bg-surface"
          >
          <span class="text-sm text-muted">Personaliza el color principal de los botones y resaltes.</span>
        </div>
      </section>

      <!-- Toggles -->
      <section class="border-t border-subtle pt-6 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-medium text-muted">Modo Compacto</h3>
            <p class="text-xs text-muted">Reduce los márgenes y paddings para mostrar más información en pantalla.</p>
          </div>
          <HdToggle 
            :modelValue="localSettings.compact_mode"
            @update:modelValue="updateSetting('compact_mode', $event)"
          />
        </div>

        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-medium text-muted">Reducir Animaciones</h3>
            <p class="text-xs text-muted">Desactiva transiciones y animaciones innecesarias.</p>
          </div>
          <HdToggle 
            :modelValue="localSettings.reduced_animations"
            @update:modelValue="updateSetting('reduced_animations', $event)"
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
