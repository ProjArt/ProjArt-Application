import { ref, watch } from 'vue';
import { usePopup } from './usePopup.js';
export const loaded = ref(false);

export function useLoading({ waitFor = () => {}, then = () => {} } = {}) {
    usePopup({
        title: "Téléchargement des données",
        body: "Nous téléchargeons les données... Veuillez patienter.",
        buttons: [],
    });

    watch(loaded, (newVal) => {
        if (newVal) {
            useClosePopup();
        }
    });
    waitFor().then(() => {
        loaded.value = true;
        then();
    });
}