<script setup>
import { ref } from "vue";
import TheAppBar from ".//TheAppBar.vue";
import TheTabbar from "./TheTabbar.vue";
import TheNotification from "./TheNotification.vue";
import TheDrawer from "./TheDrawer.vue";

const drawer = ref();

function openDrawer() {
  drawer.value.toggle();
}

document.addEventListener("touchstart", handleTouchStart, false);
document.addEventListener("touchmove", handleTouchMove, false);

var xDown = null;
var yDown = null;

function getTouches(evt) {
  return (
    evt.touches || // browser API
    evt.originalEvent.touches
  ); // jQuery
}

function handleTouchStart(evt) {
  const firstTouch = getTouches(evt)[0];
  xDown = firstTouch.clientX;
  yDown = firstTouch.clientY;
}

function handleTouchMove(evt) {
  if (!xDown || !yDown) {
    return;
  }

  var xUp = evt.touches[0].clientX;
  var yUp = evt.touches[0].clientY;

  var xDiff = xDown - xUp;
  var yDiff = yDown - yUp;

  if (Math.abs(xDiff) > Math.abs(yDiff)) {
    /*most significant*/
    if (xDiff > 0) {
      /* left swipe */
      console.log("left swipe");
    } else {
      /* right swipe */
      console.log("swipe right");
      if (xUp < 50) {
        drawer.value.toggle();
      }
    }
  } else {
    if (yDiff > 0) {
      /* up swipe */
      console.log("up swipe");
    } else {
      /* down swipe */
      console.log("down swipe");
    }
  }
  /* reset values */
  xDown = null;
  yDown = null;
}
</script>


<template>
  <the-app-bar @open-drawer="openDrawer" />
  <div class="spacer-top">&nbsp;</div>
  <Suspense>
    <the-theme-manager />
  </Suspense>
  <the-notification></the-notification>
  <main>
    <router-view></router-view>
  </main>
  <div class="spacer-bottom">&nbsp;</div>
  <the-tabbar />

  <the-drawer ref="drawer" />
</template>

<style lang="scss" scoped>
main {
  width: 100%;
  overflow-y: hidden;
}

.spacer-top {
  width: 100%;
  height: 4.5rem;
}

.spacer-bottom {
  width: 100%;
  height: 5rem;
}
</style>

