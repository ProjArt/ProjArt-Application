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
  <div v-for="absence in absences" :key="absence.id">
    <div class="absence-group">
      <div class="orientation">
        {{ absence.orientation }}
      </div>
      <div class="unity">
        {{ absence.unity }}
      </div>
      <div class="absolute_rate">{{ absence.absolute_rate }}%</div>
    </div>
  </div>
</template>

<style scoped>
.absence-group {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  border: 1px solid black;
  margin-bottom: 2px;
}
</style>