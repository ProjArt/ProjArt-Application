import { createApp } from "vue";
import App from "./components/App.vue";
import { plugin, defaultConfig } from "@formkit/vue";
import router from "./router/routes";


const app = createApp(App).use(router);
app.use(plugin, defaultConfig);



app.mount("#app");

navigator.serviceWorker.addEventListener("message", (event) => {
    console.log("message", event);
});