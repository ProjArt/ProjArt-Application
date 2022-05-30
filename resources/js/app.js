import { createApp } from "vue";
import App from "./components/App.vue";
import { plugin, defaultConfig } from "@formkit/vue";
import router from "./router/routes";

navigator.serviceWorker.register('/tgv/sw.js');
const app = createApp(App).use(router);
app.use(plugin, defaultConfig);

app.mount("#app");