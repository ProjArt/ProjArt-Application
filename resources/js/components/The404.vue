<script setup>
console.log("notFound");
</script>

<template>
    <div class="main-body">
        <router-view />
        <div class="quatre-zero-quatre-wrapper">
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
            <img class="anchor-back-to-home" src="/storage/images/logo.png" width="27.4%" style="position:relative">
            </div>
    </div>
</template>

<style scoped lang="scss">
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;800&display=swap');

main{
    width:100vw;
}
 .main-body {
    background-color: var(--primary-color);
    color: var(--background-color);
    font-family: Poppins;
    height: 100vh;
    width:100vw;
} 

/* Group 43 */
.quatre-zero-quatre-wrapper{
position: relative;
width: 94.86%;
height: 32.74%;
left: 3.86%;
/*top: 7.49%;*/
}


/* ERREUR */
.erreur-quatre-zero-quatre{
position: relative;
left: 3.86%;
right: 39.13%;
top: 23.56%;
bottom: 65.92%;

margin-bottom:0px;
width:fit-content;

font-family: 'Poppins';
font-style: bold;
font-weight: 700;
font-size: 3.3em;
line-height: 5rem;
display: flex;
align-items: flex-end;
text-align: center;

color: #FFFFFF;

transform: rotate(-33.55deg);
}

/* 404 */
.quatre-zero-quatre-chiffre{
position: relative;
line-height: 5rem;
left: 35.27%;
right: 7.73%;
top: 24.96%;

margin-top:0px;
width: fit-content;
top:0;


font-family: 'Poppins';
font-style: bold;
font-weight: 700;
font-size: 5.8em;
display: flex;
align-items: flex-end;
text-align: center;

color: #FFFFFF;

transform: rotate(-31.34deg);
}

/* La page que vous recherchez est malheureusement indisponible */
.indisponible-text {
    position: relative;
    left: 22.5%;
    right: 22.5%;
    bottom: 57.47%;
    margin: 0px;
    width:55%;
    top:0%;

    font-family: "Poppins";
    font-style: 'semi-bold';
    font-weight: 600;
    font-size: 20px;
    line-height: 30px;
    display: flex;
    align-items: flex-end;
    text-align: center;

    color: #ffffff;
}


.back-to-home-link {
    color:var(--background-color);
    position:relative;
    top:8%;
    display:flex;
    align-items:center;
    justify-content:center;
    background-color: var(--accent-color);
    left: 30%;
    right:30%;
    width: 40%;
    height:6.8%;
    border-radius: 1em;
    color: var(--background-color);
    text-decoration:none;
}


.logo{
    position:relative;
    top:16%;
    display:flex;
    justify-content:center;
}

.anchor-back-to-home{
    text-decoration: none;
    color: white;
}
</style>
