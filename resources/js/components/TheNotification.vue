<script setup>
import { notification } from "../stores/notifications";
</script>

<template>
  <div class="notification" v-if="notification">
    {{ notification.title }}
    {{ notification.body }}
  </div>
</template>

<style scoped>
.notification {
  position: absolute;
  bottom: 20px;
  right: 20px;
  width: 20%;
  height: 10%;
  background-color: red;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}
</style>