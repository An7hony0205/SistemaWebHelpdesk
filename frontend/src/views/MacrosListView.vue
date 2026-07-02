<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import HdCard from '../components/ui/HdCard.vue';
import HdButton from '../components/ui/HdButton.vue';
import HdTable from '../components/ui/HdTable.vue';
import HdBadge from '../components/ui/HdBadge.vue';
import HdInput from '../components/ui/HdInput.vue';
import HdTextarea from '../components/ui/HdTextarea.vue';
import HdToggle from '../components/ui/HdToggle.vue';

const macros = ref([]);
const loading = ref(true);
const showModal = ref(false);
const submitting = ref(false);

const newMacro = ref({
  id: null,
  title: '',
  content: '',
  is_active: true
});

const tableColumns = [
  { key: 'title', label: 'Título' },
  { key: 'is_active', label: 'Estado' },
  { key: 'actions', label: 'Acciones' }
];

const fetchMacros = async () => {
  loading.value = true;
  try {
    const response = await api.get('/macros');
    macros.value = response.data;
  } catch (error) {
    console.error('Error fetching macros:', error);
  } finally {
    loading.value = false;
  }
};

const openModal = (macro = null) => {
  if (macro) {
    newMacro.value = { ...macro };
  } else {
    newMacro.value = { id: null, title: '', content: '', is_active: true };
  }
  showModal.value = true;
};

const saveMacro = async () => {
  if (!newMacro.value.title || !newMacro.value.content) return;
  
  submitting.value = true;
  try {
    if (newMacro.value.id) {
      await api.put(`/macros/${newMacro.value.id}`, newMacro.value);
    } else {
      await api.post('/macros', newMacro.value);
    }
    showModal.value = false;
    await fetchMacros();
  } catch (error) {
    console.error('Error saving macro:', error);
    alert('Error al guardar la macro');
  } finally {
    submitting.value = false;
  }
};

const deleteMacro = async (id) => {
  if (!confirm('¿Seguro que deseas eliminar esta macro?')) return;
  try {
    await api.delete(`/macros/${id}`);
    await fetchMacros();
  } catch (error) {
    console.error('Error deleting macro:', error);
  }
};

onMounted(() => {
  fetchMacros();
});
</script>

<template>
  <HdCard :no-padding="true">
    <template #header>
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-content">
          Respuestas Predefinidas (Macros)
        </h3>
        <HdButton
          variant="primary"
          size="sm"
          @click="openModal()"
        >
          Nueva Macro
        </HdButton>
      </div>
    </template>
    
    <div
      v-if="loading"
      class="p-6 text-center text-muted font-body"
    >
      Cargando macros...
    </div>
    
    <HdTable 
      v-else 
      :columns="tableColumns" 
      :items="macros"
    >
      <template #cell(title)="{ value }">
        <span class="font-medium text-content">{{ value }}</span>
      </template>
      <template #cell(is_active)="{ value }">
        <HdBadge :variant="value ? 'success' : 'neutral'">
          {{ value ? 'Activo' : 'Inactivo' }}
        </HdBadge>
      </template>
      <template #cell(actions)="{ item }">
        <div class="flex space-x-2">
          <HdButton
            variant="secondary"
            size="sm"
            @click="openModal(item)"
          >
            Editar
          </HdButton>
          <HdButton
            variant="ghost"
            size="sm"
            class="text-danger"
            @click="deleteMacro(item.id)"
          >
            Eliminar
          </HdButton>
        </div>
      </template>
    </HdTable>

    <!-- Modal Form -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 overflow-y-auto"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
          class="fixed inset-0 transition-opacity"
          aria-hidden="true"
          @click="showModal = false"
        >
          <div class="absolute inset-0 bg-background/75 backdrop-blur-sm" />
        </div>
        <span
          class="hidden sm:inline-block sm:align-middle sm:h-screen"
          aria-hidden="true"
        >&#8203;</span>
        <div class="relative z-10 inline-block align-bottom bg-surface rounded-lg text-left overflow-hidden shadow-modal border border-subtle transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="saveMacro">
            <div class="px-6 pt-5 pb-4">
              <h3 class="text-lg leading-6 font-medium font-brand text-content mb-4">
                {{ newMacro.id ? 'Editar Macro' : 'Nueva Macro' }}
              </h3>
              <div class="space-y-4">
                <HdInput 
                  v-model="newMacro.title" 
                  label="Título de la Macro" 
                  required 
                  placeholder="Ej: Saludo inicial"
                />
                <HdTextarea 
                  v-model="newMacro.content" 
                  label="Contenido" 
                  required 
                  rows="5" 
                  placeholder="Hola, gracias por contactarnos..."
                />
                <HdToggle 
                  v-model="newMacro.is_active" 
                  label="Activo" 
                  description="Las macros inactivas no aparecerán en los tickets."
                />
              </div>
            </div>
            <div class="bg-surface-elevated px-6 py-4 sm:flex sm:flex-row-reverse border-t border-subtle">
              <HdButton
                type="submit"
                :disabled="submitting"
                variant="primary"
                class="w-full sm:w-auto sm:ml-3"
              >
                {{ submitting ? 'Guardando...' : 'Guardar' }}
              </HdButton>
              <HdButton
                type="button"
                variant="secondary"
                class="mt-3 w-full sm:mt-0 sm:w-auto"
                @click="showModal = false"
              >
                Cancelar
              </HdButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </HdCard>
</template>
