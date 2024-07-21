import { defineStore } from "pinia";
import apiServices from "../services/api";

export const useUnitProductStore = defineStore("useUnitProductStore", {
    state: () => ({
        units: [],
        errorGetUnits: false,
    }),
    actions: {
        async getAllUnitProduct() {
            try {
                this.errorGetUnits = false;

                const response = await apiServices.get("unit");

                if (response.data.status_code === 200) {
                    this.units = response.data.datas;
                }
            } catch (error) {
                this.errorGetUnits = true;
            }
        },
    },
    persist: true,
});
