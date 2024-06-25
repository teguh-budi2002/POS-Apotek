import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura";
import ToastService from "primevue/toastservice";
import router from "./router/routes";
import App from "./App.vue";
import { useAuthStore } from "./stores/auth";

const pinia = createPinia();
const app = createApp(App);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            prefix: "p",
            darkModeSelector: "system",
            cssLayer: false,
        },
    },
});
app.use(ToastService);
app.use(pinia);
app.use(router);

const authStore = useAuthStore();
authStore.getAuthInfo().then(() => {
    app.mount("#app");
});
