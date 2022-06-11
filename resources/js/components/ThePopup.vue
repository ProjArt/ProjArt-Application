<script setup>
import { showPopup, options, useClosePopup } from "../composables/usePopup";
import { ref, computed, toRaw, watch, watchEffect } from "vue";
</script>

<template>
  <div v-if="showPopup">
    <div class="popup__bg" @click="useClosePopup()"></div>
    <div class="popup__container">
      <div class="popup__title">{{ options.title }}</div>
      <div class="popup__body" v-html="options.body"></div>
      <div class="popup__footer">
        <button :class="'popup__button--' + (button.main ? 'main' : 'secondary')" @click="
          button.onClick();
        useClosePopup();
        " v-for="button in options.buttons" :key="button.title">
          {{ button.title }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
@import "../../sass/abstracts/_mixins";

.popup__bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

.popup__container {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 95vw;
  max-width: 400px;
  background-color: var(--background-color);
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  z-index: 9999;
  padding: var(--spacer-md) 0 var(--spacer-md) 0;
  border-radius: var(--border-radius-md);
}

.popup__title {
  @include font-h1(var(--text-color), center);
}

.popup__body {
  font-size: 1.8rem;
  margin-top: var(--spacer-sm);
  margin-bottom: var(--spacer-sm);
  margin-left: var(--spacer-sm);
  margin-right: var(--spacer-sm);
  text-align: center;
  color: var(--text-color);
}

.popup__button--main {
  background-color: var(--accent-color);
  border-radius: var(--border-radius-md);
  height: 3.4rem;
  width: 15.8rem;
  border: none;
  margin: var(--spacer-xsm);
  color: var(--text-secondary-color);
}

.popup__button--secondary {
  background-color: var(--background-color);
  color: var(--accent-color);
  border-radius: var(--border-radius-md);
  border: 3px solid var(--accent-color);
  height: 3.4rem;
  width: 15.8rem;
  margin: var(--spacer-xsm);
}
</style>