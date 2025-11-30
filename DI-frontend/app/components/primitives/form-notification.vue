<script setup lang="ts">
const props = defineProps<{
    type: "ERROR"|"WARNING"|"INFO"|"OK"|"LOADING",
    text: string
}>();

const color = computed(() => {
    if (props.type === 'ERROR') {
        return "var(--red)";
    } else if (props.type === 'WARNING') {
        return "var(--orange)";
    } else if (props.type === 'INFO' || props.type === 'LOADING') {
        return "var(--purple)";
    } else if (props.type === 'OK') {
        return "var(--green)";
    }
});
</script>

<template>
    <p class="notification">
        <ErrorIcon v-if="type === 'ERROR'"/> 
        <WarningIcon v-if="type === 'WARNING'"/>
        <InfoIcon v-if="type === 'INFO'"/>
        <SuccessIcon v-if="type === 'OK'"/>
        <LoadingIcon v-if="type === 'LOADING'"/>
        {{ text }}</p>
</template>

<style lang="css" scoped>
.notification>:deep(svg) {
    height: var(--font-size);
}

.notification {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 5px;
    color: v-bind(color);
}
</style>