import { createApp } from "vue";
import router from "@/router";
import { createPinia } from "pinia";
import App from "@/App.vue";
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import "./style.css";

const app = createApp(App);

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

app.use(router);
app.use(pinia);

app.mount("#app");