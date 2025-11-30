<script setup lang="ts">
import type { NuxtError } from '#app'
const { t, locale } = useI18n();

const props = defineProps<{
    error: NuxtError
}>();

const handleError = () => clearError({ redirect: '/' })
</script>

<template>
  <div style="height: 100vh;">
    <Logo orientation="h" :size="2" style="margin: 20px;"/>
    <div class="center">
      <h3><SadComputerIcon/> ERROR</h3>
      <h2>{{ error?.statusCode }}</h2>
      <p>{{ error?.message }}</p>
      <Button @click="handleError"><HouseIcon/>Back to Home</Button>
    </div>
  </div>
</template>

<style lang="css" scoped>
h2 {
  font-size: calc(var(--font-size) * 7);
  font-family: var(--font-alt);
  color: var(--pink);
}

h3:deep(svg) {
  height: calc(var(--font-size) * 3.5);
}

h3 {
  font-size: calc(var(--font-size) * 3.5);
  font-weight: 900;
  display: flex;
  flex-flow: row wrap;
  align-items: center;
  justify-content: center;
  gap: 20px;
}

.center {
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: center;
  text-align: center;
  gap: 15px;
}
</style>