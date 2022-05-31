<script setup>
import TheMenu from "./TheMenu.vue";
import TheBody from "./TheBody.vue";
import TheHeader from "./TheHeader.vue";
import TheNotification from "./TheNotification.vue";


</script>


<template>
  <the-header />
  <the-notification></the-notification>
  <router-view></router-view>
</template>


