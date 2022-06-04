<script setup>
import useLogout from "../composables/useLogout";
import { routesNames } from "../router/routes";
import { isAuthenticated } from "../stores/auth";
import {
  sendNotification,
  //askPermissionNotification,
} from "../stores/notifications";
import { user } from "../stores/auth";
import { changeCssColorsVariable } from "../composables/changeCssColorsVariable.js";

console.log(isAuthenticated.value);

changeCssColorsVariable();

async function _send() {
  await sendNotification({
    title: "coucou",
    message: "test",
    to: user.value.username,
  });
}
</script>
<template>
  <div class="menu" v-if="isAuthenticated">
    <div v-for="route in routesNames()" :key="route" class="menu__item">
      <router-link :to="route.path" class="menu__item-link">
        <span class="menu-icon material-icons">{{ route.icon }}</span>
        <span class="menu-title">{{ route.name }}</span>
      </router-link>
    </div>
  </div>
</template> 


<style scoped lang="scss">
.menu {
  background-color: var(--tab-bar-bg-color);
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  width: 100%;
  position: fixed;
  bottom: 0;
  left: 0;
  z-index: 100;
}

.menu__item {
  width: 100%;
  height: 100%;
}
.menu__item-link {
  color: inherit;
  text-decoration: inherit;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: var(--default-pading);
}

.menu__item-link.router-link-active {
  background-color: var(--tab-bar-active-color);
}
</style>