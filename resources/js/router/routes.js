import { createRouter, createWebHistory } from "vue-router";
import home from "./HomeRoute.vue";
import calendar from "./CalendarRoute.vue";

const routes = [
    { name: "home", path: "/", component: home },
    { name: "calendar", path: "/calendar", component: calendar },
];

export const routesNames = (() => {
    const obj = {};
    routes.forEach((route) => {
        obj[route.name] = route.path;
    });
    return obj;
})();

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async(to, from) => {
    const isAuthenticated = localStorage.getItem("token");
    if (!isAuthenticated && to.name !== "home") {
        return { name: "home" };
    }
});

export default router;