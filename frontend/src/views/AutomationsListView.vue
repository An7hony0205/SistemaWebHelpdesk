<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import HdButton from '../components/ui/HdButton.vue';
import HdCard from '../components/ui/HdCard.vue';
import HdBadge from '../components/ui/HdBadge.vue';

const router = useRouter();
const rules = ref([]);
const loading = ref(true);

const fetchRules = async () => {
  loading.value = true;
  try {
    const response = await api.get('/automations');
    rules.value = response.data;
  } catch (error) {
    console.error('Error fetching automations:', error);
  } finally {
    loading.value = false;
  }
};

const toggleRule = async (rule) => {
  try {
    await api.put(`/automations/${rule.id}`, { is_active: !rule.is_active });
    rule.is_active = !rule.is_active;
  } catch (error) {
    console.error('Error toggling rule:', error);
  }
};

const createRule = () => {
  // router.push('/admin/automations/new');
  alert('Builder en construcción (MVP)');
};

onMounted(() => {
  fetchRules();
});
</script>

<template>
  <div class="space-y-6 font-body">
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold font-brand text-content">
          Reglas de Automatización (Triggers)
        </h2>
        <p class="text-sm text-muted mt-1">
          Configura flujos de trabajo basados en eventos del sistema.
        </p>
      </div>
      <HdButton
        variant="primary"
        @click="createRule"
      >
        Crear Regla
      </HdButton>
    </div>

    <div
      v-if="loading"
      class="flex justify-center py-12"
    >
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
    </div>

    <div
      v-else-if="rules.length === 0"
      class="bg-surface rounded-lg shadow-base border border-subtle text-center py-12"
    >
      <p class="text-muted">
        No hay reglas de automatización configuradas.
      </p>
    </div>

    <HdCard
      v-else
      :no-padding="true"
    >
      <ul class="divide-y divide-subtle">
        <li
          v-for="rule in rules"
          :key="rule.id"
          class="p-6 hover:bg-black/5 dark:hover:bg-white/5 transition-colors flex items-center justify-between"
        >
          <div class="flex-1">
            <div class="flex items-center space-x-3">
              <h3 class="text-lg font-medium font-brand text-content">
                {{ rule.name }}
              </h3>
              <HdBadge variant="neutral">
                {{ rule.trigger_event }}
              </HdBadge>
            </div>
            <p class="text-sm text-muted mt-1">
              {{ rule.description || 'Sin descripción' }}
            </p>
            
            <div class="mt-3 flex space-x-4 text-sm text-muted">
              <div><span class="font-medium text-content">{{ rule.conditions?.length || 0 }}</span> Condiciones</div>
              <div><span class="font-medium text-content">{{ rule.actions?.length || 0 }}</span> Acciones</div>
            </div>
          </div>
          
          <div class="flex flex-col items-end space-y-3">
            <HdButton 
              :variant="rule.is_active ? 'success' : 'secondary'" 
              size="sm"
              @click="toggleRule(rule)"
            >
              {{ rule.is_active ? 'Activa' : 'Inactiva' }}
            </HdButton>
          </div>
        </li>
      </ul>
    </HdCard>
  </div>
</template>
