<script setup>
//console.log("notFound");

//Remove top and bottom menus
function removeMenus(){
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

function removeTopWhiteSpace(){
    let body = document.querySelector('body')
    body.classList.add('top-to-remove')
    body.setAttribute("style", "margin: 0 0 0 0")
}

removeMenus()
removeTopWhiteSpace()
</script>

<template>
    <div class="main-body">
        <router-view />
        <div class="title-wrapper">
        <h2 class="erreur-quatre-zero-quatre">ERREUR</h2>
        <h1 class="quatre-zero-quatre-chiffre">404</h1>
        </div>
        <div class="indisponible-text">
            La page que vous cherchez est malheureusement indisponible
        </div>
        <div class="back-to-home-link">
            <a class="anchor-back-to-home" href="/">Retour à l'accueil</a>
        </div>
        <div class="logo">
            <img
                class="anchor-back-to-home"
                src="/storage/images/logo.png"
                width="50%"
                style="position: relative"
            />
        </div>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@800&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@600;800&display=swap");



main {
    width: 100vw;
}

.app-bar{
    display:none;
}

.menu{
    display:none !important;
}

.spacer-bottom{
    display:none;
}

.top-to-remove {
    margin: 0 0 0 0;
}

.main-body {
    display: flex;
    flex-direction: column;
    background-color: var(--primary-color);
    color: var(--background-color);
    font-family: Poppins;
    height: 100vh;
    width: 100vw;
    align-items: center;
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
    bottom: 65.92%;
    /* top: 3%; */
    margin-bottom: 0px;
    width: -webkit-fit-content;
    margin: 0 8rem 0 0;
    margin: -moz-fit-content;
    width: fit-content;
    font-family: "Poppins";
    font-style: bold;
    font-weight: 700;
    font-size: 3.3em;
    line-height: 5rem;
    display: flex;
    align-items: flex-end;
    text-align: center;
    color: #ffffff;
}

/* 404 */
.quatre-zero-quatre-chiffre {
    position: relative;
    line-height: 5rem;
    width: -webkit-fit-content;
    width: -moz-fit-content;
    margin: 0 0 0 5rem;
    width: fit-content;
    font-family: "Poppins";
    font-style: bold;
    font-weight: 700;
    font-size: 5.8em;
    display: flex;
    align-items: flex-end;
    text-align: center;
    color: #ffffff;
}

/* La page que vous recherchez est malheureusement indisponible */
.indisponible-text {
    width: 4em;
    margin: 2em auto 3em auto;
    font-style: "semi-bold";
    font-weight: 600;
    font-size: 20px;
    line-height: 30px;
    display: flex;
    align-items: flex-end;
    text-align: center;
    color: #ffffff;
    justify-content:center;
}

.back-to-home-link {
    color: var(--background-color);
    display: flex;
    margin: 0 0 2em 0;
    align-items: center;
    justify-content: center;
    background-color: var(--accent-color);
    padding: 1em 1em 1em 1em;
    border-radius: 1em;
    color: var(--background-color);
    text-decoration: none;
}

.logo {
    display: flex;
    justify-content: center;
    margin: 3em 0 0 0;
}

.anchor-back-to-home {
    text-decoration: none;
    color: white;
}

</style>
