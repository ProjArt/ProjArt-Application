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

function buildMenu() {
  let menu = routesNames().filter(
    (route) =>
      route.is_visible.includes(user.value.role) ||
      route.is_visible.includes("*")
  );
  return menu.sort((a, b) => a.order - b.order);
}
</script>
<template>
  <div class="menu" v-if="isAuthenticated">
    <div
      v-for="route in buildMenu()"
      :key="route"
      :class="route.order == 0 ? 'menu__main' : 'menu__item'"
    >
      <router-link :to="route.path" class="menu__item-link">
        <span class="menu-icon material-icons">{{ route.icon }}</span>
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
  color: var(--text-color);
  text-decoration: inherit;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding-top: var(--default-padding);
  padding-bottom: var(--default-padding);
}

.menu__item-link.router-link-active {
  background-color: var(--tab-bar-active-color);
}

.menu__main .menu__item-link {
  position: absolute;
  left: 50%;
  bottom: 20%;
  width: 7vh;
  height: 5vh;
  transform: translateX(-50%);
  border-radius: 50%;
}

.menu__main .menu__item-link {
  background-color: red;
}
</style>