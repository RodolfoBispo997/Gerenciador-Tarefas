<template>
  <div>
    <header class="relative bg-white shadow">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
          Lista de tarefas
        </h1>
      </div>
    </header>
    <main>
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <!-- Table -->
        <div class="overflow-x-auto">
          <span class="flex justify-end">
            <button
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-end"
              @click="showModal = true"
            >
              Adicionar tarefa
            </button>
          </span>
          <table
            class="min-w-full bg-white rounded-lg shadow border border-gray-200"
          >
            <thead class="bg-gray-50 rounded-t-lg">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="task in tasks"
                :key="task.id"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 text-sm text-gray-900">
                  {{ task.user ? task.user.name : "" }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ task.title }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ task.description }}
                </td>
                <td class="px-6 py-4 text-sm">
                  <span
                    :class="{
                      'bg-green-100 text-green-800': task.status === 'completed',
                      'bg-yellow-100 text-yellow-800': task.status === 'pending',
                      'bg-blue-100 text-blue-800': task.status === 'in_progress',
                    }"
                    class="px-2 py-1 rounded-full text-xs font-semibold"
                  >
                    {{ task.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm space-x-2">
                  <span
                    class="text-indigo-600 hover:underline cursor-pointer"
                    @click="editTask(task)"
                    >Editar</span
                  >
                  <span
                    class="text-red-600 hover:underline cursor-pointer"
                    @click="deleteTask(task)"
                    >Excluir</span
                  >
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Loading & Error -->
          <div v-if="loading" class="flex justify-center items-center mt-4">
            <div class="flex items-center space-x-2">
              <div class="w-6 h-6 border-2 border-t-blue-500 border-gray-300 rounded-full animate-spin"></div>
              <span class="text-gray-500 text-sm">Carregando...</span>
            </div>
          </div>
          <div v-if="error" class="mt-4 text-red-500">
            Erro ao carregar dados.
          </div>
        </div>

        <!-- Modal de Criação -->
        <div
          v-if="showModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
          <div class="bg-white rounded-lg w-1/3 p-6">
            <h2 class="text-xl font-bold mb-4">Adicionar Nova Tarefa</h2>
            <label class="block text-sm font-medium mb-1">Título</label>
            <input
              type="text"
              placeholder="Title"
              class="w-full mb-2 p-2 border rounded"
              v-model="newTask.title"
            />
            <label class="block text-sm font-medium mb-1">Descrição</label>
            <textarea
              placeholder="Description"
              class="w-full mb-2 p-2 border rounded"
              v-model="newTask.description"
            ></textarea>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select
              class="w-full mb-2 p-2 border rounded"
              v-model="newTask.status"
            >
              <option value="pending">Pendente</option>
              <option value="in_progress">Em andamento</option>
              <option value="completed">Concluído</option>
            </select>
            <label class="block text-sm/6 font-medium text-gray-900">Responsável</label>
            <select
              class="w-full mb-2 p-2 border rounded"
              v-model="newTask.user_id"
            >
              <option :value="null">Sem usuário</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
            <div class="flex justify-end space-x-2">
              <button
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                @click="showModal = false"
              >
                Cancelar
              </button>
              <button
                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                @click="createTask()"
              >
                Salvar
              </button>
            </div>
          </div>
        </div>

        <!-- Modal de edição -->
        <div
          v-if="showEditModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
          <div class="bg-white rounded-lg w-1/3 p-6">
            <h2 class="text-xl font-bold mb-4">Editar tarefa</h2>
            <input
              type="text"
              placeholder="Title"
              class="w-full mb-2 p-2 border rounded"
              v-model="editTaskData.title"
            />
            <textarea
              placeholder="Description"
              class="w-full mb-2 p-2 border rounded"
              v-model="editTaskData.description"
            ></textarea>
            <select
              class="w-full mb-2 p-2 border rounded"
              v-model="editTaskData.status"
            >
              <option value="pending">Pendente</option>
              <option value="in_progress">Em andamento</option>
              <option value="completed">Concluído</option>
            </select>
            <label class="block text-sm/6 font-medium text-gray-900">Responsável</label>
            <select
              class="w-full mb-2 p-2 border rounded"
              v-model="editTaskData.user_id"
            >
              <option :value="null">Sem usuário</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
            <div class="flex justify-end space-x-2">
              <button
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                @click="showEditModal = false"
              >
                Cancelar
              </button>
              <button
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                @click="updateTask()"
              >
                Atualizar
              </button>
            </div>
          </div>
        </div>

        <!-- Modal de exclusão -->
        <div
          v-if="showDeleteModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
          <div class="bg-white rounded-lg w-1/3 p-6">
            <h2 class="text-xl font-bold mb-4 text-red-600">Confirmar exclusão</h2>
            <p class="mb-6">
              Tem certeza que deseja excluir a task
              <strong>{{ deleteTaskData.title }}</strong>?
            </p>
            <div class="flex justify-end space-x-2">
              <button
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                @click="showDeleteModal = false"
              >
                Cancelar
              </button>
              <button
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                @click="confirmDeleteTask()"
              >
                Excluir
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import axiosClient from "@/axios";
import { ref, onMounted } from "vue";

const tasks = ref([]);
const users = ref([]);
const loading = ref(true);
const error = ref(null);

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

const newTask = ref({
  title: "",
  description: "",
  status: "pending",
  user_id: null,
});

const editTaskData = ref({
  id: null,
  title: "",
  description: "",
  status: "pending",
  user_id: null,
});

const deleteTaskData = ref({ id: null, title: "" });

// Pega os dados para popular a tabela
const fetchTasks = async () => {
  loading.value = true;
  try {
    const response = await axiosClient.get("/api/task");
    tasks.value = response.data.tasks;
    users.value = response.data.users;
  } catch (err) {
    error.value = err;
    console.log("Erro ao carregar tasks:", err);
  } finally {
    loading.value = false;
  }
};

// Monta a tabela quando entrar na tela
onMounted(fetchTasks);

// Cria uma nova task
const createTask = async () => {
  loading.value = true;
  try {
    await axiosClient.post("/api/task", newTask.value);
    showModal.value = false;
    newTask.value = {
      title: "",
      description: "",
      status: "pending",
      user_id: null,
    };
    await fetchTasks();
  } catch (err) {
    console.log("Erro ao criar task:", err);
  } finally {
    loading.value = false;
  }
};

// Abre o modal de edição com os dados corretos
const editTask = (task) => {
  editTaskData.value = {
    id: task.id,
    title: task.title,
    description: task.description,
    status: task.status,
    user_id: task.user_id,
  };
  showEditModal.value = true;
};

// Atualiza a task
const updateTask = async () => {
  loading.value = true;
  try {
    await axiosClient.put(`/api/task/${editTaskData.value.id}`, editTaskData.value);
    showEditModal.value = false;
    await fetchTasks();
  } catch (err) {
    console.log("Erro ao atualizar task:", err);
  } finally {
    loading.value = false;
  }
};

// Abre modal de exclusão
const deleteTask = (task) => {
  deleteTaskData.value = { id: task.id, title: task.title };
  showDeleteModal.value = true;
};

// Confirma exclusão
const confirmDeleteTask = async () => {
  loading.value = true;
  try {
    await axiosClient.delete(`/api/task/${deleteTaskData.value.id}`);
    showDeleteModal.value = false;
    await fetchTasks();
  } catch (err) {
    console.log("Erro ao deletar task:", err);
  } finally {
    loading.value = false;
  }
};

const detailTask = (task) => {
  alert(`Detalhe task: ${task.title}`);
};
</script>
