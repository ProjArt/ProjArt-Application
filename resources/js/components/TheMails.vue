<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";

// At start of component, fetch the data
async function setupMails() {
  const response = await useFetch({
    url: API.getMails.path(),
    method: API.getMails.method,
  });
  if (response.success === true) {
    console.log("Mails fetched", response.data.reverse());
    mails.value = response.data;
  } else {
    console.log(response, "error");
  }
}

setupMails();

const mails = ref([]);
</script>

<template>
  <h1 class="title">Mails</h1>
  <div v-for="mail in mails" :key="mail.uid">
    <router-link :to="'/mails/' + mail.uid" class="menu__item-link">
      <div class="mail-group">
        <div class="mail-content">
          <div :class="mail.seen ? 'seen' : 'not-seen'"></div>
          <div class="from">{{ mail.from }}</div>
          <div class="subject">{{ mail.subject }}</div>
          <div class="date">{{ mail.date }}</div>
        </div>
        <span class="material-icons">arrow_right</span>
      </div>
    </router-link>
  </div>
</template>

<style scoped>
.mail-group {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  border-bottom: 1px solid #e0e0e0;
}
.mail-group .seen {
  background-color: #00ff00;
}

.mail.group .not-seen {
  background-color: #ff0000;
}

.menu__item-link {
  color: inherit;
  text-decoration: inherit;
}
</style>