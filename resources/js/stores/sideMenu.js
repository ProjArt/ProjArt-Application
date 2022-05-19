import { ref, computed } from 'vue';

const sideMenuCompact = ref(true);

export const sideMenuWidth = computed({
    get: () => {
        if (sideMenuCompact.value) {
            return '18vw';
        }
        return '5vw';
    },
    set: (value) => {
        sideMenuCompact.value = !sideMenuCompact.value;
    }
});