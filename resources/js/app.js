import "./bootstrap";
import { createApp, markRaw } from "vue";
import { createPinia } from "pinia";
import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura";
import ToastService from "primevue/toastservice";
import router from "./router/routes";
import App from "./App.vue";

const pinia = createPinia();
pinia.use(({ store }) => {
    store.router = markRaw(router);
});
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

app.mount("#app");
