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
    <h2>Sélectionner un thème</h2>
    <form class="themeSlectionForm" @change="updateUserTheme()">
      <!-- Cette procédure fait en sorte que seul le tème correspondant à celui sélectionné est checké par défaut.
        Elle permet de contourner le problème lié au fait que le backend ne retourne pas le nom du thème concerné mais des
        couleurs en hexadecimal, ce qui est illisible pour un humain.       -->
      <input
        v-if="initialThemeId == 1"
        checked
        type="radio"
        name="theme"
        value="1"
        v-model="selectedThemeId"
        data-colors="black-white"
      />
      <input
        v-if="initialThemeId != 1"
        type="radio"
        name="theme"
        value="1"
        v-model="selectedThemeId"
        data-colors="black-white"
      />
      <label for="red-white">Black-white</label><br />
      <input
        v-if="initialThemeId == 2"
        checked
        type="radio"
        name="theme"
        value="2"
        selected="true"
        v-model="selectedThemeId"
        data-colors="black-white"
      />
      <input
        v-if="initialThemeId != 2"
        type="radio"
        name="theme"
        value="2"
        selected="true"
        v-model="selectedThemeId"
        data-colors="black-white"
      />
      <label for="black-white">White-black</label><br />
    </form>
  </div>
</template>

<style>
.themeSlectionForm {
  background-color: var(--primary-color);
  color: var(--secondary-color);
  border-color: var(--secondary-color);
}
</style>