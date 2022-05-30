<script setup>
import useLogout from "../composables/useLogout";
import { routesNames } from "../router/routes";
import { isAuthenticated } from "../stores/auth";

console.log(isAuthenticated.value);
console.log("routesNames", routesNames);
</script>
<template>
  <header class="header">
    <div class="header__links">
      <button @click="useLogout()">Déconnexion</button>
    </div>
    <div class="menu" v-if="isAuthenticated">
      <div
        v-for="routeName in Object.keys(routesNames)"
        :key="routeName"
        class="menu__item"
      >
        <router-link :to="routesNames[routeName]">{{ routeName }}</router-link>
      </div>
    </div>
  </header>
</template> 
<style scoped lang="scss">
.header {
  background-color: lightgrey;
  width: 100%;
  height: 100px;
}

.menu {
  display: flex;
  flex-direction: row;
  justify-content: space-around;
  align-items: center;
  width: 100%;
  height: 100%;
}
</style>