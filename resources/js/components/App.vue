<script setup>
import { computed, ref } from "vue";
import TheAppBar from ".//TheAppBar.vue";
import TheTabbar from "./TheTabbar.vue";
import TheNotification from "./TheNotification.vue";
import TheDrawer from "./TheDrawer.vue";
import { user } from "../stores/auth";
import { isHome } from "../stores/route";
import router from "../router/routes";
import ThePopup from "./ThePopup.vue";

const drawer = ref();
const getLocation = () => {
  console.log("getLocation");
  return window.location.pathname.replace(/^\//, "");
};

function openDrawer() {
  drawer.value.toggle();
}

const route = computed(() => router.currentRoute.value.name);
</script>


<template>
  <the-app-bar @open-drawer="openDrawer" v-if="!isHome" />
  <div class="spacer-top" v-if="!isHome">&nbsp;</div>
  <the-notification></the-notification>
  <router-view v-slot="{ Component }">
    <main :class="'main--' + getLocation()">
      <template v-if="['mail', ''].includes(route)">
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

  <the-drawer ref="drawer" v-if="!isHome" />

  <the-popup />
</template>

<style lang="scss" >
#app {
  display: flex;
  flex-direction: column;
  height: calc(100vh - var(--app-bar-height));
}
</style>

