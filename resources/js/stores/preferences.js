import { computed } from "vue";



export const theme = computed({
    get: () => {
        return JSON.parse(localStorage.getItem("theme"));
    },
    set: (value) => {
        localStorage.setItem("theme", JSON.stringify(value));
    }
});