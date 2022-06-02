import { user } from "../stores/auth.js"


//Crée une balise style qui définit des nouvelles valeurs aux variables de couleur
export function changeCssColorsVariable() {
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