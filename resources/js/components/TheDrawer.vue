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
      <span class="material-icons" @click="toggle">close</span>
      <img class="icon" src="/images/logo_REDY.svg" />
    </div>
    <div class="drawer__header-name">
      {{ user.gaps_user.firstname }} {{ user.gaps_user.name }}
    </div>
    <div class="hr"></div>
    <div class="drawer__content">
      <div v-for="route in buildMenu()" :key="route.path" class="drawer__content-item" @click="changePage(route.path)">
        {{ route.text }}
        <span class="material-icons">keyboard_arrow_right</span>
      </div>
    </div>
  </div>
  <div class="drawer-invisible" @click="toggle">&nbsp;</div>
</template>

<style scoped lang="scss">
.drawer {
  background-color: var(--drawer-bg-color);

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
  justify-content: space-between;
  align-items: center;
  width: 100%;
  height: var(--drawer-header-height);
  padding: var(--default-pading);
}

.drawer__content {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: flex-start;
  width: 100%;
  height: 100%;
  padding: var(--default-pading);
}

.drawer__content-item {
  width: 80%;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding-left: 2rem;
  margin-bottom: 1rem;
}

.icon {
  width: 50px;
}

.hr {
  width: 100%;
  height: 1px;
  background-color: black;
  margin-top: 2rem;
  margin-bottom: 2rem;
}
</style>