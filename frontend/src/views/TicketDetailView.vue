<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import HdCard from '../components/ui/HdCard.vue';
import HdBadge from '../components/ui/HdBadge.vue';
import HdButton from '../components/ui/HdButton.vue';
import HdTextarea from '../components/ui/HdTextarea.vue';

const route = useRoute();
const router = useRouter();
const ticketId = route.params.id;

const ticket = ref(null);
const activities = ref([]);
const macros = ref([]);
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

const fetchActivities = async () => {
  try {
    const response = await api.get(`/tickets/${ticketId}/activities`);
    activities.value = response.data;
  } catch (error) {
    console.error('Error fetching activities:', error);
  }
};

const fetchMacros = async () => {
  try {
    const response = await api.get('/macros');
    macros.value = response.data.filter(m => m.is_active);
  } catch (error) {
    console.error('Error fetching macros:', error);
  }
};

const insertMacro = (event) => {
  const macroId = event.target.value;
  if (!macroId) return;
  const macro = macros.value.find(m => m.id == macroId);
  if (macro) {
    newComment.value = newComment.value ? newComment.value + '\n\n' + macro.content : macro.content;
  }
  event.target.value = ''; // reset select
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
  await Promise.all([fetchTicket(), fetchActivities(), fetchMacros()]);
  loading.value = false;
});
</script>

<template>
  <div
    v-if="loading"
    class="p-6 text-center text-muted font-body"
  >
    Cargando detalles del ticket...
  </div>
  <div
    v-else-if="!ticket"
    class="p-6 text-center text-danger font-body"
  >
    Ticket no encontrado
  </div>
  
  <div
    v-else
    class="max-w-5xl mx-auto space-y-6"
  >
    <div class="flex items-center space-x-4 mb-4">
      <HdButton
        variant="ghost"
        size="sm"
        @click="router.push('/tickets')"
      >
        &larr; Volver a Tickets
      </HdButton>
    </div>

    <!-- Encabezado del Ticket -->
    <HdCard :no-padding="false">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-2xl font-brand font-bold text-content mb-2">
            #{{ ticket.id }} - {{ ticket.title }}
          </h2>
          <div class="flex items-center space-x-3 text-sm text-muted">
            <span>Creado por: {{ ticket.user?.name || 'Desconocido' }}</span>
            <span>&bull;</span>
            <span>Categoría: {{ ticket.category?.name || 'General' }}</span>
            <span>&bull;</span>
            <span>{{ new Date(ticket.created_at).toLocaleString() }}</span>
          </div>
        </div>
        <HdBadge
          :variant="ticket.status?.name === 'Abierto' ? 'success' : 'neutral'"
          size="lg"
        >
          {{ ticket.status?.name || ticket.status_id }}
        </HdBadge>
      </div>
      <div class="mt-6 text-content whitespace-pre-wrap font-body">
        {{ ticket.description }}
      </div>
    </HdCard>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Columna Principal: Comentarios y Timeline -->
      <div class="md:col-span-2 space-y-6">
        <!-- Respuesta -->
        <HdCard :no-padding="false">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-brand font-medium text-content">
              Añadir Respuesta
            </h3>
            <select
              v-if="macros.length > 0"
              class="text-sm border border-subtle bg-surface text-content rounded px-2 py-1"
              @change="insertMacro"
            >
              <option value="">
                Insertar Macro...
              </option>
              <option
                v-for="macro in macros"
                :key="macro.id"
                :value="macro.id"
              >
                {{ macro.title }}
              </option>
            </select>
          </div>
          <HdTextarea 
            v-model="newComment" 
            rows="4" 
            placeholder="Escribe tu respuesta aquí..."
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

        <!-- Comentarios y Actividad -->
        <div class="space-y-4">
          <h3 class="text-lg font-brand font-medium text-content">
            Timeline del Ticket
          </h3>
          
          <div
            v-if="activities.length === 0"
            class="text-muted text-sm italic"
          >
            No hay actividad registrada.
          </div>

          <div class="relative border-l-2 border-subtle ml-4 space-y-6">
            <div
              v-for="activity in activities"
              :key="activity.id"
              class="ml-6 relative"
            >
              <span class="absolute -left-8 top-1 w-4 h-4 rounded-full bg-surface border-2 border-primary" />
              <div class="bg-surface p-4 rounded-lg shadow-sm border border-subtle">
                <p class="text-sm font-medium text-content">
                  {{ activity.causer ? activity.causer.name : 'Sistema' }} 
                  <span class="text-muted font-normal">{{ activity.description }}</span>
                </p>
                <div
                  v-if="activity.properties && Object.keys(activity.properties).length > 0"
                  class="mt-2 text-xs text-muted bg-surface-elevated p-2 rounded"
                >
                  <pre class="whitespace-pre-wrap">{{ JSON.stringify(activity.properties, null, 2) }}</pre>
                </div>
                <p class="text-xs text-muted mt-2">
                  {{ new Date(activity.created_at).toLocaleString() }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Columna Lateral: Detalles y Metadatos -->
      <div class="space-y-6">
        <HdCard :no-padding="false">
          <h3 class="text-lg font-brand font-medium text-content mb-4">
            Detalles
          </h3>
          <div class="space-y-4 text-sm font-body">
            <div>
              <span class="text-muted block mb-1">Asignado a:</span>
              <span class="font-medium text-content">{{ ticket.assignedUser?.name || 'Sin Asignar' }}</span>
            </div>
            <div>
              <span class="text-muted block mb-1">Prioridad:</span>
              <span class="font-medium text-content">{{ ticket.priority_id || 'Normal' }}</span>
            </div>
            <div v-if="ticket.sla">
              <span class="text-muted block mb-1">Política SLA:</span>
              <span class="font-medium text-content">{{ ticket.sla.policy?.name }}</span>
            </div>
          </div>
        </HdCard>
      </div>
    </div>
  </div>
</template>
