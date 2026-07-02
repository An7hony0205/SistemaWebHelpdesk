<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';

const router = useRouter();
const articles = ref([]);
const categories = ref([]);
const searchQuery = ref('');
const loading = ref(true);
const selectedCategory = ref(null);

const fetchCategories = async () => {
  try {
    const response = await api.get('/kb-categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const fetchArticles = async () => {
  loading.value = true;
  try {
    const params = {};
    if (searchQuery.value) params.q = searchQuery.value;
    if (selectedCategory.value) params.category_id = selectedCategory.value;
    
    const response = await api.get('/kb-articles', { params });
    articles.value = response.data.data; // Paginado
  } catch (error) {
    console.error('Error fetching articles:', error);
  } finally {
    loading.value = false;
  }
};

const search = () => {
  fetchArticles();
};

const filterByCategory = (categoryId) => {
  selectedCategory.value = categoryId;
  fetchArticles();
};

const goToArticle = (slug) => {
  router.push(`/kb/articles/${slug}`);
};

onMounted(async () => {
  await fetchCategories();
  await fetchArticles();
});
</script>

<template>
  <div class="max-w-7xl mx-auto py-8">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-extrabold text-content tracking-tight mb-4">Base de Conocimientos</h1>
      <p class="text-xl text-muted mb-8">¿En qué podemos ayudarte hoy?</p>
      
      <!-- Search Bar -->
      <div class="max-w-2xl mx-auto flex gap-2">
        <input 
          v-model="searchQuery" 
          @keyup.enter="search"
          type="text" 
          class="flex-1 rounded-md border-subtle shadow-sm focus:border-primary focus:ring-primary text-lg px-4 py-3" 
          placeholder="Busca artículos, guías, errores frecuentes..."
        >
        <button @click="search" class="bg-primary text-white px-6 py-3 rounded-md hover:bg-primary-dark transition font-medium">
          Buscar
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Sidebar / Categories -->
      <div class="col-span-1">
        <h3 class="text-lg font-medium text-content mb-4">Categorías</h3>
        <ul class="space-y-2">
          <li>
            <button 
              @click="filterByCategory(null)" 
              :class="['w-full text-left px-3 py-2 rounded-md transition', selectedCategory === null ? 'bg-primary text-white' : 'text-muted hover:bg-surface/50']"
            >
              Todos los artículos
            </button>
          </li>
          <li v-for="category in categories" :key="category.id">
            <button 
              @click="filterByCategory(category.id)" 
              :class="['w-full text-left px-3 py-2 rounded-md transition', selectedCategory === category.id ? 'bg-primary text-white' : 'text-muted hover:bg-surface/50']"
            >
              {{ category.name }}
            </button>
          </li>
        </ul>
      </div>

      <!-- Articles List -->
      <div class="col-span-1 md:col-span-3">
        <div v-if="loading" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>
        
        <div v-else-if="articles.length === 0" class="text-center py-12 bg-surface rounded-lg shadow-sm">
          <svg class="mx-auto h-12 w-12 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-content">No se encontraron artículos</h3>
          <p class="mt-1 text-sm text-muted">Intenta con otros términos de búsqueda.</p>
        </div>

        <div v-else class="space-y-4">
          <div 
            v-for="article in articles" 
            :key="article.id" 
            @click="goToArticle(article.slug)"
            class="bg-surface rounded-lg shadow-sm border border-subtle p-6 hover:shadow-md transition cursor-pointer"
          >
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-xl font-semibold text-primary mb-2">{{ article.title }}</h2>
                <p class="text-muted line-clamp-2">{{ article.excerpt || article.content.substring(0, 150) + '...' }}</p>
              </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-muted space-x-4">
              <span v-if="article.category" class="bg-surface/50 px-2 py-1 rounded">{{ article.category.name }}</span>
              <span>👁️ {{ article.views_count }} vistas</span>
              <span v-if="article.author">✏️ {{ article.author.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
