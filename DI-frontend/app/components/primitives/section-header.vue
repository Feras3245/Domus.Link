<script lang="ts" setup>
const props = defineProps<{
    icon?: Component,
    text: string
}>();
</script>

<template>
    <h6><component :is="icon" class="icon"/>{{ text }}</h6>
</template>

<style lang="css" scoped>
h6 {
    font-size: calc(var(--font-size) * 1.4);
    font-weight: 700;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background-color: var(--pink);
    color: var(--contrast-normal);
    border-radius: 10px;
}

h6>:deep(.icon){
    height: calc(var(--font-size) * 1.4);
}
</style>