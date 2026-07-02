<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import HdCard from '../components/ui/HdCard.vue';
import HdBadge from '../components/ui/HdBadge.vue';
import HdButton from '../components/ui/HdButton.vue';
import HdTextarea from '../components/ui/HdTextarea.vue';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const ticketId = route.params.id;

const ticket = ref(null);
const newComment = ref('');
const loading = ref(true);
const submitting = ref(false);

const fetchTicket = async () => {
  try {
    const response = await api.get(`/tickets/${ticketId}`);
    ticket.value = response.data;
  } catch (error) {
    console.error('Error fetching ticket:', error);
  }
};

const submitComment = async () => {
  if (!newComment.value) return;
  submitting.value = true;
  try {
    await api.post(`/tickets.comments`, {
      ticket_id: ticketId,
      description: newComment.value,
      is_internal: false
    });
    newComment.value = '';
    await fetchTicket();
  } catch (error) {
    console.error('Error submitting comment:', error);
  } finally {
    submitting.value = false;
  }
};

onMounted(async () => {
  loading.value = true;
  await fetchTicket();
  loading.value = false;
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
          @click="router.push('/login')"
        >
          Salir
        </HdButton>
      </div>
    </header>

    <main class="max-w-4xl mx-auto p-6 mt-8">
      <HdButton
        variant="ghost"
        size="sm"
        class="mb-4"
        @click="router.push('/portal')"
      >
        &larr; Volver a mis solicitudes
      </HdButton>

      <div
        v-if="loading"
        class="text-center text-muted"
      >
        Cargando detalles...
      </div>
      <div
        v-else-if="!ticket"
        class="text-center text-danger"
      >
        Ticket no encontrado.
      </div>
      
      <div
        v-else
        class="space-y-6"
      >
        <HdCard>
          <div class="flex justify-between items-start mb-4">
            <div>
              <h2 class="text-2xl font-brand font-bold text-content">
                #{{ ticket.id }} - {{ ticket.title }}
              </h2>
              <p class="text-sm text-muted mt-1">
                {{ new Date(ticket.created_at).toLocaleString() }} &bull; {{ ticket.category?.name || 'General' }}
              </p>
            </div>
            <HdBadge
              :variant="ticket.status?.name === 'Abierto' ? 'success' : 'neutral'"
              size="lg"
            >
              {{ ticket.status?.name || ticket.status_id }}
            </HdBadge>
          </div>
          <div class="text-content whitespace-pre-wrap">
            {{ ticket.description }}
          </div>
        </HdCard>

        <!-- Historial de respuestas -->
        <div class="space-y-4">
          <h3 class="text-lg font-brand font-medium text-content">
            Conversación
          </h3>
          <div
            v-for="comment in ticket.comments"
            :key="comment.id" 
            class="bg-surface p-4 rounded-lg shadow-sm border border-subtle"
            :class="{ 'ml-12 border-primary': comment.user_id !== ticket.user_id }"
          >
            <p class="text-sm font-medium text-content mb-2">
              {{ comment.user?.name || 'Usuario' }} <span class="text-muted font-normal ml-2">{{ new Date(comment.created_at).toLocaleString() }}</span>
            </p>
            <div class="text-content whitespace-pre-wrap">
              {{ comment.description }}
            </div>
          </div>
        </div>

        <!-- Responder -->
        <HdCard
          v-if="ticket.status?.name !== 'Cerrado'"
          :no-padding="false"
        >
          <h3 class="text-lg font-brand font-medium text-content mb-4">
            Enviar una respuesta
          </h3>
          <HdTextarea 
            v-model="newComment" 
            rows="3" 
            placeholder="Escribe tu mensaje aquí..."
            class="mb-4"
          />
          <div class="flex justify-end">
            <HdButton
              variant="primary"
              :disabled="submitting || !newComment"
              @click="submitComment"
            >
              {{ submitting ? 'Enviando...' : 'Responder' }}
            </HdButton>
          </div>
        </HdCard>
        <div
          v-else
          class="text-center p-4 bg-surface rounded border border-subtle text-muted"
        >
          Este ticket está cerrado y no admite más respuestas.
        </div>
      </div>
    </main>
  </div>
</template>
