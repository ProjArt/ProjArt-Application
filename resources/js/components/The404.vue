<script setup>
import { is404 } from "../stores/route";
import TheLogo from "./TheLogo.vue";

document.querySelector('main').setAttribute('style', 'margin: 0 0 0 0');

//Remove top and bottom menus
function removeMenus() {
    let botMenu = document.querySelector('.menu');
    let parent = botMenu.parentNode;
    parent.removeChild(botMenu);

    let spaceMenu = document.querySelector('.spacer-bottom')
    parent = spaceMenu.parentNode
    parent.removeChild(spaceMenu)

    let topMenu = document.querySelector('.app-bar')
    parent = topMenu.parentNode
    parent.removeChild(topMenu)

    let spacerTop = document.querySelector('.spacer-top')
    parent = spacerTop.parentNode
    parent.removeChild(spacerTop)
}

function removeTopWhiteSpace() {
    let body = document.querySelector('body')
    body.classList.add('top-to-remove')
    body.setAttribute("style", "margin: 0 0 0 0")
}

</script>

<template>
    <div class="main-body">
        <router-view />
        <div class="title-wrapper-wrapper">
            <div class="title-wrapper">
                <h2 class="erreur-quatre-zero-quatre">ERREUR</h2>
                <h1 class="quatre-zero-quatre-chiffre">404</h1>
            </div>
        </div>
        <div class="indisponible-text">
            La page que vous cherchez est malheureusement indisponible
        </div>
        <div class="back-to-home-link">
            <a class="anchor-back-to-home" href="/">Retour à l'accueil</a>
        </div>
        <div class="logo">
            <the-logo class="anchor-back-to-home"></the-logo>
        </div>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@800&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@600;800&display=swap");



main {
    width: 100vw;
    margin: 0 0 0 0 !important;
}

.anchor-back-to-home :deep(svg) {
    width: 10rem;
    height: 10rem;
}

.app-bar {
    display: none;
}

.menu {
    display: none !important;
}

.spacer-bottom {
    display: none;
}

.top-to-remove {
    margin: 0 0 0 0;
}

.main-body {
    display: flex;
    flex-direction: column;
    background-color: var(--background-color);
    color: var(--text-color);
    font-family: Poppins;
    height: 100vh;
    width: 100vw;
    align-items: center;
}

.title-wrapper-wrapper {
    margin: 4.8rem 0 0 0;
}

.title-wrapper {
    /* position: absolute; */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transform: rotate(-33.55deg);
    margin: 3em 0 2em 0;
}

/* ERREUR */
.erreur-quatre-zero-quatre {
    width: -webkit-fit-content;
    margin: 0 8rem 0 0;
    margin: -moz-fit-content;
    width: -moz-fit-content;
    width: fit-content;
    font-family: "Poppins";
    font-style: normal;
    font-weight: 700;
    font-size: 6rem;
    line-height: 9rem;
    display: flex;
    align-items: flex-end;
    text-align: center;
    color: var(--text-color);
}

/* 404 */
.quatre-zero-quatre-chiffre {
    position: relative;
    width: -webkit-fit-content;
    width: -moz-fit-content;
    margin: 0 0 0 15rem;
    width: fit-content;
    font-family: "Poppins", normal;
    font-style: bold;
    font-weight: 700;
    font-size: 10rem;
    line-height: 7.5rem;
    display: flex;
    align-items: flex-end;
    text-align: center;
    color: var(--text-color);
}

/* La page que vous recherchez est malheureusement indisponible */
.indisponible-text {
    width: 4em;
    margin: 5.7rem auto 0 auto;
    font-family: Poppins, normal;
    font-style: "semi-bold";
    font-weight: 500;
    font-size: 1.8rem;
    line-height: 2.7rem;
    display: flex;
    align-items: flex-end;
    text-align: center;
    color: var(--text-color);
    justify-content: center;
}

.back-to-home-link {
    color: var(--background-color);
    display: flex;
    margin: 5.1rem 0 0 0;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    background-color: var(--accent-color);
    padding: 1em 1em 1em 1em;
    border-radius: 0.6rem;
    color: var(--background-color);
    padding: 1.4rem 2.3rem;
    text-decoration: none;
    line-height: 1rem;
}

.logo {
    display: flex;
    justify-content: center;
    margin: 8.4rem 0 0 0;
}

.anchor-back-to-home {
    text-decoration: none;
    color: var(--text-secondary-color);
}
</style>
