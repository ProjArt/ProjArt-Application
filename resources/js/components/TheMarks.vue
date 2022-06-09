<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import TheSelect from "./TheSelect";

// At start of component, fetch the data
async function setupMarks() {
  const response = await useFetch({
    url: API.getMarks.path(),
    method: API.getMarks.method,
  });
  if (response.success === true) {
    console.log("Marks fetched", response.data);
    modules.value = response.data;
    selectedYear.value = response.data[0].years;
  } else {
    console.log(response, "error");
  }
}

setupMarks();

const modules = ref([]);
const years = computed(() => {
  return new Set(modules.value.map((module) => module.years));
});
const selectedYear = ref("");
const selectedModules = computed(() => {
  return modules.value.filter((module) => module.years == selectedYear.value);
});

function changeDate(event) {
  console.log("Changing date to", event.target.value);
  selectedYear.value = event.target.value;
}
</script>

<template>
  <div class="marks__header">
    <div class="page__title">Notes</div>
    <the-select></the-select>

    <!-- <div class="page__select">
      <select @change="changeDate($event)">
        <option v-for="year in years" :key="year.id">{{ year }}</option>
      </select>
    </div> -->
  </div>

  <!-- {{ marks }} -->
  <div v-for="module in selectedModules" :key="module.id">
    <div class="module__group">
      <div class="module__title">
        {{ module.code }}
      </div>
      <div class="year-group">
        <div v-for="mark in module.marks" :key="mark.id">
          <div class="mark__group">
            <div class="mark__title">
              <div class="mark__module_code">
                {{ mark.course_code }}
              </div>
              <div class="mark__value">
                {{ mark.value }}
              </div>
            </div>
            <div class="module__description">
              <div
                v-for="detail in mark.details"
                :key="detail.id"
                class="mark__detail_item"
              >
                <div class="mark__detail_title">
                  {{ detail.title }}
                </div>
                <div class="mark__detail_weight">
                  {{ detail.weight }}
                </div>
                <div class="mark__detail_value">
                  {{ detail.value }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
@import "../../sass/components/_page.scss";
.module__title {
  @extend .page__subtitle;
  margin-right: var(--default-padding);
}
.marks__header {
  display: flex;
  justify-content: space-between;
}
.mark__group {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  margin-bottom: 2px;
  border-radius: var(--border-radius-md);
  background-color: var(--information-color);
  margin: var(--default-padding);
}

.mark__title {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-start;
  margin-right: 1rem;
  min-width: 40vw;
}

.mark__module_code {
  color: white;
  background-color: var(--primary-color);
  padding: calc(2 * var(--default-padding));
  border-radius: var(--border-radius-md);
  font-size: 2rem;
}

.mark__value {
  font-weight: 500;
  font-size: 2rem;
  padding: var(--spacer-xsm);
}

.module__description {
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-start;
  margin-right: 1rem;
}

.mark__detail_item {
  width: 100%;
  display: grid;
  grid-template-columns: 50% 30% 20%;

  align-items: center;
  padding: 0.5rem;
  margin-bottom: 2px;
  font-size: 1.2rem;
}

.mark__detail_item:not(:last-child) {
  border-bottom: 1px solid var(--text-color);
}

.mark__detail_value {
  display: flex;
  align-items: flex-end;
  justify-content: flex-end;
}
</style>