import { defineStore } from 'pinia';
import api from '../services/api';

export const usePreferencesStore = defineStore('preferences', {
  state: () => ({
    allPreferences: {},
    loading: false,
    initialized: false,
  }),

  getters: {
    getCategory: (state) => (category) => {
      return state.allPreferences[category] || {};
    },
    appearance: (state) => state.allPreferences['appearance'] || {},
    notifications: (state) => state.allPreferences['notifications'] || {},
    profile: (state) => state.allPreferences['profile'] || {},
    tickets: (state) => state.allPreferences['tickets'] || {},
  },

  actions: {
    async fetchAll() {
      this.loading = true;
      try {
        const response = await api.get('/preferences');
        this.allPreferences = response.data;
        this.initialized = true;
        this.applyGlobalSettings();
      } catch (error) {
        console.error('Error fetching preferences', error);
      } finally {
        this.loading = false;
      }
    },

    async updateCategory(category, settings) {
      // Optimistic update
      const original = { ...this.allPreferences[category] };
      this.allPreferences[category] = { ...original, ...settings };
      this.applyGlobalSettings();

      try {
        const response = await api.patch(`/preferences/${category}`, { settings });
        this.allPreferences[category] = response.data;
      } catch (error) {
        console.error(`Error updating preference category ${category}`, error);
        // Rollback
        this.allPreferences[category] = original;
        this.applyGlobalSettings();
        throw error;
      }
    },

    async resetCategory(category) {
      try {
        const response = await api.delete(`/preferences/${category}`);
        this.allPreferences[category] = response.data;
        this.applyGlobalSettings();
        return response.data;
      } catch (error) {
        console.error(`Error resetting preference category ${category}`, error);
        throw error;
      }
    },

    applyGlobalSettings() {
      const appSettings = this.appearance;
      if (appSettings) {
        // Theme
        const isDark = appSettings.theme === 'dark' || 
          (appSettings.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        if (isDark) {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }

        // Compact mode
        if (appSettings.compact_mode) {
          document.documentElement.classList.add('compact-mode');
        } else {
          document.documentElement.classList.remove('compact-mode');
        }

        // Reduced animations
        if (appSettings.reduced_animations) {
          document.documentElement.classList.add('reduce-animations');
        } else {
          document.documentElement.classList.remove('reduce-animations');
        }

        // Font size
        if (appSettings.font_size) {
          document.documentElement.setAttribute('data-font-size', appSettings.font_size);
        }


      }
    }
  }
});
