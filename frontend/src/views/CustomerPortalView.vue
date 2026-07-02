<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import HdButton from '../components/ui/HdButton.vue';
import HdCard from '../components/ui/HdCard.vue';
import HdBadge from '../components/ui/HdBadge.vue';
import HdInput from '../components/ui/HdInput.vue';
import HdTextarea from '../components/ui/HdTextarea.vue';
import HdSelect from '../components/ui/HdSelect.vue';

const authStore = useAuthStore();
const router = useRouter();

const tickets = ref([]);
const categories = ref([]);
const loading = ref(true);
const showModal = ref(false);
const submitting = ref(false);

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
  } finally {
    submitting.value = false;
  }
};

const goToTicket = (id) => {
  router.push(`/portal/tickets/${id}`);
};

const handleLogout = () => {
  authStore.logout();
  router.push({ name: 'Login' });
};

onMounted(async () => {
  await fetchCategories();
  await fetchTickets();
});
</script>

<template>
  <div class="min-h-screen bg-background font-body">
    <!-- Header simple -->
    <header class="bg-surface shadow-sm border-b border-subtle h-16 flex items-center justify-between px-6">
      <div class="flex items-center space-x-4">
        <span class="text-xl font-brand font-bold text-primary">Portal de Ayuda</span>
      </div>
      <div class="flex items-center space-x-4">
        <span class="text-sm font-medium text-muted">Hola, {{ authStore.user?.name }}</span>
        <HdButton
          variant="ghost"
          size="sm"
          class="text-danger"
          @click="handleLogout"
        >
          Salir
        </HdButton>
      </div>
    </header>

    <main class="max-w-5xl mx-auto p-6 mt-8">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-brand font-bold text-content">
          Mis Solicitudes
        </h1>
        <HdButton
          variant="primary"
          @click="showModal = true"
        >
          Nueva Solicitud
        </HdButton>
      </div>

      <div
        v-if="loading"
        class="text-center text-muted"
      >
        Cargando tus solicitudes...
      </div>
      
      <div
        v-else-if="tickets.length === 0"
        class="text-center bg-surface p-10 rounded-lg shadow-sm border border-subtle"
      >
        <p class="text-muted text-lg">
          No tienes ninguna solicitud abierta.
        </p>
        <HdButton
          variant="secondary"
          class="mt-4"
          @click="showModal = true"
        >
          Crear tu primera solicitud
        </HdButton>
      </div>

      <div
        v-else
        class="grid gap-4"
      >
        <div
          v-for="ticket in tickets"
          :key="ticket.id" 
          class="bg-surface p-4 rounded-lg shadow-sm border border-subtle hover:border-primary cursor-pointer transition-colors flex justify-between items-center"
          @click="goToTicket(ticket.id)"
        >
          <div>
            <h3 class="text-lg font-medium text-content">
              #{{ ticket.id }} - {{ ticket.title }}
            </h3>
            <p class="text-sm text-muted mt-1">
              Creado el {{ new Date(ticket.created_at).toLocaleDateString() }} &bull; {{ ticket.category?.name || 'General' }}
            </p>
          </div>
          <div>
            <HdBadge :variant="ticket.status?.name === 'Abierto' ? 'success' : 'neutral'">
              {{ ticket.status?.name || ticket.status_id }}
            </HdBadge>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal de Creación -->
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
                Nueva Solicitud de Soporte
              </h3>
              <div class="space-y-4">
                <HdInput 
                  v-model="newTicket.title" 
                  label="Asunto" 
                  required 
                  placeholder="Resumen de tu problema"
                />
                <HdSelect 
                  v-model="newTicket.category_id" 
                  label="¿De qué trata?" 
                  :options="categories" 
                  required 
                />
                <HdTextarea 
                  v-model="newTicket.description" 
                  label="Detalles" 
                  required 
                  rows="4" 
                  placeholder="Por favor describe el problema detalladamente..."
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
                {{ submitting ? 'Enviando...' : 'Enviar Solicitud' }}
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
  </div>
</template>
