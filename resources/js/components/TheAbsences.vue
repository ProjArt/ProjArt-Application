<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import AbsencesRouteVue from "../router/AbsencesRoute.vue";
import { API } from "../stores/api";
import TheEmptyPage from "./TheEmptyPage";
import { usePopup } from "../composables/usePopup";
import NoAbsence from "./svg/NoAbsence";

// At start of component, fetch the data
async function setupAbsences() {
  const response = await useFetch({
    url: API.getAbsences.path(),
    method: API.getAbsences.method,
  });
  if (response.success === true) {
    absences.value = response.data;
  } else {
    console.log(response, "error");
  }
}

setupAbsences();

const absences = ref([]);

function popup() {
  usePopup({
    title: "Absences",
    body: "Ceci est le body",
    buttons: [
      {
        title: "Button 2",
        onClick: () => {
          console.log("Button 2 clicked");
        },
        main: false,
      },
      {
        title: "Button 1",
        onClick: () => {
          console.log("Button 1 clicked");
        },
        main: true,
      },
    ],
  });
}
</script>

<template>
  <div class="page__title">Absences</div>

  <the-empty-page v-if="absences.length == 0" :component="NoAbsence" text="Vous n'avez pas d'absences">
  </the-empty-page>
  <template v-else>
    <div class="page__subtitle" v-if="absences.length != 0">
      <div class="page__subtitle--main">Matières</div>
      <div class="page__subtitle--secondary">Taux</div>
    </div>
    <div v-for="absence in absences" :key="absence.id">
      <div class="absence__item">
        <div class="absence__unity">
          {{ absence.unity.split(" - ")[0] }}
        </div>
        <div class="absences__right">
          <span class="material-symbols-outlined" v-if="absence.absolute_rate > 15">
            warning
          </span>
          <div class="absence__absolute_rate">{{ absence.absolute_rate }}%</div>
        </div>
      </div>
    </div>
  </template>
</template>

<style scoped>
.absence__item {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: var(--default-padding);
  margin: var(--default-padding);
  background-color: var(--information-color);
  border-radius: var(--border-radius-md);
  color: var(--text-color);
}

.absence__absolute_rate {
  color: var(--text-secondary-color);
  background-color: var(--primary-color);
  padding: var(--default-padding);
  border-radius: var(--border-radius-md);
}

.absence__item {
  font-size: 1.2rem;
}

.absence__absolute_rate {
  font-size: 2rem;
  padding: var(--spacer-xsm);
  margin-left: var(--spacer-sm);
}

.absences__right {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
}
</style>