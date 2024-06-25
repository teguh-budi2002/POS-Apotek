import { defineStore } from "pinia";
import apiServices from "../services/api";
import { useRouter } from "vue-router";
import Cookies from "js-cookie";
import { configToken } from "../config/token";
import { ref } from "vue";

export const useAuthStore = defineStore("authStore", {
    state: () => ({
        userData: null,
        isError: false,
        submitProcess: false,
        token: Cookies.get("token") || false,
        isAuth: false,
    }),
    getters: {
        isLoggedin: (state) => state.isAuth,
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

                    const router = useRouter();
                    router.push({
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

        async getAuthInfo() {
            await apiServices.get("auth-check").then((res) => {
                const checkAuth = res.data.is_auth;
                this.isAuth = checkAuth;
            });
        },
    },
});
