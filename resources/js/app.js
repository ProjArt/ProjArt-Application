import { createApp } from "vue";
import App from "./components/App.vue";
import { plugin, defaultConfig } from "@formkit/vue";
import router from "./router/routes";
import { notification } from "./stores/notifications";


const app = createApp(App).use(router);
app.use(plugin, defaultConfig);



app.mount("#app");

navigator.serviceWorker.addEventListener("message", (event) => {
    notification.value = (event.data);
});