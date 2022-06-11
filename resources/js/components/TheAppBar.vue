<script setup>
import { user } from "../stores/auth";
import { theme } from "../stores/preferences";

const emits = defineEmits(["open-drawer"]);

function openDrawer() {
  emits("open-drawer");
}
</script>

<template>
  <div class="app-bar">
    <span @click="openDrawer()" class="material-icons">menu</span>
    <div class="icon">
      <svg
        version="1.1"
        id="Calque_1"
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        x="0px"
        y="0px"
        viewBox="0 0 394 272"
        style="enable-background: new 0 0 394 272"
        xml:space="preserve"
      >
        <g>
          <polygon
            class="st0"
            points="245.5,241.5 33.6,241.5 33.6,29.7 245.5,29.7 245.5,86.1 218.5,86.1 218.5,56.7 60.6,56.7 60.6,214.5 
		218.5,214.5 218.5,185.2 245.5,185.2 	"
          />
        </g>
        <g>
          <path
            class="st1"
            d="M156.7,169.9l-12.4-23.7h-0.1v23.7h-20.7v-66h30.8c5.3,0,9.9,0.9,13.7,2.8c3.8,1.9,6.6,4.5,8.5,7.7
		c1.9,3.3,2.8,6.9,2.8,11c0,4.4-1.2,8.3-3.7,11.7c-2.5,3.4-6,5.9-10.6,7.4l14.4,25.3H156.7z M144.2,132.4h8.2c2,0,3.5-0.5,4.5-1.4
		c1-0.9,1.5-2.3,1.5-4.2c0-1.7-0.5-3.1-1.5-4.1c-1-1-2.5-1.5-4.4-1.5h-8.2V132.4z"
          />
          <path
            class="st1"
            d="M206.7,120.4v8.1h20.5V144h-20.5v9.3H230v16.5h-44v-66h44v16.5H206.7z"
          />
          <path
            class="st1"
            d="M283.4,108.1c5.2,2.8,9.2,6.7,12,11.7c2.8,5,4.2,10.6,4.2,17c0,6.3-1.4,11.9-4.2,17c-2.8,5-6.8,9-12,11.9
		c-5.2,2.9-11.2,4.3-18.1,4.3h-27.5v-66h27.5C272.2,103.9,278.2,105.3,283.4,108.1z M274.5,147.6c2.7-2.5,4.1-6.2,4.1-10.9
		c0-4.7-1.4-8.4-4.1-10.9c-2.7-2.5-6.4-3.8-11.1-3.8h-4.9v29.5h4.9C268.1,151.4,271.8,150.1,274.5,147.6z"
          />
          <path
            class="st2"
            d="M361,103.9l-23.7,46v20h-20.7v-20l-23.7-46h23.7l10.5,23.4l10.5-23.4H361z"
          />
        </g>
      </svg>
    </div>
    <span class="material-icons">search</span>
  </div>
</template>

<style scoped>
.st0 {
  fill: var(--secondary-color);
}
.st1 {
  fill: var(--text-color);
}
.st2 {
  fill: var(--secondary-color);
}
.icon-transparent {
  color: transparent;
}
.app-bar {
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  width: 100vw;
  height: var(--app-bar-height);
  background-color: var(--background-color);
  z-index: 100;
}

.icon {
  width: 50px;
}

.app-bar .material-icons {
  margin-left: var(--default-padding);
  margin-right: var(--default-padding);
  color: var(--accent-color);
}
</style>