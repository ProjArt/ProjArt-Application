<script setup>
import TheHeader from "./TheHeader.vue";
import TheNotification from "./TheNotification.vue";
import TheThemeManager from "./TheThemeManager.vue";
</script>


<template>
  <the-header />
  <Suspense>
  <the-theme-manager />
  </Suspense>
  <the-notification></the-notification>
  <router-view></router-view>
</template>


