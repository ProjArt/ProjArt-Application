<script setup>
import { user } from '../stores/auth.js';
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch.js";
console.log("user:", user.value);

async function getThemes() {
  const response = await useFetch({
    url: API.getThemes.path(),
    method: API.getThemes.method,
  });
};

const themesList = await getThemes();
console.log(themesList);


</script>


<template>



</template>