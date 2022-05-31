<script setup>
import { user } from "../stores/auth";
</script>

<template>
  <div class="settings-group">SETTINGS</div>
  <div class="user">{{ user }}</div>
</template>

<style scoped>
</style>