import { ref, computed } from 'vue';


const _notifications = ref([]);

export const notification = computed({
    get: () => _notifications.value[_notifications.value.length - 1],
    set: (value) => {
        _notifications.value.push(value);
        console.log("notifications", _notifications.value);
    }
});