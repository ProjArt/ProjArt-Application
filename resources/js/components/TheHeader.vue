<script setup>
import useLogout from '../composables/useLogout';
</script>
<template>
    <header class="header">
        <div class="header__links">
            <button @click="useLogout()">Déconnexion</button>
        </div>
    </header>
</template> 
<style scoped lang="scss">
.header {
    background-color: lightgrey;
    width: 100%;
    height: 100px;
}
</style>