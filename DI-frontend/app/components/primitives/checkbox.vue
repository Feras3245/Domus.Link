<script setup lang="ts">
const props = defineProps({
    name: {type:String, required: true},
});

const value = defineModel<boolean>({default: false});
</script>

<template>
<label :for="name" class="checkbox-input">
    <input v-model="value" v-bind="$attrs" type="checkbox" :name="name" :id="name"/>
    <span v-if="(typeof $slots) !== 'undefined'"><slot></slot></span>
</label>
</template>

<style scoped lang="css">

.checkbox-input {
    display: inline-flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 0.4rem;
    width: fit-content;
}

input[type="checkbox"] {
  width: 1.3rem;
  height: 1.3rem;
  accent-color: var(--contrast-normal);
}

input[type="checkbox"]:checked {
  accent-color: #fe356d;
}
</style>