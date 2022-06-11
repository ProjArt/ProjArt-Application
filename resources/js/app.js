import { createApp } from "vue";
import App from "./components/App.vue";
import { plugin, defaultConfig } from "@formkit/vue";
import router from "./router/routes";
import {
    notification,
    registerToChannelNotification,
} from "./stores/notifications";


/* navigator.serviceWorker.register('/workerCacheFetched.js');
 */

const app = createApp(App).use(router);
app.use(plugin, defaultConfig);

app.mount("#app");

/* registerToChannelNotification("all");

navigator.serviceWorker.addEventListener("message", (event) => {
    notification.value = event.data;
}); */

/* if (window.safari) {
    history.pushState(null, null, window.location.href);
    window.onpopstate = function() {
        history.go(1);
    }
} */

document.querySelector("#app").addEventListener("touchstart", (e) => {
    const xPos = e.touches[0].clientX;
    const minX = window.innerWidth * 0.05;
    console.log(xPos, minX);
    if (xPos > minX && xPos < window.innerWidth - minX) return;
    e.preventDefault();
});