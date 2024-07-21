<template>
    <div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
        <Toast />
        <Dialog
            v-model:visible="visible"
            modal
            header="User Not Authenticated"
            :style="{ width: '35rem', backgroundColor: 'white' }"
            :breakpoints="{ '640px': '25rem' }"
        >
            <template #container>
                <div class="p-5">
                    <p
                        class="text-xl text-center text-rose-500 whitespace-nowrap mb-3"
                    >
                        PERHATIAN
                    </p>

                    <p class="text-slate-600 block mb-5">
                        Silakan login terlebih dahulu untuk mengakses dashboard.
                    </p>
                    <Button
                        label="Oke, saya mengerti."
                        @click="handleCloseAuthInfoModal"
                        class="p-4 w-fit !text-white !bg-rose-400 !border-0 hover:!bg-rose-300"
                    ></Button>
                </div>
            </template>
        </Dialog>
        <div
            class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1"
        >
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
                <div>
                    <img :src="'/assets/images/logo.png'" class="w-mx-auto" />
                </div>
                <div class="mt-12 flex flex-col items-center">
                    <div class="w-full flex-1 mt-8">
                        <div class="mx-auto max-w-xs">
                            <p
                                class="text-4xl mb-8 text-center text-slate-600 font-semibold"
                            >
                                Login Dashboard
                            </p>
                            <form @submit.prevent="onSubmitLogin">
                                <input
                                    class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:bg-white"
                                    :class="{
                                        'outline outline-1 outline-rose-500 bg-white':
                                            errors.email,
                                    }"
                                    type="text"
                                    v-model="email"
                                    v-bind="emailAttr"
                                    placeholder="Email"
                                />
                                <p class="mt-2 text-rose-500">
                                    {{ errors.email }}
                                </p>
                                <input
                                    class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:bg-white mt-5"
                                    :class="{
                                        'outline outline-1 outline-rose-500 bg-white':
                                            errors.password,
                                    }"
                                    type="password"
                                    v-model="password"
                                    v-bind="passwordAttr"
                                    placeholder="Password"
                                />
                                <p class="mt-2 text-rose-500">
                                    {{ errors.password }}
                                </p>
                                <button
                                    :disabled="authStore.submitProcess"
                                    class="mt-5 disabled:bg-blue-200 disabled:cursor-wait tracking-wide font-semibold bg-blue-400 text-white w-full py-4 rounded-lg hover:bg-blue-500 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none"
                                >
                                    <span>Masuk</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-fit lg:block hidden">
                <img
                    :src="'assets/images/bg-login.jpg'"
                    alt="bg-login"
                    class="w-full h-full rounded-r-md"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from "../../stores/auth";
import { useToast } from "primevue/usetoast";
import { useForm } from "vee-validate";
import * as Yup from "yup";
import { useRouter, useRoute } from "vue-router";
import { ref } from "vue";

export default {
    setup() {
        let visible = ref(false);
        const toast = useToast();
        const authStore = useAuthStore();
        const router = useRouter();
        const route = useRoute();

        if (localStorage.getItem("checkAuth") === "userNotAuthenticated") {
            visible.value = true;
        }

        const handleCloseAuthInfoModal = () => {
            localStorage.removeItem("checkAuth");
            visible.value = false;
        };

        const { errors, handleSubmit, defineField } = useForm({
            validationSchema: Yup.object().shape({
                email: Yup.string()
                    .email("Alamat email harus valid.")
                    .required("Email wajib diisi"),
                password: Yup.string().required("Password wajib diisi"),
            }),
        });

        const [email, emailAttr] = defineField("email");
        const [password, passwordAttr] = defineField("password");

        const onSubmitLogin = handleSubmit(async (values) => {
            const { email, password } = values;
            await authStore.Login(email, password);

            if (authStore.isError) {
                toast.add({
                    severity: "error",
                    summary: "Login Gagal",
                    detail: "Pengguna Tidak Ditemukan",
                    life: 3000,
                });
            } else {
                router.replace({ name: "dashboard" });
            }
        });

        if (route.query.isLogout) {
            toast.add({
                severity: "error",
                summary: "Logout",
                detail: "Logout Berhasil",
                life: 3000,
            });
        }

        return {
            email,
            emailAttr,
            password,
            passwordAttr,
            onSubmitLogin,
            handleSubmit,
            errors,
            authStore,
            visible,
            handleCloseAuthInfoModal,
        };
    },
};
</script>

<style></style>
