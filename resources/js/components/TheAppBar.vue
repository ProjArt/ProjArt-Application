<script setup>
import TheLogo from "./TheLogo.vue";

const emits = defineEmits(["open-drawer"]);

function openDrawer() {
  emits("open-drawer");
}
</script>

<template>
  <div class="app-bar">
    <span @click="openDrawer()" class="material-icons">menu</span>
    <router-link :to="'/'">
      <the-logo></the-logo>
    </router-link>
    <span class="material-icons" style="color: transparent">search</span>
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
  width: 100vw;
  height: var(--app-bar-height);
  background-color: var(--background-color);
  z-index: 100;
}

.icon {
  width: 50px;
}

.app-bar .material-icons {
  margin-left: var(--default-padding);
  margin-right: var(--default-padding);
  color: var(--accent-color);
}
</style>