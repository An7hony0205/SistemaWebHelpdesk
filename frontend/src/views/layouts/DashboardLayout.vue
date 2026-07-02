<script setup>
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';
import HdButton from '../../components/ui/HdButton.vue';

const authStore = useAuthStore();
const router = useRouter();

onMounted(() => {
  authStore.fetchUser();
});

const handleLogout = () => {
  authStore.logout();
  router.push({ name: 'Login' });
};
</script>

<template>
  <div class="min-h-screen bg-background flex font-body">
    <!-- Sidebar -->
    <div class="w-64 bg-surface shadow-xl border-r border-subtle">
      <div class="h-16 flex items-center px-6 border-b border-subtle">
        <span class="text-xl font-bold font-brand text-primary">HelpDesk SaaS</span>
      </div>
      <nav class="p-4 space-y-2 font-brand">
        <router-link :to="{ name: 'Dashboard' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors">Dashboard</router-link>
        <router-link :to="{ name: 'Tickets' }" class="flex items-center px-4 py-2 text-muted hover:bg-black/10 dark:hover:bg-white/10 rounded-md">
            <svg class="w-5 h-5 mr-3 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
            Tickets
        </router-link>
        <router-link to="/kb" class="flex items-center px-4 py-2 text-muted hover:bg-black/10 dark:hover:bg-white/10 rounded-md">
            <svg class="w-5 h-5 mr-3 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            Base de Conocimientos
        </router-link>
        <div class="pt-4 pb-2">
          <span class="px-4 text-xs font-semibold text-muted uppercase tracking-wider">Administración</span>
        </div>
        <router-link v-if="$can('manage settings')" :to="{ name: 'Automations' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors flex items-center">
            <svg class="w-5 h-5 mr-3 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Automatizaciones (Triggers)
        </router-link>
        <router-link v-if="$can('manage settings')" :to="{ name: 'Macros' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors flex items-center">
            <svg class="w-5 h-5 mr-3 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            Macros (Respuestas)
        </router-link>
        <router-link v-if="$can('manage settings')" :to="{ name: 'Integrations' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors flex items-center">
            <svg class="w-5 h-5 mr-3 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
            Desarrolladores (API)
        </router-link>
        <router-link v-if="$can('manage settings')" :to="{ name: 'AiSettings' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors flex items-center">
            <svg class="w-5 h-5 mr-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Inteligencia Artificial
        </router-link>
        <router-link v-if="$can('manage settings')" :to="{ name: 'SlaPolicies' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors">Políticas SLA</router-link>
        <div class="pt-4 pb-2">
          <span class="px-4 text-xs font-semibold text-muted uppercase tracking-wider">Notificaciones</span>
        </div>
        <router-link :to="{ name: 'Preferences' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors">Mis Preferencias</router-link>
        <router-link v-if="$can('manage settings')" :to="{ name: 'NotificationTemplates' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors">Plantillas</router-link>
        <router-link v-if="$can('manage settings')" :to="{ name: 'NotificationLogs' }" class="block px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors">Auditoría (Logs)</router-link>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Topbar -->
      <header class="h-16 bg-surface/80 backdrop-blur-md border-b border-subtle shadow-sm flex items-center justify-between px-6 sticky top-0 z-10">
        <h1 class="text-xl font-semibold font-brand text-content">{{ $route.name }}</h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm font-medium text-muted">{{ authStore.user?.name || 'Cargando...' }}</span>
          <HdButton variant="ghost" size="sm" class="text-danger" @click="handleLogout">
            Cerrar Sesión
          </HdButton>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 overflow-auto">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>
