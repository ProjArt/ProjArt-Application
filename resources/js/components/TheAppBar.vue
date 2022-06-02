<script setup>
</script>

<template>
  <div class="app-bar">
    <span class="material-icons">menu</span>
    <img class="icon" src="/images/logo_REDY.svg" />
    <span class="material-icons">search</span>
  </div>
</template>

<style scoped>
.app-bar {
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  height: var(--app-bar-height);
  background-color: var(--app-bar-bg-color);
  padding: var(--default-pading);
  z-index: 1000;
}

.icon {
  width: 50px;
}
</style>