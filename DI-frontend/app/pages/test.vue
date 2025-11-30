<script setup lang="ts">

function something(val:Ref<number>) {
    val.value = 10;
}
</script>

<template>
    <div style="height: 200px;"></div>
    <div class="center">
        <test-comp @clicked="something"></test-comp>
    </div>
    <div style="height: 200px;"></div>
</template>

<style lang="css" scoped>
.center {
    width: 95%;
    margin: auto;
}
</style>

