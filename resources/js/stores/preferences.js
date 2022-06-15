import { computed, toRaw } from "vue";

export const theme = computed({
    get: () => {
        return toRaw(JSON.parse(localStorage.getItem("theme")));
    },
    set: (value) => {
        localStorage.setItem("theme", JSON.stringify(value));
    },
});
