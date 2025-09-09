<template>
  <GuestLayout>
    <h2
      class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900"
    >
      Confirmação de E-mail
    </h2>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <div class="bg-white shadow-md rounded-lg p-8 max-w-md w-full text-center">
      <div class="flex justify-center mb-4">
        <!-- Ícone de check -->
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-green-500"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0a9 9 0 0118 0z"
          />
        </svg>
      </div>

      <h1 class="text-2xl font-semibold text-gray-800 mb-2">
        E-mail confirmado!
      </h1>
      <p class="text-gray-600 mb-6">
        Seu endereço de e-mail foi verificado com sucesso.  
        Agora você já pode acessar todos os recursos da plataforma.
      </p>

      <button
        @click="goToLogin"
        class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
      >
        Ir para o Login
      </button>
    </div>
    </div>
    <!-- Notification -->
    <Notification
      v-model="showNotification"
      :message="notificationMessage"
      :type="notificationType"
    />
  </GuestLayout>
</template>

<script setup>
import { useRouter } from "vue-router"

const router = useRouter()

function goToLogin() {
  router.push({ name: 'Login' })
}
</script>
