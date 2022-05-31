import { createRouter, createWebHistory } from "vue-router";
import home from "./HomeRoute.vue";
import calendar from "./CalendarRoute.vue";
import absences from "./AbsencesRoute.vue";
import marks from "./MarksRoute.vue";
import menus from "./MenusRoute.vue";
import notFound from "./404Route.vue";
import settings from "./SettingsRoute.vue";
import infos from "./InfosRoute.vue";

const routes = [
    { name: "home", path: "/", component: home },
    { name: "calendar", path: "/calendar", component: calendar },
    { name: "absences", path: "/absences", component: absences },
    { name: "marks", path: "/marks", component: marks },
    { name: "menus", path: "/menus", component: menus },
    { name: "notFound", path: '/:pathMatch(.*)*', component: notFound }
    { name: "settings", path: "/settings", component: settings },
    { name: "infos", path: "/infos", component: infos }
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