<script setup >
import { ref, toRaw } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import { routesNames } from "../router/routes";
import { user } from "../stores/auth";
import { registerToChannelNotification } from "../stores/notifications";
import { theme } from "../stores/preferences";
import router from "../router/routes";
import { usePopup } from "../composables/usePopup";
import TheLogo from "./TheLogo.vue";

const isSubmitted = ref(false);
const formData = ref({});
const errorMessage = ref("");

const submitHandler = async () => {
  if (localStorage.getItem("hasAcceptedDatas") != "true") {
    usePopup({
      title: "Acceptez-vous ?",
      body: "Pour le bon fonctionnnement de l’application RedY nous devons accéder et télécharger vos données Gaps sur le serveur de l'école. <br> <br>Etes-vous d’accord ? <br><br> (Le téléchargement va prendre environ 2 minutes, soyez patients et actualiser la page pour obtenir les données.)",
      buttons: [
        {
          title: "Non",
          onClick: () => { },
          main: false,
        },
        {
          title: "Oui",
          onClick: async () => {
            localStorage.setItem("hasAcceptedDatas", "true");
            await proceedLogin();
          },
          main: true,
        },
      ],
    });
  } else {
    await proceedLogin();
  }
};

async function proceedLogin() {
  isSubmitted.value = true;
  formData.value.username = formData.value.username.replace(/\@.*$/g, "").toLowerCase();
  const response = await useFetch({
    url: API.login.path(),
    method: API.login.method,
    data: toRaw(formData.value),
  });
  if (response.success === true) {
    /*     await registerToChannelNotification(response.data.user.username);
     */
    localStorage.setItem("token", response.data.access_token);
    user.value = response.data.user;
    theme.value = response.data.user.theme;
    errorMessage.value = "";

    let route = routesNames().find((e) => e.name == "calendar");
    router.push(route.path);
  } else {
    errorMessage.value = response.message;
  }
}
</script>
<template>
  <div class="wrapper login">
    <div class="login__logo">
      <the-logo/>
    </div>
    <FormKit type="form" v-model="formData" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Connexion"
      @submit="submitHandler">
      <h2 class="login__title">Connexion</h2>
      <FormKit type="text" name="username" placeholder="prenom.nom" validation="required"
        label="Nom d'utilisateur GAPS" />
      <FormKit type="password" name="password" placeholder="Mot de passe GAPS" validation="required"
        label="Mot de passe GAPS" />
      <button type="submit" class="button button--main">Connexion</button>
    </FormKit>
    <div>
      <p>{{ errorMessage }}</p>
    </div>
    <!--  <a class="forgot-password" href="#">Forgot password ?</a> -->
  </div>
</template>
<style scoped lang="scss">
@import "../../sass/abstracts/mixins.scss";

.wrapper {
  width: 100vw;
  height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: var(--background-color);
}

:deep(.formkit-form) {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 400px;
}

.login__title {
  @include font-h1(var(--text-color), center);
  margin: 3rem 0;
}

:deep(.formkit-input[type="submit"]) {
  display: none;
}

:deep(.formkit-inner) {
  margin: 1rem 0 3rem 0;
}

:deep(.formkit-label) {
  @include font-title-subject(var(--text-color), left);
}


:deep(.formkit-input),
.button {
  width: 100%;
  max-width: 400px !important;
}

.button {
  padding: 0;
  margin: 1rem 0;
}

.login__logo {
  margin-top: 10rem;
  width: 40vw;
  max-width: 200px;
}

input {
  margin: 0;
}
</style>