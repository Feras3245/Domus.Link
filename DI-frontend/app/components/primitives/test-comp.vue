<script setup lang="ts">

const val = ref<number>(0);
const emits = defineEmits(['clicked']);

function clickedEvent() {
    emits('clicked', val);
}
</script>

<template>
    <div>
        <p>{{ val }}</p>
        <br>
        <Button @click="clickedEvent">Emit</Button>
    </div>
</template>

<style lang="css" scoped>
</style>