import { createApp } from "vue";
import App from "./components/App.vue";
import { plugin, defaultConfig } from "@formkit/vue";
import router from "./router/routes";
import { drawer } from "./stores/drawer";
import useSwipe from "./composables/useSwipe";
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


//to disable back navigation in safari
document.querySelector("#app").addEventListener("touchstart", (e) => {
    const xPos = e.touches[0].clientX;
    const yPos = e.touches[0].clientY;
    const minX = 30;
    const minY = 50;

    if (!(xPos < minX && yPos > minY)) return;
    console.log(xPos, yPos);

    e.preventDefault();
});

useSwipe({
    excepts: ["calendar"],
    onSwipeLeft: () => {
        console.log("swipe left");
    },
    onSwipeRight: () => {
        drawer.value.toggle();
    },
});