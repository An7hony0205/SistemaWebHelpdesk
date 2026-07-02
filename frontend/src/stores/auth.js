import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(email, password) {
      const response = await api.post('/login', { email, password });
      this.token = response.data.token;
      this.user = response.data.user;
      localStorage.setItem('auth_token', this.token);
    },
    async fetchUser() {
      if (!this.token) return;
      try {
        const response = await api.get('/me');
        this.user = response.data;
        // Load preferences
        const { usePreferencesStore } = await import('./preferences');
        const prefsStore = usePreferencesStore();
        if (!prefsStore.initialized) {
          prefsStore.fetchAll();
        }
      } catch (error) {
        this.logout();
      }
    },
    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('auth_token');
    }
  }
});
