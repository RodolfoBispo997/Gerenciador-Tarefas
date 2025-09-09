<template>
  <GuestLayout>
    <h2
      class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900"
    >
      Faça login na sua conta
    </h2>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form @submit.prevent="login" class="space-y-6" method="POST">
        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900"
            >Endereço de e-mail</label
          >
          <div class="mt-2">
            <input
              type="email"
              name="email"
              id="email"
              v-model="data.email"
              class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label
              for="password"
              class="block text-sm/6 font-medium text-gray-900"
              >Senha</label
            >
            <div class="text-sm">
              <RouterLink
                :to="{ name: 'ForgotPassword' }"
                class="font-semibold text-indigo-600 hover:text-indigo-500"
                >Esqueceu a senha?</RouterLink
              >
            </div>
          </div>
          <div class="mt-2">
            <input
              type="password"
              name="password"
              id="password"
              v-model="data.password"
              class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Entrar</span>
            <span v-else>Entrando...</span>
          </button>
        </div>
      </form>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center mt-4">
        <div class="flex items-center space-x-2">
          <div
            class="w-6 h-6 border-2 border-t-indigo-500 border-gray-300 rounded-full animate-spin"
          ></div>
          <span class="text-gray-500 text-sm">Carregando...</span>
        </div>
      </div>

      <p class="mt-10 text-center text-sm/6 text-gray-500">
        Não é membro?
        <!-- {{ " " }} -->
        <RouterLink
          :to="{ name: 'Signup' }"
          class="font-semibold text-indigo-600 hover:text-indigo-500"
        >
          Crie uma conta
        </RouterLink>
      </p>
    </div>

    <!-- Mensagem de erro + botão de reenviar -->
    <div v-if="showResend" class="mt-4 text-center">
      <p class="text-sm text-gray-600">
        Não recebeu o e-mail de verificação?
      </p>
      <button
        @click="resendVerification"
        class="mt-2 text-sm font-semibold text-indigo-600 hover:text-indigo-500"
        :disabled="resendLoading"
      >
        <span v-if="!resendLoading">Reenviar e-mail de verificação</span>
        <span v-else>Reenviando...</span>
      </button>
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
import { ref } from "vue";

const data = ref({
  email: "",
  password: "",
});

const loading = ref(false);

// estado para reenviar
const showResend = ref(false);
const resendLoading = ref(false);

// estados da notificação
const showNotification = ref(false);
const notificationMessage = ref("");
const notificationType = ref("success");

function login() {
  loading.value = true;

  axiosClient
    .post("/api/login", data.value)
    .then((response) => {
      if (response.data.token) {
        localStorage.setItem("token", response.data.token);
        notificationMessage.value = "Login realizado com sucesso!";
        notificationType.value = "success";
        showNotification.value = true;
        router.push({ name: "Home" });
      }
    })
    .catch((error) => {
      console.log("VERIFICAR AQUI ==>>", error)
      let msg = "Erro inesperado, tente novamente.";
      showResend.value = false; // reset

      if (error.response?.status === 401) {
        msg = "Credenciais inválidas. Verifique seu e-mail e senha.";
      } else if (error.response?.status === 403) {
        msg = "Você precisa verificar seu e-mail antes de acessar.";
        showResend.value = true; // 👈 mostra o botão de reenviar
      } else if (error.response?.data?.message) {
        msg = error.response.data.message;
      }

      notificationMessage.value = msg;
      notificationType.value = "error";
      showNotification.value = true;
    })
    .finally(() => {
      loading.value = false;
    });
}

function resendVerification() {
  resendLoading.value = true;

  axiosClient
    .post("/api/email/resend-guest", { email: data.value.email })
    .then((res) => {
      notificationMessage.value = res.data.message || "E-mail reenviado!";
      notificationType.value = "success";
      showNotification.value = true;
    })
    .catch((error) => {
      let msg = "Erro ao reenviar e-mail.";
      if (error.response?.data?.message) {
        msg = error.response.data.message;
      }
      notificationMessage.value = msg;
      notificationType.value = "error";
      showNotification.value = true;
    })
    .finally(() => {
      resendLoading.value = false;
    });
}
</script>
