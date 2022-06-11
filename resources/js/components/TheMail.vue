<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import router from "../router/routes";

// At start of component, fetch the data

const mail = ref();

const mailID = window.location.href.substring(
  window.location.href.lastIndexOf("/") + 1
);
console.log(mailID);
async function setupMail() {
  mail.value = undefined;
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
</script>

<template>
  <div class="wrapper">
    <h2 class="sendmail__title">
      <button @click="router.back()" class="bouton-back">
        <span class="material-symbols-outlined"> arrow_back_ios </span>
      </button>
      <div class="sendmail__title-text">Mail</div>
    </h2>
    <div class="mail-group" v-if="mail != undefined">
      <div class="mail__header">
        <div clas="mail-from">
          {{ mail.fromName }}
        </div>
        <div class="mail-date">
          {{ mail.date }}
        </div>
      </div>
      <div class="mail__subject">
        {{ mail.subject }}
      </div>
      <div class="hr"></div>
      <div
        class="mail-message"
        v-html="
          mail.textPlain != null
            ? mail.textPlain
                .replace(/<\/?[^>]+(>|$)/g, '')
                .replace(/(?:\r\n|\r|\n)/g, '<br />')
            : mail.textHtml.replace('<script>', '').replace('</script>', '')
        "
      ></div>
    </div>
  </div>
</template>

<style scoped lang="scss">
@import "../../sass/abstracts/mixins";

.wrapper {
  padding: var(--default-padding);
}

.mail__header {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
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
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  border-bottom: 1px solid #e0e0e0;
  font-size: 1.2rem;
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

.hr {
  width: 100%;
  border-bottom: 1px solid var(--secondary-color);
  margin-top: var(--spacer-sm);
  margin-bottom: var(--spacer-sm);
}

.mail__subject {
  font-weight: 600;
  font-size: 1.4rem;
  margin-top: var(--spacer-sm);
}
</style>