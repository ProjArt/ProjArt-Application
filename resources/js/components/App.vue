<script setup>
import { computed, ref } from "vue";
import TheAppBar from ".//TheAppBar.vue";
import TheTabbar from "./TheTabbar.vue";
import TheNotification from "./TheNotification.vue";
import TheDrawer from "./TheDrawer.vue";
import { user } from "../stores/auth";
import { isHome } from "../stores/route";

const drawer = ref();

function openDrawer() {
  drawer.value.toggle();
}
</script>


<template>
  <the-app-bar @open-drawer="openDrawer" v-if="!isHome" />
  <div class="spacer-top" v-if="!isHome">&nbsp;</div>
  <the-notification></the-notification>
  <main>
    <router-view v-slot="{ Component, name }">
      <template v-if="['mail', 'home'].includes(name)">
        <component :is="Component" />
      </template>
      <template v-else>
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </template>
    </router-view>
  </main>
  <div class="spacer-bottom" v-if="!isHome">&nbsp;</div>
  <the-tabbar />

  <the-drawer ref="drawer" v-if="!isHome" />
</template>

<style lang="scss" scoped>
main {
  width: 100%;
  overflow-y: hidden;
}

.spacer-top {
  width: 100%;
  height: 4.5rem;
}

.spacer-bottom {
  width: 100%;
  height: 5rem;
}
</style>

