
<script setup>
const props = defineProps(["image", "text"]);
</script>

<template >
  <div class="empty__page">
    <img v-if="props.image" :src="props.image" class="empty__image" />
    <p class="empty__text">{{ props.text }}</p>
  </div>
</template>

<style scoped>
.empty__page {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.empty__image {
  width: 100px;
  height: 100px;
  margin-bottom: var(--default-padding);
  position: absolute;
  top: 50vh;
  width: 100%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.empty__text {
  position: absolute;
  top: 60vh;
  width: 80vw;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 1.4rem;
  color: var(--text-color);
}
</style>