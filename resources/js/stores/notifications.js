import { ref, computed } from 'vue';
import { API } from "./api";
import useFetch from "../composables/useFetch";


const _notifications = ref([]);

export const notification = computed({
    get: () => _notifications.value[_notifications.value.length - 1],
    set: (value) => {
        _notifications.value.push(value);
        console.log("notifications", _notifications.value);
    }
});

export async function sendNotification({ title, message, to }) {

    const response = await useFetch({
        url: API.sendNotification.path(),
        method: API.sendNotification.method,
        data: {
            title,
            message,
            to,
        }
    });

    console.log("sendNotification", response);
}