<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import { usePreferencesStore } from '../stores/preferences';

import HdButton from '../components/ui/HdButton.vue';
import HdBadge from '../components/ui/HdBadge.vue';
import HdCard from '../components/ui/HdCard.vue';
import HdInput from '../components/ui/HdInput.vue';
import HdSelect from '../components/ui/HdSelect.vue';
import HdTextarea from '../components/ui/HdTextarea.vue';
import HdTable from '../components/ui/HdTable.vue';

const tickets = ref([]);
const categories = ref([]);
const loading = ref(true);
const showModal = ref(false);
const submitting = ref(false);

const router = useRouter();
const prefsStore = usePreferencesStore();

const filteredTickets = computed(() => {
  const settings = prefsStore.tickets;
  if (settings.show_closed) {
    return tickets.value;
  }
  return tickets.value.filter(t => t.status !== 'Cerrado' && t.status !== 'Resuelto');
});

const newTicket = ref({
  title: '',
  description: '',
  category_id: ''
});

const fetchTickets = async () => {
  loading.value = true;
  try {
    const response = await api.get('/tickets');
    tickets.value = response.data;
  } catch (error) {
    console.error('Error fetching tickets:', error);
  } finally {
    loading.value = false;
  }
};

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories');
    categories.value = response.data;
    if (categories.value.length > 0) {
      newTicket.value.category_id = categories.value[0].id;
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const createTicket = async () => {
  if (!newTicket.value.title || !newTicket.value.description || !newTicket.value.category_id) return;
  submitting.value = true;
  try {
    await api.post('/tickets', newTicket.value);
    showModal.value = false;
    newTicket.value = { title: '', description: '', category_id: categories.value.length ? categories.value[0].id : '' };
    await fetchTickets();
  } catch (error) {
    console.error('Error creating ticket:', error);
    alert('Error al crear el ticket');
  } finally {
    submitting.value = false;
  }
};

onMounted(async () => {
  if (!prefsStore.initialized) {
    await prefsStore.fetchAll();
  }
  await fetchCategories();
  await fetchTickets();
});

const goToTicket = (id) => {
  router.push(`/tickets/${id}`);
};

const tableColumns = [
  { key: 'id', label: 'ID' },
  { key: 'title', label: 'Título' },
  { key: 'status', label: 'Estado' },
  { key: 'created_at', label: 'Creado' }
];
</script>

<template>
  <HdCard :no-padding="true">
    <template #header>
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-content">
          Todos los Tickets
        </h3>
        <HdButton
          variant="primary"
          size="sm"
          @click="showModal = true"
        >
          Nuevo Ticket
        </HdButton>
      </div>
    </template>
    
    <div
      v-if="loading"
      class="p-6 text-center text-muted font-body"
    >
      Cargando tickets...
    </div>
    
    <div
      v-else-if="filteredTickets.length === 0"
      class="p-6 text-center text-muted font-body"
    >
      No hay tickets disponibles.
    </div>

    <!-- Kanban View -->
    <div
      v-else-if="prefsStore.tickets.default_view === 'kanban'"
      class="p-6 font-body"
    >
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tableros de ejemplo simplificados -->
        <div class="bg-surface-elevated p-4 rounded-lg border border-subtle">
          <h4 class="text-muted font-medium mb-4">
            Abiertos
          </h4>
          <div
            v-for="ticket in filteredTickets.filter(t => t.status === 'Abierto')"
            :key="ticket.id"
            class="bg-surface p-3 rounded-md mb-2 shadow-base cursor-pointer border border-subtle hover:bg-black/5 transition-colors"
            @click="goToTicket(ticket.id)"
          >
            <p class="text-sm text-content font-medium">
              #{{ ticket.id }} {{ ticket.title }}
            </p>
            <p class="text-xs text-muted mt-2">
              {{ new Date(ticket.created_at).toLocaleDateString() }}
            </p>
          </div>
        </div>
        <div class="bg-surface-elevated p-4 rounded-lg border border-subtle">
          <h4 class="text-muted font-medium mb-4">
            En Progreso / Otros
          </h4>
          <div
            v-for="ticket in filteredTickets.filter(t => t.status !== 'Abierto' && t.status !== 'Cerrado' && t.status !== 'Resuelto')"
            :key="ticket.id"
            class="bg-surface p-3 rounded-md mb-2 shadow-base cursor-pointer border border-subtle hover:bg-black/5 transition-colors"
            @click="goToTicket(ticket.id)"
          >
            <p class="text-sm text-content font-medium">
              #{{ ticket.id }} {{ ticket.title }}
            </p>
            <p class="text-xs text-muted mt-2">
              {{ new Date(ticket.created_at).toLocaleDateString() }}
            </p>
          </div>
        </div>
        <div class="bg-surface-elevated p-4 rounded-lg border border-subtle">
          <h4 class="text-muted font-medium mb-4">
            Cerrados
          </h4>
          <div
            v-for="ticket in filteredTickets.filter(t => t.status === 'Cerrado' || t.status === 'Resuelto')"
            :key="ticket.id"
            class="bg-surface p-3 rounded-md mb-2 shadow-base cursor-pointer border border-subtle opacity-70 hover:bg-black/5 transition-colors"
            @click="goToTicket(ticket.id)"
          >
            <p class="text-sm text-content font-medium line-through">
              #{{ ticket.id }} {{ ticket.title }}
            </p>
            <p class="text-xs text-muted mt-2">
              {{ new Date(ticket.created_at).toLocaleDateString() }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Table View -->
    <HdTable 
      v-else 
      :columns="tableColumns" 
      :items="filteredTickets.slice(0, prefsStore.tickets.items_per_page)"
    >
      <template #cell(id)="{ value }">
        <span
          class="font-medium cursor-pointer text-primary hover:underline"
          @click="goToTicket(value)"
        >#{{ value }}</span>
      </template>
      <template #cell(status)="{ value }">
        <HdBadge :variant="value === 'Abierto' ? 'success' : 'neutral'">
          {{ value }}
        </HdBadge>
      </template>
      <template #cell(created_at)="{ value }">
        {{ new Date(value).toLocaleDateString() }}
      </template>
    </HdTable>

    <!-- Modal de Nuevo Ticket -->
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
          <form @submit.prevent="createTicket">
            <div class="px-6 pt-5 pb-4">
              <h3 class="text-lg leading-6 font-medium font-brand text-content mb-4">
                Crear Nuevo Ticket
              </h3>
              <div class="space-y-4">
                <HdInput 
                  v-model="newTicket.title" 
                  label="Título" 
                  required 
                  placeholder="Ej: Problema con la impresora"
                />
                <HdSelect 
                  v-model="newTicket.category_id" 
                  label="Categoría" 
                  :options="categories" 
                  required 
                />
                <HdTextarea 
                  v-model="newTicket.description" 
                  label="Descripción" 
                  required 
                  rows="4" 
                  placeholder="Detalla tu problema aquí..."
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
                {{ submitting ? 'Creando...' : 'Crear Ticket' }}
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
