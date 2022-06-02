<script setup>
import { computed, ref, watch } from "vue";

defineExpose({
  toggle,
});
function toggle() {
  console.log("DRAWER TOGGLE FROM DRAWER");

  isOpen.value = !isOpen.value;

  if (isOpen.value) {
    disableScrolling();
  } else {
    enableScrolling();
  }
}

function disableScrolling() {
  let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  let scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

  window.onscroll = function () {
    window.scrollTo(scrollLeft, scrollTop);
  };
}

function enableScrolling() {
  window.onscroll = function () {};
}

const isOpen = ref(false);

const positionX = computed(() => (isOpen.value ? "0" : "-100%"));

watch(positionX, (newValue) => {
  console.log("DRAWER POSITION X:", newValue);
});
</script>

<template>
  <div class="drawer">
    <div class="drawer__header">
      <span class="material-icons" @click="toggle">close</span>
      <img class="icon" src="/images/logo_REDY.svg" />
    </div>
    <div class="drawer__content">COUCOU</div>
  </div>
  <div class="drawer-invisible" @click="toggle">&nbsp;</div>
</template>

<style scoped>
.drawer {
  background-color: var(--drawer-bg-color);

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: absolute;
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
  position: absolute;
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
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
  padding: var(--default-pading);
}

.icon {
  width: 50px;
}
</style>