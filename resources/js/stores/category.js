import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useCategoryStore = defineStore("useCategoryStore", {
    state: () => ({
        categories: [],
        errorGetCategories: false,
    }),
    actions: {
        async getAllCategoryProduct() {
            try {
                this.errorGetCategories = false;

                const response = await apiServices.get("category");

                if (response.data.status_code === 200) {
                    this.categories = response.data.datas;
                }
            } catch (error) {
                this.errorGetCategories = true;
            }
        },
    },
    persist: true,
});
