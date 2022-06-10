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
import { isHome } from "../stores/route";
import { is404 } from "../stores/route";

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
  menu.sort((a, b) => a.order - b.order);
  menu.splice(3, 0, {
    name: "spacer",
    path: "",
    label: "",
    is_visible: [],
    order: 3,
  });
  console.log(menu);
  return menu;
}
</script>
<template>
  <div class="menu" v-if="!isHome && !is404">
    <div
      v-for="route in buildMenu()"
      :key="route"
      :class="route.order == 0 ? 'menu__main' : 'menu__item'"
    >
      <div v-if="route.path">
        <router-link :to="route.path" class="menu__item-link">
          <span class="menu__icon material-icons">{{ route.icon }}</span>
        </router-link>
      </div>
      <div v-else class="menu__item-link"></div>
    </div>
  </div>
</template> 


<style scoped lang="scss">
.menu {
  background-color: var(--tab-bar-bg-color);
  box-shadow: 0px -2px 14px rgba(0, 0, 0, 0.06);
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
  padding-top: var(--spacer-sm);
  padding-bottom: var(--spacer-sm);
}

.menu__item-link .menu__icon {
  color: var(--inactive-color);
}

.menu__item-link.router-link-active .menu__icon {
  color: var(--tab-bar-active-color);
}

.menu__main .menu__item-link {
  position: absolute;
  left: 50%;
  bottom: 2rem;
  width: 6rem;
  height: 2rem;
  transform: translateX(-50%);
  border-radius: 50%;
  box-shadow: 1px -3px 10px rgb(0 0 0 / 20%);
}

.menu__main .menu__item-link {
  background-color: var(--inactive-color);
}

.menu__main .menu__item-link.router-link-active {
  background-color: var(--tab-bar-active-color);
}

.menu__main .menu__item-link.router-link-active .menu__icon {
  color: #fff;
}

.menu__main .menu__icon {
  color: #fff;
}
</style>