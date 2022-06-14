<script setup >
import { ref, computed, toRaw, watch, watchEffect } from "vue";
import useFetch from "../composables/useFetch";
import { API } from "../stores/api";
import TheEmptyPage from "./TheEmptyPage";
import { theme } from "../stores/preferences";

// At start of component, fetch the data
async function setupMails() {
  console.log("fetching mails");
  const response = await useFetch({
    url: API.getMails.path(),
    method: API.getMails.method,
  });
  console.log("fetched mails");
  if (response.success === true) {
    console.log("Mails fetched", response.data.reverse());
    mails.value = response.data;
  } else {
    console.log(response, "error");
  }
  console.log("done");
}

setupMails();

const mails = ref([]);
</script>

<template>
  <div class="page__title">Mails</div>

  <the-empty-page
    v-if="mails.length == 0"
    :image="'/images/logo_REDY_'+ (theme.name == 'light' ? 'dark' : 'light')+'.svg'"
    text="Vous n'avez pas de mails"
  >
  </the-empty-page>
  <template v-else>
    <div v-for="mail in mails" :key="mail.uid">
      <router-link :to="'/mails/' + mail.uid" class="menu__item-link">
        <div
          class="mail-group"
          :class="mail.seen ? '' : 'mail-group--not-seen'"
        >
          <div class="mail-content">
            <div class="mail__from">{{ mail.from.split("<")[0] }}</div>
          </div>
          <div class="mail__content-group">
            <div class="mail__content--message">
              <div class="mail__subject">{{ mail.subject }}</div>
              <div class="mail__date">
                {{ new Date(Date.parse(mail.date)).toLocaleString("ch-FR") }}
              </div>
            </div>
            <span class="material-icons">arrow_right</span>
          </div>
        </div>
      </router-link>
    </div>
    
  </template>
  <router-link :to="'/mails/send'" class="mail__send-button"
      >Envoyer</router-link
    >
</template>

<style scoped>
.mail-group--not-seen {
  border: 1px solid var(--secondary-color);
}
.mail-group--not-seen .material-icons {
  color: var(--secondary-color);
}
.mail-group {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  padding: var(--spacer-xxsm);
  font-size: 1.2rem;
  background-color: var(--information-color);
  margin: var(--spacer-sm) var(--default-padding) var(--spacer-sm)
    var(--default-padding);
  border-radius: var(--border-radius-md);
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

.mail__from {
  background-color: var(--primary-color);
  color: var(--text-secondary-color);
  padding: var(--spacer-xsm);
  border-radius: var(--border-radius-md);
  font-size: 1.2rem;

  width: 30vw;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mail__content-group {
  display: flex;
  flex-direction: row;
  align-items: center;
}
.mail__content--message {
  text-align: right;
}

.mail__send-button {
  position: fixed;
  right: var(--spacer-sm);
  bottom: var(--spacer-lg);
  background-color: var(--accent-color);
  padding: var(--spacer-sm);
  border-radius: var(--border-radius-md);
  color: var(--text-secondary-color);
  text-decoration: none;
  font-size: 1.4rem;
}
</style>