<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";

// At start of component, fetch the data

const mailID = window.location.href.substring(
  window.location.href.lastIndexOf("/") + 1
);
console.log(mailID);
async function setupMail() {
  const response = await useFetch({
    url: API.showMail.path(mailID),
    method: API.showMail.method,
  });
  if (response.success === true) {
    console.log("Mail fetched", response.data);
    mail.value = response.data;
  } else {
    console.log(response, "error");
  }
}

setupMail();

const mail = ref();
</script>

<template>
  <div>
    <h1 class="title">Mail</h1>
    <div class="mail-group" v-if="mail != undefined">
      <div clas="mail-from">
        {{ mail.fromName }}
      </div>
      <div class="mail-date">
        {{ mail.date }}
      </div>
      <div class="subject">
        {{ mail.subject }}
      </div>
      <hr />
      <div
        class="mail-message"
        v-html="
          mail.textPlain
            .replace(/<\/?[^>]+(>|$)/g, '')
            .replace(/(?:\r\n|\r|\n)/g, '<br />')
        "
      ></div>
    </div>
  </div>
</template>

<style scoped>
.mail-group {
  display: flex;
  flex-direction: column;
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