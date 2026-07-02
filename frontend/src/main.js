import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './style.css'
import { useAuthStore } from './stores/auth'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Inyectar método $can globalmente
app.config.globalProperties.$can = function (permission) {
  const authStore = useAuthStore();
  if (!authStore.user || !authStore.user.all_permissions) return false;
  return authStore.user.all_permissions.includes(permission);
}

app.mount('#app')
