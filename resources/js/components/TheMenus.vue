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
  <div class="page__title">Cafeteria</div>
  <div class="menus__group">
    <div v-for="day in Object.keys(menus)" :key="day.id">
      <div class="menu__item">
        <div class="menu__title">
          {{ day }}
        </div>
        <div v-for="menu in menus[day]" :key="menu.id" class="menu__menu">
          <div class="menu__group">
            <div class="menu__title-item">Entrée</div>
            <div class="menu__description-item">
              {{ menu.entry }}
            </div>
          </div>
          <div class="menu__group">
            <div class="menu__title-item">Plat</div>
            <div class="menu__description-item">
              {{ menu.plate }}
            </div>
          </div>
          <div class="menu__group">
            <div class="menu__title-item">Dessert</div>
            <div class="menu__description-item">
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
.menu__menu {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-bottom: 2rem;
}
.menu__group {
  width: 90%;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: var(--default-padding);
  margin-bottom: var(--default-padding);
  border-radius: var(--border-radius-md);
  background-color: var(--information-color);
}

.menu__title-item {
  color: white;
  background-color: var(--primary-color);
  padding: var(--default-padding);
  border-radius: var(--border-radius-md);
  margin-right: var(--default-padding);
  min-width: 5rem;
}

.menu__description-item {
  text-align: right;
}
</style>