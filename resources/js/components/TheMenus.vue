<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";

// At start of component, fetch the data
async function setupMenus() {
  const response = await useFetch({
    url: API.getCafeteriaMenu.path(),
    method: API.getCafeteriaMenu.method,
  });
  if (response.success === true) {
    console.log("Menus fetched", response.data);
    menus.value = response.data;
  } else {
    console.log(response, "error");
  }
}

setupMenus();

const menus = ref([]);
</script>

<template>
  <div class="menus-group">
    <div v-for="day in Object.keys(menus)" :key="day.id">
      <div class="day-group">
        <div class="day-title">
          {{ day }}
        </div>
        <div v-for="menu in menus[day]" :key="menu.id">
          <div class="menu-group">
            <div class="entry">
              {{ menu.entry }}
            </div>
            <div class="plate">
              {{ menu.plate }}
            </div>
            <div class="dessert">
              {{ menu.dessert }}
            </div>
          </div>
        </div>
      </div>
      <br />
      <br />
    </div>
  </div>
</template>

<style scoped>
.menu-group {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  border: 1px solid black;
  margin-bottom: 2px;
}
</style>