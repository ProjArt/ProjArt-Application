<script setup>
import { ref } from "vue"
import useFetch from '../composables/useFetch';
import { API } from '../stores/api';
const calendarNames = ref([]);
const calendar = ref({});
async function getCalendar() {
    const response = await useFetch({
        url: API.events.path(),
        method: API.events.method,
    });
    console.log(response.data);
    calendarNames.value = response.data.map(event => event.name);
    calendar.value = response.data
    console.log(calendar.value);
}
getCalendar();
</script>
<template>
    <FormKit type="select" label="Calendrier" name="calendar" :options="calendarNames" validation="required"
        validation-visibility="dirty" />
</template>
