<script setup>
</script>

<template>
  <div class="select animated zoomIn">
    <!-- You can toggle select (disabled) -->
    <input type="radio" name="option" />
    <i class="toggle icon icon-arrow-down"></i>
    <i class="toggle icon icon-arrow-up"></i>
    <span class="placeholder">Choose...</span>
    <div class="options">
      <label class="option">
        <input type="radio" name="option" />
        <span class="title animated">Speedometer</span>
      </label>
      <label class="option">
        <input type="radio" name="option" />
        <span class="title animated">Fire</span>
      </label>
      <label class="option">
        <input type="radio" name="option" disabled />
        <span class="title animated">Handbag</span>
      </label>
      <label class="option">
        <input type="radio" name="option" />
        <span class="title animated">Badge</span>
      </label>
    </div>
  </div>
</template>

<style scoped lang="scss">
@import "https://fonts.googleapis.com/css?family=Roboto:100,300";
@import "https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.3.2/css/simple-line-icons.min.css";
@import "https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css";
@import "https://fonts.googleapis.com/css?family=Roboto+Mono:300,700";
h1 {
  text-align: center;
  color: #fff;
  font-weight: 600;
  font-size: 3em;
  letter-spacing: 0px;
  text-shadow: 1px 1px rgba(0, 0, 0, 0.02), 2px 2px rgba(0, 0, 0, 0.02),
    3px 3px rgba(0, 0, 0, 0.02), 4px 4px rgba(0, 0, 0, 0.02),
    5px 5px rgba(0, 0, 0, 0.02), 6px 6px rgba(0, 0, 0, 0.02),
    7px 7px rgba(0, 0, 0, 0.02);
}
h1 i {
  position: relative;
  font-size: 70px;
}
p {
  text-align: center;
  color: #fff;
  font-size: 14px;
  margin-bottom: 2em;
  line-height: 30px;
}
p img {
  position: relative;
  top: 8px;
  right: 10px;
}
.select {
  z-index: 500;
  position: relative;
  display: block;
  width: 40vw;
  height: 2rem;
  border-bottom: 0px;
  border-radius: 3px;
  font-size: 12px;
  box-shadow: 0px 1em 2em -1.5em rgba(0, 0, 0, 0.5);
  color: white;
  background-color: var(--accent-color);
}
.select > i.toggle {
  position: absolute;
  z-index: 4;
  right: 1.5em;
  top: 1.1rem;
  color: #fff;
}
.select .title,
.select .placeholder {
  position: relative;
  display: block;
  width: 100%;
  height: 100%;
  padding: 1rem;
  background: rgba(255, 255, 255, 1);
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  cursor: pointer;
}
.select > input {
  position: absolute;
  left: 0px;
  top: 0px;
  z-index: 1;
  width: 100%;
  height: 100%;
  display: block;
  opacity: 0;
  cursor: pointer;
}
.select > input:checked ~ i.toggle.icon-arrow-down {
  display: none;
}
.select > input:checked ~ i.toggle.icon-arrow-up {
  display: block;
}
.select > input:checked div.options label.option .title {
  display: none !important;
}
.select > input:not(:checked) {
  z-index: 4;
}
.select > input:not(:checked) ~ div label.option > span.title {
  display: none;
  color: black;
}
.select > input:not(:checked) ~ i.toggle.icon-arrow-up {
  display: none;
}
.select > input:not(:checked) ~ i.toggle.icon-arrow-down {
  display: block;
}
.select > input:disabled {
  cursor: no-drop;
}
.select > span.placeholder {
  position: relative;
  z-index: 0;
  display: inline-block;
  width: 100%;
  color: #fff;
  border-top: 0px;
  background-color: var(--accent-color);
}
.select label.option {
  display: block;
  overflow: hidden;
  z-index: 1;
  width: 100%;
  background-color: var(--accent-color);
}
.select label.option span.title {
  position: relative;
  z-index: 2;
  color: black;
}
.select label.option span.title i.icon {
  padding-right: 8px;
  color: #fff;
}
.select label.option span.title:hover {
  color: #fff;
  background: var(--accent-color);
  box-shadow: inset 0px 1px 0px rgba(0, 0, 0, 0.1);
}
.select label.option input {
  display: none;
}
.select label.option input:checked ~ span.title {
  position: absolute;
  display: block;
  z-index: 3;
  top: 0px;
  font-size: 12px;
  background: var(--accent-color);
  border-top: 0px;
  box-shadow: none;
  color: inherit;
  width: 100%;
}
.select label.option input:disabled ~ span.title {
  background: #f9f9f9 !important;
  color: #aaa;
}
.select label.option input:disabled ~ span.title:hover {
  color: #aaa;
  background: none;
  cursor: no-drop;
}

.select div.options {
  position: absolute;
  top: 4rem;
  left: 0;
  z-index: 500;
}
</style>