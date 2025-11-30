<script setup lang="ts">
import type { Immobilie } from '~/types/domain';

const immobilien = useImmobilienStore();
</script>

<template>
<div class="immo-grid">
    <ImmoCard v-for="(immobilie, index) in immobilien.immobilien" :immobilie="immobilie"
     @delete="immobilien.deleteImmobilie(immobilie.id)"
     @hide="immobilien.hideImmobilie(immobilie.id)"
     :key="index"/>
</div>
</template>

<style lang="css" scoped>
.immo-grid {
    display: grid;
    gap: 25px;
    box-sizing: border-box;
    justify-content: center;
    padding: 0 20px;
    grid-template-columns: repeat(auto-fill, 450px);
    justify-items: center;
    align-items: center;
    width: 100%;
}

@media screen and (max-width: 460px) {
  .immo-grid {
    grid-template-columns: repeat(auto-fill, 300px);
  }
}
</style>