<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { user } from "../stores/auth";
import router from "../router/routes";
import { routesNames } from "../router/routes";

defineExpose({
  toggle,
});
function toggle() {
  console.log("DRAWER TOGGLE FROM DRAWER");

  isOpen.value = !isOpen.value;
}

const isOpen = ref(false);

const positionX = computed(() => (isOpen.value ? "0" : "-100%"));

watch(positionX, (newValue) => {
  console.log("DRAWER POSITION X:", newValue);
});

function changePage(page) {
  console.log("Drawer change page:", page);
  router.replace(page);
  isOpen.value = false;
}

function buildMenu() {
  let menu = routesNames().filter((route) => route.is_secondary);
  menu.sort((a, b) => a.order - b.order);
  return menu;
}
</script>

<template>
  <div class="drawer">
    <div class="drawer__header">
      <span class="material-icons close" @click="toggle">close</span>
    </div>
    <div class="drawer__header-name">
      {{ user.gaps_user.name }} {{ user.gaps_user.firstname }}
    </div>
    <div class="hr"></div>
    <div class="drawer__content">
      <div v-for="route in buildMenu()" :key="route.path" class="drawer__content-item" @click="changePage(route.path)">
        <div class="drawer__content-item-text">
          {{ route.text }}
        </div>
        <span class="material-icons keyboard_arrow">keyboard_arrow_right</span>
      </div>

      <div class="drawer__content-telegram">
        <div class="hr"></div>

        <div class="drawer__content-telegram-text">
          L'application RedY est aussi disponible sur Telegram. Rejoingez-nous !
        </div>
        <a class="drawer__content-telegram-link" href="https://t.me/redy_gaps_bot">Telegram <span
            class="material-icons">telegram</span>
        </a>
      </div>
    </div>
  </div>
  <div class="drawer-invisible" @click="toggle">&nbsp;</div>
</template>

<style scoped lang="scss">
@import "../../sass/abstracts/mixins";

.drawer {
  background-color: var(--background-color);
  color: var(--text-color);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: fixed;
  top: 0;
  left: v-bind(positionX);
  z-index: 1000;
  height: 100%;
  width: 75%;
  transition: cubic-bezier(0.075, 0.82, 0.165, 1) 0.5s;
  font-size: 1.2rem;
}

.close {
  margin: var(--default-padding);
}

.keyboard_arrow {
  margin: 0 var(--default-padding);
}

.drawer-invisible {
  background-color: var(--drawer-invisible-background-color);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
  z-index: 999;
  position: fixed;
  top: 0;
  left: v-bind(positionX);
}

.drawer__header {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
  align-items: center;
  width: 100%;
  height: var(--drawer-header-height);
  padding: var(--default-pading);
}

.drawer__header span {
  color: var(--secondary-color);
}

.drawer__header-name {
  margin-top: var(--spacer-sm);
  @include font-h1(var(--text-color), center);
}

.drawer__content {
  display: flex;
  flex-direction: column;
  overflow-y: scroll;
  width: 100%;
  height: 100%;
  padding: var(--default-pading);
  color: var(--primary-color);
}

.drawer__content-item {
  width: 100%;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--spacer-sm);
  color: var(--accent-color);
  font-weight: 600;
}

.drawer__content-item-text {
  padding-left: var(--default-padding);
  font-size: 1.8rem;
}

.drawer__content-telegram {
  padding-left: var(--default-padding);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  margin: 0 0 5rem 0;
  height: 100%;
}

.drawer__content-telegram-text {
  font-weight: 600;
  font-size: 1.8rem;
  color: var(--text-color);
  line-height: 1.5;
}

.drawer__content-telegram-link {
  display: flex;
  flex-direction: row;
  align-items: center;
  text-decoration: none;
  margin-top: var(--spacer-sm);
  @include font-text-calendar-content(var(--accent-color), left);

  a {
    display: flex;
    justify-content: center;
  }
}

.drawer__content-telegram-link a {
  all: unset;
  color: var(--accent-color);
  font-weight: 600;
}

.icon {
  width: 50px;
}

.hr {
  width: 100%;
  height: 1px;
  background-color: var(--text-color);
  margin-top: 2rem;
  margin-bottom: 2rem;
}
</style>