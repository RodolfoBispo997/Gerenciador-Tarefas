<template>
  <GuestLayout>
    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
      Reset Password
    </h2>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form @submit.prevent="submit" class="space-y-4" method="POST">
        <div>
          <label for="password" class="block text-sm/6 font-medium text-gray-900">
            Nova senha
          </label>
          <div class="mt-2">
            <input
              type="password"
              name="password"
              id="password"
              v-model="data.password"
              class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
            <small class="text-red-500">{{ err.error_password }}</small>
          </div>
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900">
            Confirme a senha
          </label>
          <div class="mt-2">
            <input
              type="password"
              name="password_confirmation"
              id="password_confirmation"
              v-model="data.password_confirmation"
              class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
            <small class="text-red-500">{{ err.error_password }}</small>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Redefinir senha</span>
            <span v-else>Processando...</span>
          </button>
        </div>
      </form>

      <!-- Loading extra (igual ForgotPassword.vue) -->
      <div v-if="loading" class="flex justify-center items-center mt-4">
        <div class="flex items-center space-x-2">
          <div class="w-6 h-6 border-2 border-t-indigo-500 border-gray-300 rounded-full animate-spin"></div>
          <span class="text-gray-500 text-sm">Carregando...</span>
        </div>
      </div>

      <p class="mt-10 text-center text-sm/6 text-gray-500">
        Já tem conta?
        <RouterLink
          :to="{ name: 'Login' }"
          class="font-semibold text-indigo-600 hover:text-indigo-500"
        >
          Entre aqui
        </RouterLink>
      </p>
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
import axiosClient from "@/axios";
import GuestLayout from "@/components/GuestLayout.vue";
import Notification from "@/components/Notification.vue";
import router from "@/router";
import { ref, onMounted } from "vue";

// erros simples (se quiser detalhar depois)
const err = ref({
  error_password: "",
});

const data = ref({
  email: "",
  token: "",
  password: "",
  password_confirmation: "",
});

const loading = ref(false);

// estados da notificação
const showNotification = ref(false);
const notificationMessage = ref("");
const notificationType = ref("success");

onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  data.value.token = params.get("token") || "";
  data.value.email = params.get("email") || "";
});

const submit = async () => {
  loading.value = true;
  try {
    await axiosClient.post("/api/resetPassword", data.value);
    notificationMessage.value = "Senha redefinida com sucesso! Agora você já pode entrar.";
    notificationType.value = "success";
    showNotification.value = true;

    // redireciona com um pequeno delay para o usuário ver a mensagem
    setTimeout(() => {
      router.push({ name: "Login" });
    }, 1500);
  } catch (error) {
    notificationMessage.value =
      error.response?.data?.message || "Erro inesperado, tente novamente.";
    notificationType.value = "error";
    showNotification.value = true;
  } finally {
    loading.value = false;
  }
};
</script>
