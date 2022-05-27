<script setup>
import TheLogin from '../components/TheLogin.vue';
import TheRegister from '../components/TheRegister.vue';
import { API } from "../stores/api";
import useFetch from "../composables/useFetch";
import { ref, toRaw } from "vue";
const isAuthenticated = ref(false);
(async function checkAuth() {
    const response = await useFetch({
        url: API['api/me'].path(),
        method: API['api/me'].method,
        data: {}
    });
    console.log(response)
    if (response.success === true) {
        isAuthenticated.value = true;
        window.location.href += "/calendar";
    } else {
        isAuthenticated.value = false;
    }
})()

</script>
<template>
    <the-login v-if="!isAuthenticated" />
    <the-register v-if="!isAuthenticated" />
</template>
<style>
</style>