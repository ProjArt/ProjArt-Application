import { useRouter } from "vue-router";
import { computed } from "vue";

export const isHome = computed(() => {
    const router = useRouter();
    return router.currentRoute.value.path === "/";
});

export const is404 = computed(() => {
    const router = useRouter();
    return router.currentRoute.value.name == "notFound";
})