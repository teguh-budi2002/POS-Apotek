import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";
import router from "./router/routes";
import App from "./App.vue";

import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura";
import ToastService from "primevue/toastservice";
import Select from "primevue/select";
import Button from "primevue/button";
import Toast from "primevue/toast";
import Dialog from "primevue/dialog";
import Card from "primevue/card";
import Image from "primevue/image";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tooltip from 'primevue/tooltip';
import Breadcrumb from "primevue/breadcrumb";

import "primeicons/primeicons.css";

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
const app = createApp(App);

app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            prefix: "p",
            darkModeSelector: "dark-mode",
            cssLayer: false,
        },
    },
});
app.component("Select", Select);
app.component("Button", Button);
app.component("Dialog", Dialog);
app.component("Toast", Toast);
app.component("Card", Card);
app.component("Image", Image);
app.component("DataTable", DataTable);
app.component("Column", Column);
app.component("Breadcrumb", Breadcrumb);
app.directive('tooltip', Tooltip)
app.use(ToastService);
app.use(pinia);
app.use(router);

app.mount("#app");
