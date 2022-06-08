<script setup>
import TheLogin from "../components/TheLogin.vue";
import TheRegister from "../components/TheRegister.vue";
import { API } from "../stores/api";
import useFetch from "../composables/useFetch";
import { ref, toRaw } from "vue";
import { routesNames } from "./routes";
import router from "../router/routes";

const isAuthenticated = ref(false);
(async function checkAuth() {
  const response = await useFetch({
    url: API.me.path(),
    method: API.me.method,
    data: {},
  });
  if (response.success === true) {
    isAuthenticated.value = true;
    let route = routesNames().find((e) => e.name == "calendar");
    router.push(route.path);
  } else {
    isAuthenticated.value = false;
  }
})();
</script>
<template>
  <the-login v-if="!isAuthenticated" />
  <!-- <the-register v-if="!isAuthenticated" /> -->
</template>
<style>
</style>