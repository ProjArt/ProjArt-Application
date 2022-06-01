<script setup>


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
    background-color: white;
    height: 100vh;
    width: v-bind(bodyWidth);
}
</style>