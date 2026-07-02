<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import HdInput from '../components/ui/HdInput.vue';
import HdButton from '../components/ui/HdButton.vue';
import HdCard from '../components/ui/HdCard.vue';

const email = ref('admin@test.com');
const password = ref('password');
const errorMsg = ref('');
const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  errorMsg.value = '';
  try {
    await authStore.login(email.value, password.value);
    router.push({ name: 'Dashboard' });
  } catch (error) {
    errorMsg.value = 'Credenciales inválidas o error de red';
  }
};
</script>

<template>
  <div class="min-h-screen bg-background flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Efecto de gradiente de fondo (glassmorphism) -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-primary/20 rounded-full blur-3xl" />
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-surface rounded-full blur-3xl" />

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 mb-8">
      <h2 class="mt-6 text-center text-3xl font-brand font-bold text-content">
        HelpDesk SaaS
      </h2>
      <p class="mt-2 text-center text-sm text-muted">
        Inicia sesión en tu cuenta
      </p>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <HdCard>
        <form
          class="space-y-6"
          @submit.prevent="handleLogin"
        >
          <HdInput 
            v-model="email" 
            label="Email" 
            type="email" 
            required 
            placeholder="ejemplo@empresa.com"
          />

          <HdInput 
            v-model="password" 
            label="Contraseña" 
            type="password" 
            required 
            placeholder="••••••••"
          />

          <div
            v-if="errorMsg"
            class="text-danger text-sm font-medium"
          >
            {{ errorMsg }}
          </div>

          <HdButton
            type="submit"
            variant="primary"
            class="w-full"
          >
            Entrar
          </HdButton>
        </form>
      </HdCard>
    </div>
  </div>
</template>
