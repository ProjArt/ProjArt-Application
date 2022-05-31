<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

console.log("user:", user.value);

async function getThemes() {
    const response = await useFetch({
        url: API.getThemes.path(),
        method: API.getThemes.method,
    });
    console.log(response.data);
    return response.data;
}

const themesList = await getThemes();
console.log(themesList);
const selectedThemeId = ref(1);


function updateUserTheme(){
let themeId = selectedThemeId.value.value;
console.log(themeId);

let newTheme = themesList.filter(theme => theme.id == themeId)[0];
console.log(newTheme);

user.theme = newTheme;
console.log("newUser", user.theme);

changeCssColorsVariable();
}


//Crée une balise style qui définit des nouvelles valeurs aux variables de couleur
function changeCssColorsVariable(){
  let styleForThemeNode = document.querySelector('style.themeColorsUpdater');

  if (styleForNodeThemeNode == null){
    styleForThemeNode = document.createElement('style')
    styleForNodeThemeNode.classList.add("themeColorsUpdater")
    document.append(styleForNodeThemeNode)
  }


  styleForNodeThemeNode.textContent = 
  `
    --primary-color: ${user.theme.primary};
    --secondary-color: ${user.theme.secondary};
  `

}
</script>

<template>
    <div class="themeSelection">
        <form class="themeSlectionForm" 
        @submit.prevent="updateUserTheme()">
            <input type="radio" name="theme" value="1" ref="selectedThemeId" data-colors="red-white">
            <label for="red-white">Red-white</label><br />
            <input type="radio" name="theme" value="2" ref="selectedThemeId" data-colors="red-white">
            <label for="black-white">Black-white</label><br />
            <input type="submit" value="sélectionner ce thème" >
        </form>
    </div>
</template>
