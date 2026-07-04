<script setup>
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import { onMounted, computed } from 'vue';
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

// --- Helpers de rol ---
const userRoles = computed(() => {
  return authStore.user?.roles?.map(r => r.name) || [];
});

const isAdmin = computed(() => userRoles.value.includes('Admin'));
const isSoporte = computed(() => userRoles.value.includes('Soporte'));

/**
 * Navegación dinámica basada en roles.
 *
 * Estructura:
 *   - Cada sección tiene un `label` (encabezado) y `items`.
 *   - Si `label` es null, los items se muestran sin encabezado.
 *   - Cada item tiene: `to` (ruta), `label` (texto), `icon` (SVG path), `roles` (qué roles lo ven).
 *   - Si una sección completa queda vacía para el rol actual, se oculta automáticamente.
 *
 * Módulos ocultos para MVP:
 *   Automatizaciones, Macros, Desarrolladores (API), Inteligencia Artificial,
 *   Políticas SLA, Plantillas, Auditoría (Logs), Base de Conocimientos.
 *   → Siguen en el código y las rutas, simplemente no están en esta configuración.
 *   → Para reactivarlos, basta con añadirlos aquí con los roles adecuados.
 */
const navSections = computed(() => {
  const sections = [
    {
      label: null,
      items: [
        {
          to: { name: 'Dashboard' },
          label: 'Dashboard',
          icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
          roles: ['Admin', 'Soporte'],
        },
        {
          to: { name: 'Tickets' },
          label: 'Tickets',
          icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
          roles: ['Admin', 'Soporte'],
        },
      ],
    },
    {
      label: 'Panel Administrador',
      items: [
        {
          to: { name: 'AdminUsers' },
          label: 'Gestión de Usuarios',
          icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
          roles: ['Admin'],
        },
        {
          to: { name: 'AdminCategories' },
          label: 'Gestión de Categorías',
          icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
          roles: ['Admin'],
        },
      ],
    },
    {
      label: null,
      items: [
        {
          to: { name: 'Preferences' },
          label: 'Mis Preferencias',
          icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
          roles: ['Admin', 'Soporte'],
        },
      ],
    },
  ];

  // Filtrar secciones: solo incluir items visibles para el rol actual
  return sections
    .map(section => ({
      ...section,
      items: section.items.filter(item =>
        item.roles.some(role => userRoles.value.includes(role))
      ),
    }))
    .filter(section => section.items.length > 0);
});
</script>

<template>
  <div class="min-h-screen bg-background flex font-body">
    <!-- Sidebar -->
    <div class="w-64 bg-surface shadow-xl border-r border-subtle flex flex-col">
      <div class="h-16 flex items-center px-6 border-b border-subtle">
        <span class="text-xl font-bold font-brand text-primary">HelpDesk SaaS</span>
      </div>
      <nav class="flex-1 p-4 space-y-1 font-brand overflow-y-auto">
        <template
          v-for="(section, sIdx) in navSections"
          :key="sIdx"
        >
          <!-- Encabezado de sección (solo si tiene label) -->
          <div
            v-if="section.label"
            class="pt-5 pb-2"
          >
            <span class="px-4 text-xs font-semibold text-muted uppercase tracking-wider">{{ section.label }}</span>
          </div>

          <!-- Items de la sección -->
          <router-link
            v-for="item in section.items"
            :key="item.label"
            :to="item.to"
            class="flex items-center px-4 py-2 text-muted rounded-md hover:bg-black/10 dark:hover:bg-white/10 hover:text-primary transition-colors"
            active-class="bg-primary/10 text-primary font-semibold"
          >
            <svg
              class="w-5 h-5 mr-3 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                :d="item.icon"
              />
            </svg>
            {{ item.label }}
          </router-link>
        </template>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Topbar -->
      <header class="h-16 bg-surface/80 backdrop-blur-md border-b border-subtle shadow-sm flex items-center justify-between px-6 sticky top-0 z-10">
        <h1 class="text-xl font-semibold font-brand text-content">
          {{ $route.meta.title || $route.name }}
        </h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm font-medium text-muted">{{ authStore.user?.name || 'Cargando...' }}</span>
          <HdButton
            variant="ghost"
            size="sm"
            class="text-danger"
            @click="handleLogout"
          >
            Cerrar Sesión
          </HdButton>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 overflow-auto">
        <router-view />
      </main>
    </div>
  </div>
</template>
