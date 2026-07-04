import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/portal',
    name: 'CustomerPortal',
    component: () => import('../views/CustomerPortalView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/portal/tickets/:id',
    name: 'CustomerTicketDetail',
    component: () => import('../views/CustomerTicketDetailView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/',
    component: () => import('../views/layouts/DashboardLayout.vue'),
    meta: { requiresAuth: true, requiresAgent: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: () => import('../views/DashboardView.vue'),
      },
      {
        path: 'tickets',
        name: 'Tickets',
        component: () => import('../views/TicketsView.vue'),
      },
      {
        path: 'tickets/:id',
        name: 'TicketDetail',
        component: () => import('../views/TicketDetailView.vue'),
      },
      // Knowledge Base
      {
        path: 'kb',
        name: 'kb-explorer',
        component: () => import('../views/KnowledgeBase/KbExplorerView.vue')
      },
      {
        path: 'kb/articles/:slug',
        name: 'kb-article',
        component: () => import('../views/KnowledgeBase/KbArticleView.vue')
      },
      // Admin
      {
        path: 'admin/automations',
        name: 'Automations',
        component: () => import('../views/AutomationsListView.vue')
      },
      {
        path: 'admin/macros',
        name: 'Macros',
        component: () => import('../views/MacrosListView.vue')
      },
      {
        path: 'admin/integrations',
        name: 'Integrations',
        component: () => import('../views/IntegrationsView.vue')
      },
      {
        path: 'admin/ai-settings',
        name: 'AiSettings',
        component: () => import('../views/AiSettingsView.vue')
      },
      {
        path: 'admin/categories',
        name: 'SlaPolicies',
        component: () => import('../views/SlaPoliciesView.vue'),
      },
      {
        path: 'admin/users',
        name: 'AdminUsers',
        component: () => import('../views/AdminUsersView.vue'),
        meta: { title: 'Gestión de Usuarios' },
      },
      {
        path: 'admin/manage-categories',
        name: 'AdminCategories',
        component: () => import('../views/AdminCategoriesView.vue'),
        meta: { title: 'Gestión de Categorías' },
      },
      {
        path: 'settings/preferences',
        name: 'Preferences',
        component: () => import('../views/PreferencesView.vue'),
      },
      {
        path: 'notification-templates',
        name: 'NotificationTemplates',
        component: () => import('../views/NotificationTemplatesView.vue'),
      },
      {
        path: 'notification-logs',
        name: 'NotificationLogs',
        component: () => import('../views/NotificationLogsView.vue'),
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'Login' };
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    // Si ya está logueado y va a login, redirigir a portal o dashboard según rol
    if (authStore.user?.roles?.some(r => r.name === 'Soporte' || r.name === 'Admin')) {
      return { name: 'Dashboard' };
    } else {
      return { name: 'CustomerPortal' };
    }
  } else if (to.meta.requiresAgent && authStore.isAuthenticated) {
    if (authStore.user?.roles?.some(r => r.name === 'Soporte' || r.name === 'Admin')) {
      return true;
    } else {
      return { name: 'CustomerPortal' };
    }
  } else {
    return true;
  }
});

export default router;
