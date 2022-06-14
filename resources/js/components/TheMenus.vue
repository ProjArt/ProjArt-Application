<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import TheEmptyPage from "./TheEmptyPage";

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

const date = computed(() => {
  return new Date().toLocaleDateString("fr-CH");
});
</script>

<template>
  <div class="page__title">
    <span>Cafeteria</span><span>{{ date }}</span>
  </div>

  <the-empty-page
    v-if="menus.length == 0"
    image="/images/no_meal.svg"
    text="Il n'y a pas de menus disponibles pour le moment, revenez à 11h."
  >
  </the-empty-page>

  <template v-else>
    <div class="menus__group">
      <div v-for="(menu,index) in menus" :key="menu.id" class="menu__menu">
        <div class="page__subtitle">
          <div class="page__subtitle--main">
            Menu {{ index + 1 }}
          </div>
        </div>
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
  </template>
</template>

<style scoped>
.page__title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-right: var(--default-padding);
}
.menu__menu {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-bottom: var(--spacer-xxsm);
  font-size: 1.2rem;
}

.menu__group {
  width: 90%;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: var(--default-padding);
  margin-bottom: var(--spacer-sm);
  border-radius: var(--border-radius-md);
  background-color: var(--information-color);
}

.menu__title-item {
  color: var(--text-secondary-color);
  background-color: var(--primary-color);
  padding: calc(2 * var(--default-padding));
  border-radius: var(--border-radius-md);
  margin-right: var(--default-padding);
  min-width: 20vw;
  font-size: 2rem;
}

.menu__description-item {
  text-align: right;
  font-size: 1.4rem;
}
</style>