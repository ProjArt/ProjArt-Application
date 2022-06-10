<script setup>
import { showPopup, options, useClosePopup } from "../composables/usePopup";
import { ref, computed, toRaw, watch, watchEffect } from "vue";

watch(showPopup, (newValue) => {
  console.log("showPopup", showPopup.value);
});
</script>

<template>
  <div v-if="showPopup">
    <div class="popup__bg"></div>
    <div class="popup__container">
      <div class="popup__title">{{ options.title }}</div>
      <div class="popup__body">{{ options.body }}</div>
      <div class="popup__footer">
        <button
          :class="'popup__button--' + (button.main ? 'main' : 'secondary')"
          @click="button.onClick()"
          v-for="button in options.buttons"
          :key="button.title"
        >
          {{ button.title }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.popup__bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 100;
}
.popup__container {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 95vw;
  max-width: 400px;
  background-color: #fff;
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  z-index: 9999;
  padding: var(--spacer-md) 0 var(--spacer-md) 0;
  border-radius: var(--border-radius-md);
}

.popup__title {
  font-size: 2.6rem;
  font-weight: 800;
  margin-bottom: 2rem;
}

.popup__body {
  font-size: 1.8rem;
  margin-bottom: 2rem;
}

.popup__button--main {
  background-color: var(--accent-color);
  color: white;
  border-radius: var(--border-radius-md);
  height: 3.4rem;
  width: 15.8rem;
  border: none;
  margin: var(--spacer-xsm);
}

.popup__button--secondary {
  background-color: #fff;
  color: var(--accent-color);
  border-radius: var(--border-radius-md);
  border: 3px solid var(--accent-color);
  height: 3.4rem;
  width: 15.8rem;
  margin: var(--spacer-xsm);
}
</style>