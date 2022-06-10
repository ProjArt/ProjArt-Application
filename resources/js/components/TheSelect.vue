<script setup>
const emits = defineEmits(["onChange"]);

function onChange(event) {
  emits("onChange", event.target.id);
}

const props = defineProps(["options"]);
</script>

<template>
  <div class="select" tabindex="1">
    <template v-for="(a, index) in props.options" :key="a">
      <input
        class="selectopt"
        name="test"
        type="radio"
        :id="a"
        @change="onChange($event)"
        :checked="index === 0"
      />
      <label :for="a" class="option">{{ a }}</label>
    </template>
  </div>
</template>

<style scoped lang="css">
body {
  background: var(--accent-color);
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  padding: 0;
  margin: 0;
  height: 100vh;
  width: 100vw;
  font-family: sans-serif;
  color: var(--text-secondary-color);
}

.select {
  display: flex;
  flex-direction: column;
  position: relative;
  width: 100%;
  height: 40px;
  border-radius: 15px;
}

.option {
  padding: var(--default-padding);
  min-height: 40px;
  display: flex;
  align-items: center;
  background: var(--accent-color);
  color: var(--text-secondary-color);
  border-top: #222 solid 1px;
  position: absolute;
  top: 0;
  width: 100%;
  pointer-events: none;
  order: 2;
  z-index: 1;
  box-sizing: border-box;
  overflow: hidden;
  white-space: nowrap;
  border-radius: 5px;
  font-size: 1.2rem;
}

.option:hover {
  background: var(--accent-color);
}

.select:focus .option {
  position: relative;
  pointer-events: all;
}

input {
  opacity: 0;
  position: absolute;
  left: -99999px;
}

input:checked + label {
  order: 1;
  z-index: 2;
  background: var(--accent-color);
  border-top: none;
  position: relative;
}

input:not(:checked) + label {
  background-color: var(--background-color);
  border: 1px solid var(--accent-color);
  color: var(--text-color);
}

input:checked + label:after {
  content: "";
  width: 0;
  height: 0;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid white;
  position: absolute;
  right: 10px;
  top: calc(50% - 2.5px);
  pointer-events: none;
  z-index: 3;
  color: var(--text-secondary-color);
}

input:checked + label:before {
  position: absolute;
  right: 0;
  height: 40px;
  width: 40px;
  content: "";
  background: var(--accent-color);
}
</style>