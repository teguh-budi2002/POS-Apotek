import { defineStore } from "pinia";
import apiServices from "../services/api";
import Cookies from "js-cookie";
import { configToken } from "../config/token";

export const useAuthStore = defineStore("authStore", {
    state: () => ({
        userData: null,
        isError: false,
        submitProcess: false,
        token: Cookies.get("token") ? true : false,
    }),
    getters: {
        isLoggedin: (state) => state.token,
    },
    actions: {
        async Login(email, password) {
            try {
                this.submitProcess = true;
                const response = await apiServices.post("login/process", {
                    email,
                    password,
                });

                if (response.data.status_code === 200) {
                    this.submitProcess = false;

                    Cookies.set("token", response.data.datas.token, {
                        expires: configToken.expired_day,
                    });

                    Cookies.set("user", response.data.datas.userData, {
                        expires: configToken.expired_day,
                    });

                    this.router.push({
                        name: "dashboard",
                    });
                }
            } catch (error) {
                const errorData = error?.response?.data;

                if (errorData?.status_code === 401) {
                    this.submitProcess = false;
                    this.isError = true;
                }
            }
        },
    },
});
