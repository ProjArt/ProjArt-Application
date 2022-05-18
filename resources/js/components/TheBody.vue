<script setup>
    import { computed } from 'vue';
    import { sideMenuWidth } from '../stores/sideMenu.js';
     const bodyWidth =  computed(() => {
        return 'calc(100vw - '+ sideMenuWidth.value + "vw)";
    });
    const bodyMargin = computed(() => {
        return sideMenuWidth.value + "vw";
    });
</script>

<template>
    <div class="main-body">BODY</div>
</template>

<style scoped>
    .main-body {
        display: flex;
        flex-direction: column;
        background-color: #ba0606;
        height: 100vh;
        margin-left: v-bind(bodyMargin);
        width : v-bind(bodyWidth);
    }
</style>