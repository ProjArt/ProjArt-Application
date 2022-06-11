<script setup >
</script>

<template>
  <div class="page__title">Crédits</div>

  <div class="credits__group">
    Cette application a été créé dans le cadre du Projet d’articulation
    (ProjArt) de la Haute École d’Ingénierie et Gestion du Canton de Vaud
    (HEIG-VD) en 2022. L’application RedY a été réalisée par les étudiants de la
    classe M49-1 de la filière Ingénierie des Médias :

    <div class="credits__details">
      <div class="credits__details--col">
        <li>Antonini Dario</li>

        <li>Bongni Eva</li>
        <li>Dione Timothée</li>
        <li>Finkel Benaiah</li>
      </div>
      <div class="credits__details--col">
        <li>Picciola Sandro</li>
        <li>Revaclier Laure-Hélène</li>
        <li>Tarrit Vincent</li>
        <li>Traber Sébastien</li>
      </div>
    </div>
  </div>

  <div class="credits__group">
    Afin de réaliser ce projet, nous avons utilisé plusieurs librairies de
    paquets open-source :
    <ul class="credits__packages-list">
      <li>defstudio - telegraph</li>
      <li>doctrine - dbal</li>
      <li>guzzlehttp - guzzle</li>
      <li>laravel-notification-channels - pusher-push-notifications</li>
      <li>laravel - framework</li>
      <li>laravel - sanctum</li>
      <li>laravel - tinker</li>
      <li>laravel - ui</li>
      <li>php-imap - php-imap</li>
      <li>pusher - pusher-php-server</li>
      <li>staudenmeir - eloquent-has-many-deep</li>
      <li>voku - simple_html_dom</li>
      <li>webklex - laravel-imap</li>
      <li>fakerphp - faker</li>
      <li>kevincobain2000 - laravel-erd</li>
      <li>knuckleswtf - scribe</li>
      <li>laravel - sail</li>
      <li>mockery - mockery</li>
      <li>nunomaduro - collision</li>
      <li>phpunit - phpunit</li>
      <li>spatie - laravel-ignition</li>
    </ul>

    <ul class="credits__packages_list">
      <li>babel/plugin-transform-runtime</li>
      <li>popperjs/core</li>
      <li>types/jest</li>
      <li>vue/compiler-sfc</li>
      <li>vue/test-utils</li>
      <li>vue/vue3-jest</li>
      <li>axio</li>
      <li>babel-jest</li>
      <li>jest</li>
      <li>laravel-mix</li>
      <li>lodash</li>
      <li>postcss</li>
      <li>resolve-url-loader</li>
      <li>sass</li>
      <li>sass-loader</li>
      <li>ts-jest</li>
      <li>vue</li>
      <li>vue-loader</li>
      <li>vue-template-compiler</li>
      <li>webpack</li>
      <li>formkit/vue</li>
      <li>autoprefixer</li>
      <li>normalize.css</li>
      <li>vue-router</li>
    </ul>
  </div>
</template>

<style scoped>
.credits__group {
  background-color: var(--information-color);
  margin: var(--default-padding);
  padding: var(--spacer-sm) var(--spacer-sm) var(--spacer-sm) var(--spacer-sm);

  border-radius: var(--border-radius-md);
  margin-bottom: var(--spacer-sm);
}
.credits__details {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  background-color: var(--primary-color);
  color: var(--text-secondary-color);
  border-radius: var(--border-radius-md);
  padding: var(--spacer-sm);
  margin-top: var(--spacer-sm);
}
</style>