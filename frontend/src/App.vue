<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from './stores/auth';
import { usePreferencesStore } from './stores/preferences';

const authStore = useAuthStore();
const preferencesStore = usePreferencesStore();
const loading = ref(true);

onMounted(async () => {
  if (authStore.isAuthenticated && !authStore.user) {
    await authStore.fetchUser();
  } else if (!preferencesStore.initialized) {
    // Si no está autenticado, al menos intentar cargar las preferencias públicas si existieran
    await preferencesStore.fetchAll();
  }
  loading.value = false;
});
</script>

<template>
  <div
    v-if="loading"
    class="min-h-screen flex items-center justify-center bg-background"
  >
    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
  </div>
  <router-view v-else />
</template>
