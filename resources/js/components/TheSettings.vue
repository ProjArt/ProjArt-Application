<script setup>
import { user } from "../stores/auth";
import TheThemeManager from "./TheThemeManager.vue";
import useLogout from "../composables/useLogout.js";
import { usePopup } from "../composables/usePopup";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import { useLoading } from "../composables/useLoading";
import { ref } from "vue";
import useToast from "../composables/useToast";

const canUploadDatas = ref(true);

function disconnectAndRedirect() {
  usePopup({
    title: "Déconnexion",
    body: "Souhaitez-vous vraiment vous déconnecter de l’application RedY ?",
    buttons: [
      {
        title: "Non",
        onClick: () => { },
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
        onClick: () => { },
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
          useToast("Mise à jour des données effectuée.", "success");
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
      <Suspense>
        <the-theme-manager />
      </Suspense>
    </div>
  </div>

  <div class="settings__item">
    <div class="wrapper settings__item__content">
      <div class="settings-button">
        <h2 class="settings__title"><span class="material-icons">upload_file</span><span>Mettre à jour les données de
            GAPS</span>
        </h2>
        <button class="button button--main" @click="canUploadDatas ? updateGaps() : null" v-if="canUploadDatas">
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
    <form class="disconnectForm" method="post" @submit.prevent="disconnectAndRedirect" action="/register">
      <button type="submit" class="button button--main">Déconnexion</button>
    </form>
  </div>
</template>

<style scoped lang="scss">
@import "../../sass/components/_page.scss";
@import "../../sass/abstracts/mixins";

.settings__group {
  @extend .page__title;
}

.wrapper {
  display: flex;
  flex-direction: column;
  margin: 2rem 1rem;
  padding: 1rem;
  border-radius: var(--border-radius-md);
  background-color: var(--information-color);
}

.settings__title {
  @include font-title-subject (var(--text-color), left);
  display: flex;
  align-items: center;
}

.disconnectForm input {
  margin: 0 auto;
}


.settings-button {
  display: flex;
  justify-content: center;
  flex-direction: column;
  text-align: center;
  font-size: 1.4rem;
}

button {
  max-width: 250px;
  margin: 0 auto;
}
</style>