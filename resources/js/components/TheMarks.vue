<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import TheSelect from "./TheSelect";
import TheEmptyPage from "./TheEmptyPage";
import NoMark from "./svg/NoMark";

// At start of component, fetch the data
async function setupMarks() {
  const response = await useFetch({
    url: API.getMarks.path(),
    method: API.getMarks.method,
  });
  if (response.success === true) {
    console.log("Marks fetched", response.data);
    modules.value = response.data.sort((b, a) => a.years.localeCompare(b.years));
    selectedYear.value = response.data[0].years;
  } else {
    console.log(response, "error");
  }
}

setupMarks();

const modules = ref([]);
const years = computed(() => {
  return new Set(modules.value.map((module) => module.years).filter(year => year != ""));
});
const selectedYear = ref("");
const selectedModules = computed(() => {
  return modules.value.filter((module) => module.years == selectedYear.value).sort((a, b) => {
    if (a.mark == 0 || b.mark == 0) {
      return -1;
    } else {
      return a.name.localeCompare(b.name);
    }
  });
});

function changeDate(id) {
  console.log("Changing date to", id);
  selectedYear.value = id;
}
</script>

<template>
  <div class="marks__header">
    <div class="page__title">Notes</div>
    <div class="marks__select">
      <the-select :options="years" @onChange="(value) => changeDate(value)"></the-select>
    </div>
  </div>

  <the-empty-page v-if="selectedModules.length == 0" model="" :component="NoMark"
    text="Vous n'avez pas encore de notes">
  </the-empty-page>

  <template v-else>
    <!-- {{ marks }} -->
    <div v-for="module in selectedModules" :key="module.id">
      <div class="module__group" v-if="module.marks.length != 0">
        <div class="module__title">
          <div class="module__code">
            {{ module.code }}
          </div>
          <div class="module_mark" v-if="module.mark != 0">
            {{ module.mark }}
          </div>
        </div>
        <div class="year-group">
          <div v-for="mark in module.marks" :key="mark.id">
            <div class="mark__group">
              <div class="mark__title">
                <div class="mark__module_code">
                  {{ mark.course_code }}
                </div>
                <div class="mark_total">
                  <div class="mark__pourcentage" v-if="mark.weight_percentage != 0">
                    {{ mark.weight_percentage }}%
                  </div>
                  <div class="mark__value">
                    {{ mark.value }}
                  </div>
                </div>
              </div>
              <div class="module__description">
                <div v-for="detail in mark.details" :key="detail.id" class="mark__detail_item">
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
</template>

<style scoped lang="scss">
@import "../../sass/components/_page.scss";

.module__title {
  @extend .page__subtitle;
  margin-right: var(--default-padding);
  margin-top: var(--spacer-xsm);
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
  color: var(--text-color);
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
  color: var(--text-secondary-color);
  background-color: var(--primary-color);
  padding: calc(2 * var(--default-padding));
  border-radius: var(--border-radius-md);
  border: 1px solid var(--text-color);

  font-size: 2rem;
  width: 30vw;
}

.mark_total {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: flex-start;
  width: 100%;
}

.mark__value {
  font-weight: 500;
  font-size: 2rem;
  padding: var(--spacer-xsm);
}

.mark__pourcentage {
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
  font-size: 1.4rem;
}

.mark__detail_item:not(:last-child) {
  border-bottom: 1px solid var(--text-color);
}

.mark__detail_value {
  display: flex;
  align-items: flex-end;
  justify-content: flex-end;
}

.marks__select {
  width: 40vw;
  margin-right: var(--default-padding);
}
</style>