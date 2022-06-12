<script setup>
import { user } from "../stores/auth";
import TheThemeManager from "./TheThemeManager.vue";
import useLogout from "../composables/useLogout.js";
import { usePopup } from "../composables/usePopup";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import { useLoading } from "../composables/useLoading";
import { ref } from "vue";

const canUploadDatas = ref(true);

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

function updateGaps() {
  usePopup({
    title: "Mise à jour des données",
    body: "Souhaitez-vous vraiment mettre à jour les données de l’école ?",
    buttons: [
      {
        title: "Non",
        onClick: () => {},
        main: false,
      },
      {
        title: "Oui",
        main: true,
        onClick: async () => {
          canUploadDatas.value = false;

          await useFetch({
            url: API.updateAllGaps.path(),
            method: API.updateAllGaps.method,
          });
          console.log("loaded");
          usePopup({
            title: "Mise à jour des données",
            body: "Mise à jour des données terminée",
          });
          canUploadDatas.value = true;
        },
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
        <button
          class="button--main"
          @click="canUploadDatas ? updateGaps() : null"
          v-if="canUploadDatas"
        >
          Mettre à jour
        </button>
        <div v-else>
          Nous mettons à jour vos données. Vous pouvez continuer à utiliser
          l'application.
        </div>
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
  bottom: 20vh;
  left: 50%;
  transform: translateX(-50%);
}

.settings-button {
  display: flex;
  justify-content: center;
  text-align: center;
  font-size: 1.4rem;
}
</style>