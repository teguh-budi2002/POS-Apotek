import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useUserStore = defineStore("useUserStore", {
  state: () => ({
    listUsers: [],
    errorGetData: false,
  }),
  actions: {
    async getListNameOfUser() {
      this.errorGetData = false;
      try {
        const response = await apiServices.get('user/get-name-of-users')
        
        if (response.data.status_code === 200) {
          this.listUsers = response.data.datas;
        }
      } catch (error) {
        this.errorGetData = true
        if (error?.response?.data?.status_code === 404) {
          this.listUsers = [];
        }
      }
    },
  }
});