<script setup>
let swRegistration;
async function askForNotificationPermission() {
    console.log(Notification.permission)
    if ("Notification" in window && Notification.permission !== "granted") {
        const permission = await Notification.requestPermission();
        const notification = new Notification("Hi there!");
    }
}
askForNotificationPermission()

if ('serviceWorker' in navigator && 'PushManager' in window) {
    console.log('Service Worker and Push is supported');

    navigator.serviceWorker.register('sw.js')
        .then(function (swReg) {
            console.log('Service Worker is registered', swReg);

            swRegistration = swReg;
        })
        .catch(function (error) {
            console.error('Service Worker Error', error);
        });
} else {
    console.warn('Push messaging is not supported');
}

</script>
<template>
    <button @click="askForNotificationPermission">notifications</button>
</template>
