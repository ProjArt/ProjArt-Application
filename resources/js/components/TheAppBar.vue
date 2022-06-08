<script setup>
import { user } from "../stores/auth";
const emits = defineEmits(["open-drawer"]);

function openDrawer() {
  emits("open-drawer");
}
</script>

<template>
  <div class="app-bar">
    <span @click="openDrawer()" class="material-icons">menu</span>
    <img class="icon" src="/images/logo_REDY.svg" />
    <span class="material-icons">search</span>
  </div>
</template>

<style scoped>
.icon-transparent {
  color: transparent;
}
.app-bar {
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  height: var(--app-bar-height);
  background-color: var(--app-bar-bg-color);
  margin-left: var(--default-pading);
  margin-right: var(--default-pading);
  z-index: 100;
}

.icon {
  width: 50px;
}
</style>