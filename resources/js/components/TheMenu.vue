<script setup>
    import { sideMenuWidth } from '../stores/sideMenu.js';
    import { computed } from 'vue'; 

    const menuWidth =  computed(() => {
        return sideMenuWidth.value + "vw";
    });
</script>

<template>
    <div class="side-menu" :style="{ width : menuWidth }">MENU</div>
</template>

<style scoped>
    .side-menu {
        background-color: #b7b7b7;
        border-right: 1px solid #e5e5e5;
        height: 100vh;
        position: fixed;
        top: 0;
        z-index: 1;
    }
</style>