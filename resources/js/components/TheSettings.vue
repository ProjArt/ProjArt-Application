<script setup>
import { user } from "../stores/auth";
import TheThemeManager from "./TheThemeManager.vue"
import useLogout from "../composables/useLogout.js"

async function disconnectAndRedirect(){
  await useLogout();
  
}
</script>

<template>
  <div class="settings-group">SETTINGS</div>
  <Suspense><the-theme-manager/></Suspense>
  <br/>
  <div class="user">{{ user }}</div>
  <div class="disconnect">
    <form class="disconnectForm" method="post" @submit.prevent="disconnectAndRedirect" action="/register">
    <input type="submit" value="Se déconnecter"/>
  </form>
  </div>
</template>

<style scoped>
</style>