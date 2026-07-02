<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../services/api';

const route = useRoute();
const router = useRouter();
const article = ref(null);
const loading = ref(true);

const fetchArticle = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/kb-articles/${route.params.slug}`);
    article.value = response.data;
  } catch (error) {
    console.error('Error fetching article:', error);
    if (error.response && error.response.status === 404) {
      router.push('/kb');
    }
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  router.push('/kb');
};

onMounted(() => {
  fetchArticle();
});
</script>

<template>
  <div class="max-w-4xl mx-auto py-8">
    <button @click="goBack" class="mb-6 text-primary hover:text-primary-dark font-medium flex items-center">
      <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
      Volver a la Base de Conocimientos
    </button>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>

    <div v-else-if="article" class="bg-surface rounded-xl shadow-sm border border-subtle overflow-hidden">
      <!-- Header -->
      <div class="p-8 border-b border-subtle bg-background">
        <div class="flex items-center space-x-2 text-sm text-muted mb-4">
          <span v-if="article.category" class="uppercase font-semibold tracking-wider text-primary">{{ article.category.name }}</span>
          <span v-if="article.category">•</span>
          <span>Actualizado el {{ new Date(article.updated_at).toLocaleDateString() }}</span>
        </div>
        <h1 class="text-3xl font-bold text-content mb-4">{{ article.title }}</h1>
        <p v-if="article.excerpt" class="text-lg text-muted">{{ article.excerpt }}</p>
      </div>

      <!-- Content (Markdown/HTML) -->
      <div class="p-8 prose max-w-none">
        <!-- Para el MVP renderizamos directo, asumiendo sanitización en backend o uso de editor seguro -->
        <div v-html="article.content"></div>
      </div>
      
      <!-- Footer Info -->
      <div class="px-8 py-6 bg-background border-t border-subtle flex justify-between items-center">
        <div class="flex items-center space-x-3 text-sm text-muted">
          <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold">
            {{ article.author?.name?.charAt(0) }}
          </div>
          <div>
            <p class="font-medium text-content">Escrito por {{ article.author?.name }}</p>
            <p>{{ article.views_count }} vistas</p>
          </div>
        </div>
        
        <!-- Feedback placeholder para fase futura -->
        <div class="flex items-center space-x-2">
          <span class="text-sm text-muted mr-2">¿Fue útil?</span>
          <button class="p-2 rounded hover:bg-gray-200 text-muted">👍</button>
          <button class="p-2 rounded hover:bg-gray-200 text-muted">👎</button>
        </div>
      </div>
    </div>
  </div>
</template>
