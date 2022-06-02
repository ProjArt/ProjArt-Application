import { ref, computed } from "vue";
import { API } from "./api";
import useFetch from "../composables/useFetch";

const beamsClient = new PusherPushNotifications.Client({
    instanceId: process.env.MIX_PUSHER_APP_ID,
});

const _notifications = ref([]);

export const notification = computed({
    get: () => _notifications.value[_notifications.value.length - 1],
    set: (value) => {
        _notifications.value.push(value);
    },
});

export async function sendNotification({ title, message, to }) {
    console.log("sends notification", title, message, to);
    const response = await useFetch({
        url: API.sendNotification.path(),
        method: API.sendNotification.method,
        data: {
            title,
            message,
            to,
        },
    });
    console.log(response);
}

export async function registerToChannelNotification(channel) {
    //return; // desactivated for testing purpose
    var isSafari = window.safari !== undefined;

    var ua = window.navigator.userAgent;
    var iOS = !!ua.match(/iPad/i) || !!ua.match(/iPhone/i);
    var webkit = !!ua.match(/WebKit/i);
    var iOSSafari = iOS && webkit && !ua.match(/CriOS/i);
    if (isSafari || iOSSafari) {
        return;
    }
    await Notification.requestPermission(async function(permission) {
        if (permission === "granted") {
            await beamsClient.start();
            await beamsClient.addDeviceInterest(channel);
            const deviceInterests = await beamsClient.getDeviceInterests();
            console.log("Device registered to channels: " + deviceInterests);
        }
    });
}