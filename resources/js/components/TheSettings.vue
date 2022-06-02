<script setup>
import { user } from "../stores/auth";
import TheThemeManager from "./TheThemeManager.vue"
</script>

<template>
  <div class="settings-group">SETTINGS</div>
  <Suspense><the-theme-manager/></Suspense>
  <br/>
  <div class="user">{{ user }}</div>
</template>

<style scoped>
</style>