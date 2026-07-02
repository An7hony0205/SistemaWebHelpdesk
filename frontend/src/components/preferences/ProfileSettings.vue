<script setup>
import { ref, computed, watch } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { usePreferencesStore } from '../../stores/preferences';
import HdInput from '../ui/HdInput.vue';
import HdTextarea from '../ui/HdTextarea.vue';
import HdButton from '../ui/HdButton.vue';

const authStore = useAuthStore();
const store = usePreferencesStore();

const user = computed(() => authStore.user || {});

const localSettings = ref({ ...store.profile });
const isSaving = ref(false);

watch(() => store.profile, (newVal) => {
  localSettings.value = { ...newVal };
}, { deep: true });

const updateSetting = (key, value) => {
  localSettings.value[key] = value;
};

const applyChanges = async () => {
  isSaving.value = true;
  try {
    await store.updateCategory('profile', { ...localSettings.value });
  } catch (e) {
    console.error(e);
  } finally {
    isSaving.value = false;
  }
};

const resetToDefault = async () => {
  if(confirm('¿Restablecer preferencias de perfil?')) {
    await store.resetCategory('profile');
  }
};
</script>

<template>
  <div class="font-body">
    <h2 class="text-xl font-medium font-brand text-content mb-6">Perfil Público</h2>
    
    <div class="space-y-6">
      <!-- Datos Base (Solo lectura) -->
      <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <HdInput 
            label="Nombre Completo"
            type="text" 
            :modelValue="user.name"
            disabled
          />
          <p class="mt-1 text-xs text-muted">Administrado por tu organización.</p>
        </div>
        
        <div>
          <HdInput 
            label="Correo Electrónico"
            type="email" 
            :modelValue="user.email"
            disabled
          />
          <p class="mt-1 text-xs text-muted">Administrado por tu organización.</p>
        </div>
      </section>

      <!-- Campos Editables -->
      <section class="border-t border-subtle pt-6 grid grid-cols-1 gap-6">
        <div class="md:w-1/2">
          <HdInput 
            label="Cargo / Título"
            type="text" 
            :modelValue="localSettings.job_title"
            @update:modelValue="updateSetting('job_title', $event)"
            placeholder="Ej: Especialista de Soporte"
          />
        </div>
        
        <div class="md:w-1/2">
          <HdInput 
            label="Teléfono"
            type="tel" 
            :modelValue="localSettings.phone"
            @update:modelValue="updateSetting('phone', $event)"
            placeholder="+1 234 567 8900"
          />
        </div>

        <div>
          <HdTextarea 
            label="Firma de Correo/Respuesta"
            :modelValue="localSettings.signature"
            @update:modelValue="updateSetting('signature', $event)"
            rows="3"
            placeholder="Atentamente,&#10;Tu Nombre&#10;Tu Cargo"
          />
          <p class="mt-1 text-xs text-muted">Se adjuntará automáticamente al responder tickets.</p>
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
