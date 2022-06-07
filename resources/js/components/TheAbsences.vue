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
  <div class="page__subtitle">
    <div class="page__subtitle--main">Matières</div>
    <div class="page__subtitle--secondary">Taux</div>
  </div>
  <div v-for="absence in absences" :key="absence.id">
    <div class="absence__item">
      <div class="absence__unity">
        {{ absence.unity.split(" - ")[0] }}
      </div>
      <div class="absence__absolute_rate">
        {{ absence.absolute_rate }}%
        <span class="material-symbols-outlined" v-if="absence.absolute_rate">
          warning
        </span>
      </div>
    </div>
  </div>
</template>

<style scoped class="themeColorsUpdater">
.absence__item {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: var(--default-padding);
  background-color: var(--information-color);
  border-radius: var(--border-radius-md);
}

.absence__absolute_rate {
  color: red;
}
</style>