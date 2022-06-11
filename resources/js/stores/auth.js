import { computed } from "vue";

export const isAuthenticated = computed(() => localStorage.getItem("token"));

export const user = computed({
    get: () => {
        console.log(JSON.parse(localStorage.getItem("user")));
        return JSON.parse(localStorage.getItem("user"));
    },
    set: (value) => {
        console.log("on update le localuser", value);
        localStorage.setItem("user", JSON.stringify(value));
    },
});
