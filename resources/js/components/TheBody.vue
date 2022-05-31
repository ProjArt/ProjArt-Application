<script setup>
import { computed } from 'vue';
import { sideMenuWidth } from '../stores/sideMenu.js';
import TheThemeManager from "./TheThemeManager.vue";

const bodyWidth = computed(() => {
    return 'calc(100vw - ' + sideMenuWidth.value;
});

</script>

<template>
    <div class="main-body">
        <the-theme-manager></the-theme-manager>
        <router-view />
    </div>
</template>

<style scoped>
.main-body {
    display: flex;
    flex-direction: column;
    background-color: white;
    height: 100vh;
    margin-left: v-bind(sideMenuWidth);
    width: v-bind(bodyWidth);
}
</style>