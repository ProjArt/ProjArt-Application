import { createRouter, createWebHistory } from "vue-router";
import home from "./HomeRoute.vue";
import calendar from "./CalendarRoute.vue";
import absences from "./AbsencesRoute.vue";
import marks from "./MarksRoute.vue";
import menus from "./MenusRoute.vue";
import notFound from "./404Route.vue";
import settings from "./SettingsRoute.vue";
import infos from "./InfosRoute.vue";
import students from "./StudentsListRoute.vue";
import teachers from "./TeachersListRoute.vue";
import { user } from "../stores/auth";
import register from "./RegisterRoute.vue";
import mails from "./MailsRoute.vue";
import mail from "./MailRoute.vue";
import sendMail from "./SendMailRoute.vue";

const routes = [
    { name: "home", path: "/", component: home, icon: "home", is_visible: [] },
    {
        name: "calendar",
        path: "/calendar",
        component: calendar,
        icon: "calendar_month",
        is_visible: ["*"],
    },
    {
        name: "absences",
        path: "/absences",
        component: absences,
        icon: "person_off",
        is_visible: ["student"],
    },
    {
        name: "marks",
        path: "/marks",
        component: marks,
        icon: "format_list_bulleted",
        is_visible: ["student"],
    },
    {
        name: "menus",
        path: "/menus",
        component: menus,
        icon: "restaurant_menu",
        is_visible: ["*"],
    },
    {
        name: "notFound",
        path: "/:pathMatch(.*)*",
        component: notFound,
        icon: "home",
        is_visible: [],
    },
    {
        name: "settings",
        path: "/settings",
        component: settings,
        icon: "home",
        is_visible: [],
    },
    {
        name: "infos",
        path: "/infos",
        component: infos,
        icon: "home",
        is_visible: [],
    },
    {
        name: "students",
        path: "/students",
        component: students,
        icon: "group",
        is_visible: [],
    },
    {
        name: "teachers",
        path: "/teachers",
        component: teachers,
        icon: "group",
        is_visible: [],
    },
    {
        name: "register",
        path: "/register",
        component: register,
        icon: "home",
        is_visible: [],
    },
    {
        name: "mails",
        path: "/mails",
        component: mails,
        icon: "mail",
        is_visible: ["*"],
    },
    {
        name: "mail",
        path: "/mails/:id",
        component: mail,
        icon: "home",
        is_visible: [],
    },
    {
        name: "sendMail",
        path: "/mails/send",
        component: sendMail,
        icon: "home",
        is_visible: [],
    },
];

export const routesNames = () => {

    return routes;
};

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async(to, from) => {
    const isAuthenticated = localStorage.getItem("token");
    if (!isAuthenticated && to.name !== "home" && to.name !== "register") {
        return { name: "home" };
    }
});

export default router;