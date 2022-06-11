<script setup>
import { user } from "../stores/auth";
import TheThemeManager from "./TheThemeManager.vue";
import useLogout from "../composables/useLogout.js";
import { usePopup } from "../composables/usePopup";

function disconnectAndRedirect() {
  usePopup({
    title: "Déconnexion",
    body: "Souhaitez-vous vraiment vous déconnecter de l’application RedY ?",
    buttons: [
      {
        title: "Non",
        onClick: () => {},
        main: false,
      },
      {
        title: "Oui",
        onClick: () => {
          useLogout();
        },
        main: true,
      },
    ],
  });
}
</script>

<template>
  <div class="settings__group">Réglages</div>
  <div class="settings__item">
    <div class="settings__item__content">
      <Suspense><the-theme-manager /></Suspense>
    </div>
  </div>

  <div class="settings__item">
    <div class="page__subtitle">
      <div class="page__subtitle--main">Mettre à jour les données de Gaps</div>
    </div>
    <div class="settings__item__content">
      <div class="settings-button">
        <button class="button--main">Mettre à jour</button>
      </div>
    </div>
  </div>

  <div class="disconnect">
    <form
      class="disconnectForm"
      method="post"
      @submit.prevent="disconnectAndRedirect"
      action="/register"
    >
      <input type="submit" value="Déconnexion" class="button--main" />
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

.disconnect {
  position: absolute;
  bottom: 8vh;
  left: 50%;
  transform: translateX(-50%);
}

.settings-button {
  display: flex;
  justify-content: center;
}
</style>