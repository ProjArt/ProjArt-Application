<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";

const formData = ref({});

// At start of component, fetch the data
async function send() {
  console.log(toRaw(formData.value));
  const response = await useFetch({
    url: API.sendMail.path(),
    method: API.sendMail.method,
    data: toRaw(formData.value),
  });
  if (response.success === true) {
    console.log("Mails fetched", response.data.reverse());
    mails.value = response.data;
  } else {
    console.log(response, "error");
  }
}
</script>

<template>
  <div class="wrapper">
    <FormKit
      type="form"
      v-model="formData"
      submit-label="Envoyer"
      @submit="send"
    >
      <h2>Envoyer un mail</h2>
      <FormKit
        type="text"
        name="to"
        placeholder="À"
        validation="required"
        label="À"
      />
      <FormKit
        type="text"
        name="subject"
        placeholder="Sujet"
        validation="required"
        label="Sujet"
      />
      <FormKit
        type="textarea"
        name="message"
        placeholder="Message"
        validation="required"
        label="Message"
      />
    </FormKit>
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