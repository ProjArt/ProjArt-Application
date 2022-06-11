import { ref } from 'vue';

export const showPopup = ref(false);

export const options = ref({});

export function usePopup({ title = '', body = '', buttons = [], barrierDismissible = true } = {}) {
    options.value = { title, body, buttons, barrierDismissible };
    showPopup.value = true;
}

export function useClosePopup() {
    showPopup.value = false;
}