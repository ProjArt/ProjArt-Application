<script setup>
import { user } from "../stores/auth";
import TheThemeManager from "./TheThemeManager.vue";
import useLogout from "../composables/useLogout.js";

function disconnectAndRedirect() {
  useLogout();
}
</script>

<template>
  <div class="settings__group">Réglages</div>
  <Suspense><the-theme-manager /></Suspense>
  <br />
  <div class="page__subtitle">
    <div class="page__subtitle--main">Déconnexion</div>
  </div>
  <div class="disconnect">
    <form
      class="disconnectForm"
      method="post"
      @submit.prevent="disconnectAndRedirect"
      action="/register"
    >
      <input type="submit" value="Se déconnecter" />
    </form>
  </div>
</template>

<style scoped lang="scss">
@import "../../sass/components/_page.scss";
.settings__group {
  @extend .page__title;
}

.disconnectForm {
  padding-left: var(--default-padding);
}
</style>