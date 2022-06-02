<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

//console.log("user:", user.value);

async function getThemes() {
    const response = await useFetch({
        url: API.getThemes.path(),
        method: API.getThemes.method,
    });
    // console.log(response.data);
    return response.data;
}

const themesList = await getThemes();
//console.log(themesList);
const selectedThemeId = ref(2);

function updateUserTheme() {
    let themeId = selectedThemeId.value;
    //console.log(themeId);
    let newTheme = themesList.filter((theme) => theme.id == themeId)[0];
    //console.log(newTheme);
    user.value.theme = newTheme;
    //console.log("newUserTheme", user.value.theme);
    changeCssColorsVariable();
    registerUserThemeInDb();
}

async function registerUserThemeInDb(themeId){
    const response = await useFetch({
        url: API.setTheme.path(),
        method: API.setTheme.method,
        data: {theme_id: user.value.theme.id}
    });
    console.log(response.data);
    return response.data;
}

//Crée une balise style qui définit des nouvelles valeurs aux variables de couleur
function changeCssColorsVariable() {
    let styleForThemeNode = document.querySelector("style.themeColorsUpdater");
    if (styleForThemeNode == null) {
        styleForThemeNode = document.createElement("style");
        styleForThemeNode.classList.add("themeColorsUpdater");
        document.head.appendChild(styleForThemeNode);
    }

    styleForThemeNode.textContent = `
    :root{
    --primary-color: ${user.value.theme.primary.value};
    --secondary-color: ${user.value.theme.secondary.value};
    }
    `
    

    /*console.log(
    "primary color from themeManager:", user.value.theme.primary.value,
    "secondary color from themeManager:", user.value.theme.secondary.value,
    "selected themeID", selectedThemeId.value
    )*/
}
</script>

<template>
    <div class="themeSelection">
        <form class="themeSlectionForm" @submit.prevent="updateUserTheme()">
            <input
                type="radio"
                name="theme"
                value="1"
                v-model="selectedThemeId"
                data-colors="black-white"
            />
            <label for="red-white">Black-white</label><br />
            <input
                type="radio"
                name="theme"
                value="2"
                checked
                selected="true"
                v-model="selectedThemeId"
                data-colors="black-white"
            />
            <label for="black-white">White-black</label><br />
            <input class="submit" type="submit" value="sélectionner ce thème" />
        </form>
    </div>
</template>

<style>


    .themeSlectionForm {
    background-color:var(--primary-color);
    color:var(--secondary-color);
    border-color: var(--secondary-color);
    }
    
   div#app{
      background-color:var(--primary-color);
      color:var(--secondary-color);
      border-color: var(--secondary-color);
    }
</style>