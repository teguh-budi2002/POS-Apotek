import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router/routes";
import App from "./App.vue";

import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura";
import ToastService from "primevue/toastservice";
import Button from "primevue/button";
import Toast from "primevue/toast";
import Dialog from "primevue/dialog";

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
app.component("Button", Button);
app.component("Dialog", Dialog);
app.component("Toast", Toast);
app.use(ToastService);
app.use(pinia);
app.use(router);

app.mount("#app");
