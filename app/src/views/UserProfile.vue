<template>
  <div>
    <header class="relative bg-white shadow">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
          Perfil do Usuário
        </h1>
      </div>
    </header>

   <!-- Main -->
    <main class="flex-grow flex mt-20 items-center justify-center px-4">
      <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-8 animate-fadeIn">
        
        <!-- Nome -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
          <input
            v-model="user.name"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition"
            placeholder="Digite seu nome"
          />
        </div>

        <!-- Email -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            v-model="user.email"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm"
            disabled
          />
        </div>

        <!-- Role -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
          <input
            v-model="user.role"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm"
            disabled
          />
        </div>

        <!-- Botões -->
        <div class="flex flex-col sm:flex-row gap-4">
          <button
            @click="updateProfile"
            class="w-full sm:w-auto bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition"
          >
            Salvar Alterações
          </button>
          <button
            @click="showPasswordModal = true"
            class="w-full sm:w-auto bg-gray-200 px-6 py-2 rounded-lg shadow hover:bg-gray-300 transition"
          >
            Alterar Senha
          </button>
        </div>
      </div>
    </main>

    <!-- Modal de senha -->
    <div
      v-if="showPasswordModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fadeIn"
    >
      <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-xl">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Alterar Senha</h2>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
          <input
            v-model="current_password"
            type="password"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition"
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
          <input
            v-model="new_password"
            type="password"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition"
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
          <input
            v-model="new_password_confirmation"
            type="password"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition"
          />
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button
            class="px-4 py-2 bg-gray-300 rounded-lg shadow hover:bg-gray-400 transition"
            @click="closePasswordModal"
          >
            Cancelar
          </button>
          <button
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition"
            @click="updatePassword"
          >
            Salvar
          </button>
        </div>
      </div>
    </div>

    <!-- Notification -->
    <Notification
      v-model="showNotification"
      :message="notificationMessage"
      :type="notificationType"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axiosClient from "@/axios";
import Notification from "@/components/Notification.vue";

// estado do usuário
const user = ref({ name: "", email: "", role: "" });

// modal de senha
const showPasswordModal = ref(false);
const current_password = ref("");
const new_password = ref("");
const new_password_confirmation = ref("");

// notificações
const showNotification = ref(false);
const notificationMessage = ref("");
const notificationType = ref("success");

// carrega dados do usuário
onMounted(() => {
  axiosClient
    .get("/api/user", {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    })
    .then((res) => {
      user.value = res.data.data;
    })
    .catch((err) => {
      notifyError(err.response?.data?.message || "Erro ao carregar perfil.");
    });
});

// atualizar perfil (nome)
function updateProfile() {
  axiosClient
    .put(
      "/api/user/profile",
      { name: user.value.name },
      {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      }
    )
    .then((res) => {
      notifySuccess(res.data.message);
    })
    .catch((err) => {
      notifyError(err.response?.data?.message || "Erro ao atualizar perfil.");
    });
}

// atualizar senha
function updatePassword() {
  axiosClient
    .put(
      "/api/user/password",
      {
        current_password: current_password.value,
        new_password: new_password.value,
        new_password_confirmation: new_password_confirmation.value,
      },
      {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      }
    )
    .then((res) => {
      notifySuccess(res.data.message);
      closePasswordModal();
    })
    .catch((err) => {
      notifyError(err.response?.data?.message || "Erro ao atualizar senha.");
    });
}

function closePasswordModal() {
  showPasswordModal.value = false;
  current_password.value = "";
  new_password.value = "";
  new_password_confirmation.value = "";
}

// helpers de notificação
function notifySuccess(msg) {
  notificationMessage.value = msg;
  notificationType.value = "success";
  showNotification.value = true;
}

function notifyError(msg) {
  notificationMessage.value = msg;
  notificationType.value = "error";
  showNotification.value = true;
}
</script>


<style>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
</style>