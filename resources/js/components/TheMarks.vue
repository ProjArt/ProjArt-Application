<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";

// At start of component, fetch the data
async function setupMarks() {
  const response = await useFetch({
    url: API.getMarks.path(),
    method: API.getMarks.method,
  });
  if (response.success === true) {
    console.log("Marks fetched", response.data);
    modules.value = response.data;
  } else {
    console.log(response, "error");
  }
}

setupMarks();

const modules = ref([]);
</script>

<template>
  <!-- {{ marks }} -->
  <div v-for="module in modules" :key="module.id">
    <div class="module-group">
      {{ module.code }}
      <div class="year-group">
        <div v-for="mark in module.marks" :key="mark.id">
          <div class="mark-group">
            <div class="module_code">
              {{ mark.course_code }}
            </div>
            <div class="value">
              {{ mark.value }}
            </div>
            <br />
            <br />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.mark-group {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  border: 1px solid black;
  margin-bottom: 2px;
}
</style>