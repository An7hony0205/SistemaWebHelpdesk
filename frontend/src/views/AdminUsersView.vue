<script setup>
// Vista placeholder para Gestión de Usuarios.
// El endpoint de backend aún no existe.
// Cuando se implemente Route::apiResource('users', UserController::class),
// esta vista consumirá /api/users para listar, crear, editar y eliminar usuarios.
</script>

<template>
  <div class="space-y-6 font-body">
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold font-brand text-content">
        Gestión de Usuarios
      </h2>
    </div>

    <div class="bg-surface p-10 rounded-lg shadow-sm border border-subtle text-center">
      <svg
        class="w-16 h-16 mx-auto text-muted mb-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.5"
          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
        />
      </svg>
      <h3 class="text-lg font-semibold font-brand text-content mb-2">
        Módulo en preparación
      </h3>
      <p class="text-muted max-w-md mx-auto">
        La gestión de usuarios estará disponible próximamente. Desde aquí podrás crear, editar y administrar los usuarios de tu organización, asignar roles y gestionar permisos.
      </p>
    </div>
  </div>
</template>
