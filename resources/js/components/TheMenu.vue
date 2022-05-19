<script setup>
import { sideMenuWidth } from "../stores/sideMenu.js";
import { computed } from "vue";

function shrinkMenu() {
  sideMenuWidth.value = 0;
}
</script>

<template>
  <div class="side-menu">
    MENU

    <button @click="shrinkMenu()">Change</button>
  </div>
</template>

<style scoped>
.side-menu {
  background-color: #b7b7b7;
  border-right: 1px solid #e5e5e5;
  height: 100vh;
  width: v-bind(sideMenuWidth);
  position: fixed;
  top: 0;
  z-index: 1;
}
</style>