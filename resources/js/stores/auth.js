import { computed } from "vue";


export const isAuthenticated = computed(() => localStorage.getItem("token"));