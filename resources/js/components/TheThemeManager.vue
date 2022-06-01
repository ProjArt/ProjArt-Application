<script setup>
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch.js";

async function getThemes() {
  const response = await useFetch({
    url: API.getThemes.path(),
    method: API.getThemes.method,
  });
}

getThemes();
</script>


<template></template>