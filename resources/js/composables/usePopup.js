import { ref } from 'vue';

export const showPopup = ref(false);

export const options = ref({});

export function usePopup({ title = '', body = '', buttons = [] } = {}) {
    options.value = { title, body, buttons };
    showPopup.value = true;
}

export function useClosePopup() {
    showPopup.value = false;
}