<script setup>
import { ref } from "vue";
import TheAppBar from ".//TheAppBar.vue";
import TheTabbar from "./TheTabbar.vue";
import TheNotification from "./TheNotification.vue";
import TheDrawer from "./TheDrawer.vue";
const drawer = ref();

function openDrawer() {
  drawer.value.toggle();
}
</script>


<template>
  <the-app-bar @open-drawer="openDrawer" />
  <div class="spacer-top">&nbsp;</div>
  <the-notification></the-notification>
  <main>
    <router-view v-slot="{ Component }">
      <keep-alive>
        <component :is="Component" />
      </keep-alive>
    </router-view>
  </main>
  <div class="spacer-bottom">&nbsp;</div>
  <the-tabbar />

  <the-drawer ref="drawer" />
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

