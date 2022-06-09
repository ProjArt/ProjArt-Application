<script setup>
import { computed, ref } from "vue";
import TheAppBar from ".//TheAppBar.vue";
import TheTabbar from "./TheTabbar.vue";
import TheNotification from "./TheNotification.vue";
import TheDrawer from "./TheDrawer.vue";
const drawer = ref();
const getLocation = (() => {
  console.log("getLocation");
  return window.location.pathname.replace(/^\//, "");
})

function openDrawer() {
  drawer.value.toggle();
}
</script>


<template>
  <the-app-bar @open-drawer="openDrawer" />
  <the-notification></the-notification>
  <router-view v-slot="{ Component, name }">
    <main :class="'main--' + getLocation()">
      <template v-if="name === 'mail'">
        <component :is="Component" />
      </template>
      <template v-else>
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </template>
    </main>
  </router-view>
  <the-tabbar />

  <the-drawer ref="drawer" />
</template>

<style lang="scss" >
#app {
  display: flex;
  flex-direction: column;
  height: calc(100vh - var(--app-bar-height));
}
</style>

