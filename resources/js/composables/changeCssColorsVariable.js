import { user } from "../stores/auth.js"


//Crée une balise style qui définit des nouvelles valeurs aux variables de couleur
export function changeCssColorsVariable() {
    if (user.value == null) return;
    let styleForThemeNode = document.querySelector("style.themeColorsUpdater");
    if (styleForThemeNode == null) {
        styleForThemeNode = document.createElement("style");
        styleForThemeNode.classList.add("themeColorsUpdater");
        document.head.appendChild(styleForThemeNode);
    }

    styleForThemeNode.textContent = `
    :root{
    --rgb-primary-color: ${user.value.theme.primary.value};
    --rgb-secondary-color: ${user.value.theme.secondary.value};
    --rgb-information-color: ${user.value.theme.information.value};
    --rgb-background-color: ${user.value.theme.background.value};
    --rgb-text-color: ${user.value.theme.text.value};
    --rgb-text-color-secondary: ${user.value.theme.text_secondary.value};
    --rgb-accent-color: ${user.value.theme.accent.value};
    --rgb-inactive-color: ${user.value.theme.inactive.value};
    --rgb-border-color: ${user.value.theme.border.value};
    }
    `;
}