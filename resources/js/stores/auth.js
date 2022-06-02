import { computed } from "vue";


export const isAuthenticated = computed(() => localStorage.getItem("token"));

export const user = computed({
    get: () => {
        return JSON.parse(localStorage.getItem("user"));
    },
    set: (value) => {
        localStorage.setItem("user", JSON.stringify(value));
        console.log("on update le localuser")
    }
});