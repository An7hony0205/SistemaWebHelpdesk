<script setup>
import { ref, onMounted } from 'vue';
import { usePreferencesStore } from '../stores/preferences';

const preferencesStore = usePreferencesStore();
const currentTab = ref('appearance');

const tabs = [
  { id: 'profile', name: 'Perfil', icon: '👤' },
  { id: 'appearance', name: 'Apariencia', icon: '🎨' },
  { id: 'notifications', name: 'Notificaciones', icon: '🔔' },
  { id: 'tickets', name: 'Tickets', icon: '🎫' },
];

onMounted(() => {
  if (!preferencesStore.initialized) {
    preferencesStore.fetchAll();
  }
});
</script>

<template>
  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-4 sm:px-0">
      <h1 class="text-2xl font-semibold text-content mb-6">Centro de Preferencias</h1>
      
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 flex-shrink-0">
          <nav class="space-y-1">
            <a 
              v-for="tab in tabs" 
              :key="tab.id"
              @click="currentTab = tab.id"
              href="#"
              :class="[
                currentTab === tab.id ? 'bg-primary text-white' : 'text-muted hover:bg-slate-100 dark:hover:bg-surface hover:text-slate-900 dark:hover:text-white',
                'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors'
              ]"
            >
              <span class="mr-3">{{ tab.icon }}</span>
              <span class="truncate">{{ tab.name }}</span>
            </a>
          </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 bg-surface shadow rounded-lg border border-subtle p-6 min-h-[500px]">
          <div v-if="preferencesStore.loading" class="flex justify-center items-center h-full text-muted">
            Cargando preferencias...
          </div>
          
          <div v-else>
            <!-- Here we will inject dynamic components based on the active tab -->
            <div v-if="currentTab === 'appearance'">
               <AppearanceSettings />
            </div>
            <div v-else-if="currentTab === 'profile'">
               <ProfileSettings />
            </div>
            <div v-else-if="currentTab === 'notifications'">
               <NotificationSettings />
            </div>
            <div v-else-if="currentTab === 'tickets'">
               <TicketSettings />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AppearanceSettings from '../components/preferences/AppearanceSettings.vue';
import ProfileSettings from '../components/preferences/ProfileSettings.vue';
import NotificationSettings from '../components/preferences/NotificationSettings.vue';
import TicketSettings from '../components/preferences/TicketSettings.vue';

export default {
  components: {
    AppearanceSettings,
    ProfileSettings,
    NotificationSettings,
    TicketSettings
  }
}
</script>
