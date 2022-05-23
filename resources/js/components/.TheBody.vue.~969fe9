<script setup>
import { computed } from 'vue';
import { sideMenuWidth } from '../stores/sideMenu.js';
const bodyWidth = computed(() => {
    return 'calc(100vw - ' + sideMenuWidth.value;
});

</script>

<template>
    <div class="main-body">
        <router-view />
    </div>
</template>

<style scoped>
.main-body {
    display: flex;
    flex-direction: column;
    background-color: #ba0606;
    height: 100vh;
    margin-left: v-bind(sideMenuWidth);
    width: v-bind(bodyWidth);
}
</style>