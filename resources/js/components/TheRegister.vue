<script setup >
import { ref, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import { routesNames } from "../router/routes";
import { user } from "../stores/auth";
import { registerToChannelNotification } from "../stores/notifications";
import { theme } from "../stores/preferences";

const isSubmitted = ref(false);
const formData = ref({});
const isAuthenticated = ref(false);
const allClasses = ref([]);
const isLoading = ref(false);
const username = ref("");
const isStudent = ref(false);

(async function getClasses() {
  const response = await useFetch({
    url: API.getClassrooms.path(),
    method: API.getClassrooms.method,
  });
  if (response.success === true) {
    const retrieveClasses = [];
    response.data.classrooms.forEach((element) => {
      retrieveClasses.push(element.name);
    });
    allClasses.value = retrieveClasses;
  } else {
    console.log(response.message);
  }
})();

const submitHandler = async () => {
  isSubmitted.value = true;
  const response = await useFetch({
    url: API.register.path(),
    method: API.register.method,
    data: toRaw(formData.value),
  });
  if (response.success === true) {
    isAuthenticated.value = true;
    localStorage.setItem("token", response.data.access_token);
    console.log("Successfully registered");
    console.log(API.updateAllGaps.path(""));

    /* isLoading.value = true;
    const r = await useFetch({
      url: API.updateAllGaps.path(),
      method: API.updateAllGaps.method,
    });
    isLoading.value = false; */

    /* await OneSignal.push(async function () {
      await OneSignal.getUserId(async function (userId) {
        console.log("OneSignal User ID:", userId);

        const r = await useFetch({
          url: API.setOnesignalUserId.path(),
          method: API.setOnesignalUserId.method,
          data: {
            onesignal_id: userId,
          },
        });
        console.log(r);
      });
    }); */

    user.value = response.data.user;
    theme.value = response.data.user.theme;

    await registerToChannelNotification(response.data.user.username);

    let route = routesNames().find((e) => e.name == "calendar");
    window.location.href += route.path.replace("/", "");
  } else {
    isAuthenticated.value = false;
  }
};

watch(username, async (value) => {
  const r = await useFetch({
    url: API.getUserRole.path(value),
    method: API.getUserRole.method,
  });
  isStudent.value = r.success && !r.data.is_teacher;
});
</script>
<template>
  <div class="wrapper">
    <FormKit
      type="form"
      v-model="formData"
      :form-class="isSubmitted ? 'hide' : 'show'"
      submit-label="Enregistrer"
      @submit="submitHandler"
    >
      <h2>Création de compte</h2>
      <FormKit
        type="text"
        name="username"
        placeholder="username"
        validation="required"
        label="UserName"
        v-model="username"
      />
      <FormKit
        type="password"
        name="password"
        placeholder="password"
        validation="required"
        label="Password"
      />
      <FormKit
        type="password"
        name="password_confirm"
        placeholder="password"
        label="Confirm password"
        validation="required|confirm"
        validation-label="Password confirmation"
      />

      <div v-if="isStudent">
        <FormKit
          type="select"
          name="classroom_name"
          placeholder="class"
          validation="required"
          label="Classe"
        >
          <option v-for="aClass in allClasses" :key="aClass" :value="aClass">
            {{ aClass }}
          </option>
        </FormKit>
      </div>
    </FormKit>
    <div>
      <h2 v-if="isAuthenticated && isSubmitted">Compte crée</h2>
      <h2 v-else-if="!isAuthenticated && isSubmitted">
        Echec de la création de compte
      </h2>
    </div>

    <div v-if="isLoading">
      Téléchargement de toutes les données depuis Gaps. Veuillez patienter
      tranquillement.
    </div>
  </div>
</template>
<style scoped lang="scss">
.wrapper {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

:deep(.formkit-form) {
  display: flex;
  flex-direction: column;
  align-items: center;
}
</style>