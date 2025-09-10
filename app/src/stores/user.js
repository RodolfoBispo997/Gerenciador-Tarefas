// stores/user.js
import { defineStore } from "pinia";
import axiosClient from "../axios";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
  }),

  getters: {
    isAdmin: (state) => state.user?.role === "admin",
  },

  actions: {
    async fetchUser() {
      try {
        const res = await axiosClient.get("/api/user", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.user = res.data.data;

        console.log("VEFICAIR O QUE TEM NO PINIA ===>", this.user)
      } catch (err) {
        console.error("Erro ao carregar usuário", err);
      }
    }
  }
});
