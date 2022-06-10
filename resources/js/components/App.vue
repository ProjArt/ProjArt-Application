<script setup>
import { computed, ref } from "vue";
import TheAppBar from ".//TheAppBar.vue";
import TheTabbar from "./TheTabbar.vue";
import TheNotification from "./TheNotification.vue";
import TheDrawer from "./TheDrawer.vue";
import { user } from "../stores/auth";
import { isHome } from "../stores/route";
import { is404 } from "../stores/route";
import router from "../router/routes";
import ThePopup from "./ThePopup.vue";

//console.log("is404App", is404")

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
  <the-app-bar @open-drawer="openDrawer" v-if="!isHome && !is404" />
  <the-notification></the-notification>
  <router-view v-slot="{ Component }">
    <main :class="'main--' + getLocation()" v-if="!is404 && !isHome">
      <template v-if="['mail', ''].includes(route)">
        <component :is="Component" />
      </template>
      <template v-else>
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </template>
    </main>
    <main :class="'main-no-space-top'" v-else>
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

  <the-drawer ref="drawer" v-if="!isHome && !is404" />
  <the-popup />
</template>

<style lang="scss" >
#app {
  height: calc(100vh - var(--app-bar-height));
  background-color: var(--background-color);
}

.main-no-space-top {
  margin: 0 0 0 0;
}
</style>

