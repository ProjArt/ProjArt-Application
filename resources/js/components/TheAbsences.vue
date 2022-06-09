<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import AbsencesRouteVue from "../router/AbsencesRoute.vue";
import { API } from "../stores/api";

// At start of component, fetch the data
async function setupAbsences() {
  const response = await useFetch({
    url: API.getAbsences.path(),
    method: API.getAbsences.method,
  });
  if (response.success === true) {
    console.log("Absences fetched", response.data);
    absences.value = response.data;
  } else {
    console.log(response, "error");
  }
}

setupAbsences();

const absences = ref([]);
</script>

<template>
  <div class="page__title">Absences</div>
  <div class="page__subtitle" v-if="absences.length != 0">
    <div class="page__subtitle--main">Matières</div>
    <div class="page__subtitle--secondary">Taux</div>
  </div>
  <div v-for="absence in absences" :key="absence.id">
    <div class="absence__item">
      <div class="absence__unity">
        {{ absence.unity.split(" - ")[0] }}
      </div>
      <span class="material-symbols-outlined" v-if="absence.absolute_rate > 15">
        warning
      </span>
      <div class="absence__absolute_rate">{{ absence.absolute_rate }}%</div>
    </div>
  </div>

  <div class="" v-if="absences.length == 0">Tu n'as pas d'absences</div>
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
}

.absence__absolute_rate {
  color: white;
  background-color: var(--primary-color);
  padding: var(--default-padding);
  border-radius: var(--border-radius-md);
}
</style>