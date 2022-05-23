import { createRouter, createWebHistory } from "vue-router";
import HomeRoute from "./HomeRoute.vue";

const routes = [{ name: "home", path: "/", component: HomeRoute }];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// router.beforeEach(async (to, from) => {
//     const isAuthenticated = localStorage.getItem("token");
//     if (!isAuthenticated && to.name !== "home") {
//         return { name: "home" };
//     }
// });

export default router;
