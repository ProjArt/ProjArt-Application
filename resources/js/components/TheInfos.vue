<script setup>
</script>

<template>
  <div class="page__title">Infos</div>
  <div class="page__subtitle">
    <div class="page__subtitle--main">Les batiments</div>
  </div>
  <div class="map">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d16797.39349076457!2d6.6537570461605835!3d46.77683788204336!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x478dcf8f1bcdc8fd%3A0x52d7df3a0d79792e!2sHEIG-VD!5e0!3m2!1sfr!2sch!4v1653987173030!5m2!1sfr!2sch"
      style="border: 0"
      width="100%"
      height="200px"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
    ></iframe>
  </div>

  <div class="page__subtitle">
    <div class="page__subtitle--main">Section ingénierie des médias</div>
  </div>

  <div class="section">
    <div class="comem-title">Comem</div>
    <div class="infos-title">CONTACT ADMINISTRATIF</div>
    <div class="infos-content">Secrétariat de département</div>
    <div class="infos-contact">Mme Daniela Oberlojer</div>
    <div class="infos-buttons">
      <a href="tel:0041245577604" class="button--secondary">Téléphoner</a>
      <a href="mailto:daniela.oberlojer@heig-vd.ch" class="button--main"
        >Envoyer un mail</a
      >
    </div>
  </div>
</template>

<style scoped>
.map {
  margin-top: 3rem;
  margin-bottom: 3rem;
  margin-left: var(--default-padding);
  margin-right: var(--default-padding);
}

.section {
  font-size: 1.4rem;
  padding: var(--default-padding);
}

.infos-buttons {
  margin-top: var(--spacer-sm);
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.infos-buttons a {
  text-decoration: none;
}

.button--main {
  background-color: var(--accent-color);
  border-radius: var(--border-radius-md);
  height: 3.4rem;
  width: 15.8rem;
  border: none;
  color: var(--text-secondary-color);
  padding: var(--spacer-xxsm);
  border: 3px solid var(--accent-color);
  display: flex;
  justify-content: center;
  align-items: center;
}

.button--secondary {
  background-color: var(--background-color);
  color: var(--accent-color);
  border-radius: var(--border-radius-md);
  border: 3px solid var(--accent-color);
  height: 3.4rem;
  width: 15.8rem;
  padding: var(--spacer-xxsm);
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>