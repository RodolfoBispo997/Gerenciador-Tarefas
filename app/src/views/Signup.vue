<template>
  <GuestLayout>
    <h2
      class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900"
    >
      Create a new account
    </h2>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form @submit.prevent="submit" class="space-y-4" method="POST">
        <div>
          <label for="name" class="block text-sm/6 font-medium text-gray-900"
            >Full name</label
          >
          <div class="mt-2">
            <input
              type="text"
              name="name"
              id="name"
              v-model="data.name"
              class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
            <small class="text-red-500">{{ err.error_name }}</small>
          </div>
        </div>

        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900"
            >Email address</label
          >
          <div class="mt-2">
            <input
              type="email"
              name="email"
              id="email"
              v-model="data.email"
              class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
            <small class="text-red-500">{{ err.error_email }}</small>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label
              for="password"
              class="block text-sm/6 font-medium text-gray-900"
              >Password</label
            >
          </div>
          <div class="mt-2">
            <input
              type="password"
              name="password"
              id="password"
              v-model="data.password"
              class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
            <small  class="text-red-500">{{ err.error_password }}</small>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label
              for="password_confirmation"
              class="block text-sm/6 font-medium text-gray-900"
              >Repeat Password</label
            >
          </div>
          <div class="mt-2">
            <input
              type="password"
              name="password_confirmation"
              id="password_confirmation"
              v-model="data.password_confirmation"
              class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
            />
            <small  class="text-red-500">{{ err.error_password }}</small>
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Create an Account
          </button>
        </div>
      </form>

      <p class="mt-10 text-center text-sm/6 text-gray-500">
        Already have an account?
        {{ " " }}
        <RouterLink
          :to="{ name: 'Login' }"
          class="font-semibold text-indigo-600 hover:text-indigo-500"
        >
          Login from here
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
import router from "@/router";
import { ref } from "vue";
import Notification from "@/components/Notification.vue";

const err = ref({
  error_name: "",
  error_email: "",
  error_password: "",
});

const data = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

// estados da notificação
const showNotification = ref(false);
const notificationMessage = ref("");
const notificationType = ref("success");

// function submit() {
//   //Função para registrar
//   axiosClient
//     .post("/api/register", data.value)
//     .then((response) => {
//       console.log("Usuário registrado com sucesso", response);
//       notificationMessage.value = "Usuário registrado com sucesso!";
//       router.push({ name: "Login" });
//     })
//     .catch((error) => {
//       console.log("VER AQUII==>", error.response.data.errors);
//       err.value = {
//         error_name: error.response.data.errors.name[0],
//         error_email: error.response.data.errors.email[0],
//         error_password: error.response.data.errors.password[0],
//       };
//     });
// }


// Confirmação de email
const submit = async () => {
  // err.value = {
  //       error_name: "",
  //       error_email: "",
  //       error_password: "",
  //   }
  try {
    const response = await axiosClient.post("/api/register", data.value);
    notificationMessage.value = "Usuário criado com sucesso!!";
    //notificationMessage.value = "Usuário criado com sucesso!! Verifique seu e-mail para ativar a conta.";
    notificationType.value = "success";
    showNotification.value = true;
    if(response.status === 200){
      // redireciona com um pequeno delay para o usuário ver a mensagem
    setTimeout(() => {
      router.push({ name: "Login" });
    }, 2500);
      // router.push({ name: "Login" });
    }
  } catch (error) {

    notificationMessage.value = error.response.data.message;
      notificationType.value = "error";
      showNotification.value = true;

    console.log("VER AQUII==>", error.response.data.errors);
    // Validação de campos
      err.value = {
        error_name: error.response.data.errors.name ? error.response.data.errors.name[0] : "",
        error_email: error.response.data.errors.email ? error.response.data.errors.email[0] : "",
        error_password: error.response.data.errors.password ? error.response.data.errors.password[0] : "" ,
    }
  } finally{
    console.log("CHEGOU NO FINAL!")
  }
}
</script>

<style>
</style>