<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
      </div>
    </header>

    <!-- Main -->
    <main>
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- 1. Status -->
          <div class="bg-white shadow rounded-lg p-6 flex flex-col">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
              Status das Tarefas
            </h2>
            <canvas ref="statusChart" class="flex-1 h-64"></canvas>
          </div>

          <!-- 2. Tarefas por Usuário -->
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
              Tarefas por Usuário
            </h2>
            <div class="h-64">
              <canvas ref="userChart"></canvas>
            </div>
          </div>

          <!-- 4. Percentual -->
          <div class="bg-white shadow rounded-lg p-6 flex flex-col">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
              Percentual Concluído
            </h2>
            <canvas ref="percentageChart" class="flex-1 h-64"></canvas>
          </div>

          <!-- 3. Tarefas por Usuário e Status -->
          <div
            class="bg-white shadow rounded-lg p-6 flex flex-col md:col-span-2 lg:col-span-3"
          >
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
              Usuários x Status
            </h2>
            <canvas ref="userStatusChart" class="flex-1 h-80"></canvas>
          </div>

          <!-- 4. Percentual
          <div class="bg-white shadow rounded-lg p-6 flex flex-col">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Percentual Concluído</h2>
            <canvas ref="percentageChart" class="flex-1 h-64"></canvas>
          </div> -->

          <!-- 5. Top 5 -->
          <div
            class="bg-white shadow rounded-lg p-6 flex flex-col md:col-span-3"
          >
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
              Top 5 Usuários
            </h2>
            <canvas ref="topChart" class="flex-1 h-80"></canvas>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import {
  Chart,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement,
} from "chart.js";
import axiosClient from "@/axios";

// registra elementos necessários
Chart.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
);

// refs dos canvases
const statusChart = ref(null);
const userChart = ref(null);
const userStatusChart = ref(null);
const percentageChart = ref(null);
const topChart = ref(null);

onMounted(async () => {
  try {
    // 1. Status
    const { data: statusRes } = await axiosClient.get("/api/taskStatus");
    new Chart(statusChart.value, {
      type: "pie",
      data: {
        labels: ["Concluídas", "Pendentes"],
        datasets: [
          {
            data: [statusRes.data.completed, statusRes.data.pending],
            backgroundColor: ["#22c55e", "#ef4444"],
          },
        ],
      },
    });

    // 2. Por usuário
    const { data: usersRes } = await axiosClient.get("/api/taskByUser");
    new Chart(userChart.value, {
      type: "bar",
      data: {
        labels: usersRes.data.map((u) => u.name),
        datasets: [
          {
            label: "Tarefas",
            data: usersRes.data.map((u) => u.tasks_count),
            backgroundColor: "#3b82f6",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } },
      },
    });

    // 3. Usuário x Status
    const { data: userStatusRes } = await axiosClient.get(
      "/api/taskByUserAndStatus"
    );
    new Chart(userStatusChart.value, {
      type: "bar",
      data: {
        labels: userStatusRes.data.map((u) => u.name),
        datasets: [
          {
            label: "Pendentes",
            data: userStatusRes.data.map((u) => u.pending_tasks_count),
            backgroundColor: "#fbbf24",
          },
          {
            label: "Em Progresso",
            data: userStatusRes.data.map((u) => u.in_progress_tasks_count),
            backgroundColor: "#60a5fa",
          },
          {
            label: "Concluídas",
            data: userStatusRes.data.map((u) => u.completed_tasks_count),
            backgroundColor: "#22c55e",
          },
        ],
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } },
    });

    // 4. Percentual
    const { data: percRes } = await axiosClient.get(
      "/api/taskPercentageCompleted"
    );
    new Chart(percentageChart.value, {
      type: "doughnut",
      data: {
        labels: ["Concluídas", "Restante"],
        datasets: [
          {
            data: [percRes.data, 100 - percRes.data],
            backgroundColor: ["#16a34a", "#e5e7eb"],
          },
        ],
      },
    });

    // 5. Top 5
    const { data: topRes } = await axiosClient.get("/api/top5");
    new Chart(topChart.value, {
      type: "bar",
      data: {
        labels: topRes.data.map((u) => u.name),
        datasets: [
          {
            label: "Concluídas",
            data: topRes.data.map((u) => u.completed_tasks_count),
            backgroundColor: "#9333ea",
          },
        ],
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } },
    });
  } catch (err) {
    console.error("Erro ao carregar gráficos:", err);
  }
});
</script>
