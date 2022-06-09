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

console.log("is404App", is404)



const drawer = ref();

function openDrawer() {
  drawer.value.toggle();
}

const route = computed(() => router.currentRoute.value.name);
</script>


<template>
  <the-app-bar @open-drawer="openDrawer" v-if="!isHome && !is404" />
  <div class="spacer-top" v-if="!isHome && !is404">&nbsp;</div>
  <the-notification></the-notification>
  <main>
    <router-view v-slot="{ Component }">
      <template v-if="['mail', ''].includes(route)">
        <component :is="Component" />
      </template>
      <template v-else>
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </template>
    </router-view>
  </main>
  <div class="spacer-bottom" v-if="!isHome && !is404">&nbsp;</div>
  <the-tabbar />

  <the-drawer ref="drawer" v-if="!isHome && !is404"/>
</template>

<style lang="scss" scoped>
main {
  width: 100%;
  overflow-y: hidden;
}

.spacer-top {
  width: 100%;
  height: 7rem;
}

.spacer-bottom {
  width: 100%;
  height: 7rem;
}
</style>

