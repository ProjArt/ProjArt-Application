<script setup>
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { changeCssColorsVariable } from "../composables/changeCssColorsVariable.js";
import { ref, toRaw, computed, watch } from "vue";
import { user } from "../stores/auth.js";
import { theme } from "../stores/preferences";

async function getThemes() {
  const response = await useFetch({
    url: API.getThemes.path(),
    method: API.getThemes.method,
  });
  return response.data;
}

const themesList = await getThemes();
const initialThemeId = user.value.theme.id;
const selectedThemeId = ref(initialThemeId);

function updateUserTheme() {
  let themeId = selectedThemeId.value;
  let newTheme = themesList.filter((theme) => theme.id == themeId)[0];

  const newUser = user.value;
  newUser.theme = newTheme;
  user.value = newUser;

  changeCssColorsVariable();
  registerUserThemeInDb();
  theme.value = newTheme;
}

async function registerUserThemeInDb(themeId) {
  const response = await useFetch({
    url: API.setTheme.path(),
    method: API.setTheme.method,
    data: { theme_id: user.value.theme.id },
  });
  return response.data;
}

</script>

<template>
  <div class="themeSelection">
    <form class="themeSlectionForm" @change="updateUserTheme()">
      <!-- Cette procédure fait en sorte que seul le tème correspondant à celui sélectionné est checké par défaut.
        Elle permet de contourner le problème lié au fait que le backend ne retourne pas le nom du thème concerné mais des
        couleurs en hexadecimal, ce qui est illisible pour un humain.       -->

      <h2 class="theme__title"><span class="material-icons">star_border</span><span>Sélectionner un thème</span> </h2>
      <div class="theme__wrapper">
        <div v-for="ctheme in themesList" :key="ctheme.id" class="theme__input">
          <input type="radio" name="theme" :value="ctheme.id" v-model="selectedThemeId"
            :data-colors="'theme ' + ctheme.name" :id="ctheme.id" />

          <label :for="ctheme.id" class="button theme__button"
            :class="selectedThemeId == ctheme.id ? 'button--main' : 'button--secondary'">{{ ctheme.name }}</label>
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped lang="scss">
@import "../../sass/components/page";
@import "../../sass/abstracts/mixins";

.themeSlectionForm {
  display: flex;
  flex-direction: column;
  margin: 2rem 1rem;
  padding: 1rem;
  border-radius: var(--border-radius-md);
  background-color: var(--information-color);
}

.theme__title {
  @include font-title-subject(var(--text-color), left);
  display: flex;
  align-items: center;
  margin: 0 0 1rem 0
}

.theme__wrapper {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;

}

.theme__input {
  color: var(--text-color);
  margin: 1rem;
}

input[type="radio"] {
  display: none;
}

.theme__button {
  border-radius: var(--border-radius-md);
  box-sizing: border-box;
  min-width: 10rem;
}

.theme__button--light {
  background-color: white;
  border: 3px solid black;
  color: black;
}

.theme__button--dark {
  background-color: black;
  border: 3px solid white;
  color: white;
  border-radius: 6px;
}

.theme__button--blue {
  background-color: #304277;
  border: 3px solid white;
  color: white;
  border-radius: 6px;
}
</style>