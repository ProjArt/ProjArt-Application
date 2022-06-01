<script setup>

</script>

<template>
    <div class="main-body">
        <router-view />
        <h1>404, la page que vous recherchez est malheureusement indisponible</h1>
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