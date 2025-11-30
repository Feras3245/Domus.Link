<script setup lang="ts">
import Slider from '@vueform/slider';

const props = defineProps<{
  min: number
  max: number
}>();

const from = defineModel<number>('from', {required: true});
const to = defineModel<number>('to', {required: true});

const vals = ref<number[]>([from.value, to.value]);
</script>

<template>
  <div class="container">
        <slider
        :min="0"
        :max="100"
        v-model="vals"
        ></slider>
  </div>
</template>

<style scoped>
.container {
  position: relative;
  width: 100%;
  height: fit-content;
  margin-top: 0;
  margin-bottom: 10px;
}
</style>

<style src="@vueform/slider/themes/default.css"></style>