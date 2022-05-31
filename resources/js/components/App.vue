<script setup>
import TheMenu from "./TheMenu.vue";
import TheBody from "./TheBody.vue";
import TheHeader from "./TheHeader.vue";
import TheNotification from "./TheNotification.vue";
import TheThemeManager from "./TheThemeManager.vue";


</script>


<template>
  <the-header />
  <the-theme-manager />
  <the-notification></the-notification>
  <router-view></router-view>
</template>


