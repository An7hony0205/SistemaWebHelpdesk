<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import HdCard from '../components/ui/HdCard.vue';
import HdTable from '../components/ui/HdTable.vue';
import HdBadge from '../components/ui/HdBadge.vue';

const categories = ref([]);
const loading = ref(true);

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCategories();
});

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Nombre' },
  { key: 'status', label: 'Estado' },
];
</script>

<template>
  <div class="space-y-6 font-body">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold font-brand text-content">
        Gestión de Categorías
      </h2>
    </div>

    <div
      v-if="loading"
      class="flex justify-center py-8"
    >
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
    </div>

    <HdCard
      v-else-if="categories.length > 0"
      :no-padding="true"
    >
      <HdTable
        :columns="columns"
        :items="categories"
      >
        <template #cell(id)="{ item }">
          <span class="text-sm text-muted">#{{ item.id }}</span>
        </template>
        <template #cell(name)="{ item }">
          <span class="text-sm font-medium font-brand text-content">{{ item.name }}</span>
        </template>
        <template #cell(status)="{ item }">
          <HdBadge :variant="item.is_active ? 'success' : 'neutral'">
            {{ item.is_active ? 'Activa' : 'Inactiva' }}
          </HdBadge>
        </template>
      </HdTable>
    </HdCard>

    <div
      v-else
      class="text-center bg-surface p-10 rounded-lg shadow-sm border border-subtle"
    >
      <p class="text-muted text-lg">
        No hay categorías registradas.
      </p>
    </div>
  </div>
</template>
