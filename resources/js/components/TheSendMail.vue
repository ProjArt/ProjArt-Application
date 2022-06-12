<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import router from "../router/routes";

const formData = ref({});

const isSent = ref(false);

// At start of component, fetch the data
async function send() {
  isSent.value = false;
  console.log(toRaw(formData.value));
  const response = await useFetch({
    url: API.sendMail.path(),
    method: API.sendMail.method,
    data: toRaw(formData.value),
  });
  if (response.success === true) {
    console.log("Mails sent");
    isSent.value = true;
    formData.value = {};
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
      <h2 class="sendmail__title">
        <button @click="router.back()" class="bouton-back">
          <span class="material-symbols-outlined"> arrow_back_ios </span>
        </button>
        <div class="sendmail__title-text">Mail</div>
      </h2>
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
        :sections-schema="{
          input: {
            attrs: {
              rows: 20,
            },
          },
        }"
      />
      <button class="button--main" type="submit">Envoyer</button>
    </FormKit>
    <div v-if="isSent">
      <h2>Mail envoyé</h2>
    </div>
  </div>
</template>

<style scoped lang="scss">
@import "../../sass/abstracts/mixins";
.wrapper {
  margin: var(--default-padding);
}
.sendmail__title {
  display: flex;
  align-items: center;
}

.sendmail__title-text {
  @include font-h1(var(--text-color), left);
}
.bouton-back {
  all: unset;
  color: var(--accent-color);
}
.mail-group {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  padding: var(--default-padding);
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

:deep(.formkit-input) {
  width: 100%;
  background-color: var(--information-color);
  border: none;
  border-radius: var(--border-radius-md);
  min-height: 4.1rem;
}

:deep(.formkit-label) {
  font-size: 1.4rem;
  font-weight: 600;
}

:deep(.formkit-outer) {
  margin-top: var(--spacer-sm);
}

:deep(.formkit-input[type="submit"]) {
  display: none;
}

.button--main {
  position: absolute;
  bottom: var(--spacer-lg);
  right: 50%;
  transform: translateX(50%);
}

textarea {
  width: 100%;
  height: 100%;
  font-family: "Poppins", sans-serif;
}
</style>