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

/* navigator.serviceWorker.getRegistrations().then(function(registrations) {
    for (let registration of registrations) {
        registration.unregister()
    }
}); */

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

    if ((xPos < minX && yPos > minY)) {
        e.preventDefault();
        return false;
    }
}, { passive: false });

useSwipe({
    excepts: ["calendar"],
    onSwipeLeft: () => {
        console.log("swipe left");
    },
    onSwipeRight: () => {
        drawer.value.toggle();
    },
});


// ============

if (!("path" in Event.prototype))
    Object.defineProperty(Event.prototype, "path", {
        get: function() {
            var path = [];
            var currentElem = this.target;
            while (currentElem) {
                path.push(currentElem);
                currentElem = currentElem.parentElement;
            }
            if (path.indexOf(window) === -1 && path.indexOf(document) === -1)
                path.push(document);
            if (path.indexOf(window) === -1)
                path.push(window);
            return path;
        }
    });