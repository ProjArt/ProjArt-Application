<script setup>
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { changeCssColorsVariable } from "../composables/changeCssColorsVariable.js";
import { ref, computed, watchEffect, watch } from "vue";
import { user } from "../stores/auth.js";

async function getThemes() {
  const response = await useFetch({
    url: API.getThemes.path(),
    method: API.getThemes.method,
  });
  return response.data;
}

const themesList = await getThemes();
const initialThemeId = user.value.theme.id;
//console.log('initialUserThemeId', user.value)
const selectedThemeId = ref(initialThemeId);

function updateUserTheme() {
  let themeId = selectedThemeId.value;
  //console.log(themeId);
  let newTheme = themesList.filter((theme) => theme.id == themeId)[0];
  //console.log(newTheme);
  //user.value.theme = newTheme;

  const newUser = user.value;
  newUser.theme = newTheme;
  user.value = newUser;

  //console.log("newUserTheme", user.value.theme);
  changeCssColorsVariable();
  registerUserThemeInDb();
}

async function registerUserThemeInDb(themeId) {
  const response = await useFetch({
    url: API.setTheme.path(),
    method: API.setTheme.method,
    data: { theme_id: user.value.theme.id },
  });
  //console.log(response.data);
  return response.data;
}
</script>

<template>
  <div class="themeSelection">
    <div class="page__subtitle">
      <div class="page__subtitle--main">Selectionner un thème</div>
    </div>
    <form class="themeSlectionForm" @change="updateUserTheme()">
      <!-- Cette procédure fait en sorte que seul le tème correspondant à celui sélectionné est checké par défaut.
        Elle permet de contourner le problème lié au fait que le backend ne retourne pas le nom du thème concerné mais des
        couleurs en hexadecimal, ce qui est illisible pour un humain.       -->

      <div v-for="theme in themesList" :key="theme.id" class="theme__input">
        <input
          type="radio"
          name="theme"
          :value="theme.id"
          v-model="selectedThemeId"
          :data-colors="theme.name"
          :id="theme.id"
        />

        <label :for="theme.id">{{ theme.name }}</label>
      </div>
    </form>
  </div>
</template>

<style scoped lang="scss">
@import "../../sass/components/_page.scss";
.themeSlectionForm {
}

.themeSelection h2 {
  @extend .page__subtitle--main;
}

.theme__input {
  color: var(--text-color);
}
</style>