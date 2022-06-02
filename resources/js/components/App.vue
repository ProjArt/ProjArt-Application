<script setup>
import TheAppBar from ".//TheAppBar.vue";
import TheTabbar from "./TheTabbar.vue";
import TheNotification from "./TheNotification.vue";
import TheThemeManager from "./TheThemeManager.vue";
</script>


<template>
  <the-app-bar />
  <div class="spacer-top">&nbsp;</div>
  <Suspense>
    <the-theme-manager />
  </Suspense>
  <the-notification></the-notification>
  <main>
    <router-view></router-view>
  </main>
  <div class="spacer-bottom">&nbsp;</div>
  <the-tabbar />
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
  height: 3rem;
}
</style>

