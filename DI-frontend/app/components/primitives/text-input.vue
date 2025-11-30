<script setup lang="ts">
const props = defineProps({
    name: {type:String, required: true},
    labelText: {type:String, required: false},
});

const value = defineModel<String>();
</script>

<template>
<label :for="name" class="text-input">
    <span v-if="(typeof labelText) !== 'undefined'">{{ labelText }}</span>
    <input v-model="value" v-bind="$attrs" :title="labelText" :name="name" :id="name"/>
</label>
</template>

<style scoped lang="css">
.text-input {
    display: flex;
    flex-flow: column nowrap;
    align-items: stretch;
    gap: 5px;
}

.text-input > span {
    font-weight: 600;
}

.text-input > input {
    box-sizing: border-box;
    padding: 0.4rem 0.4rem;
    border: 1px solid var(--grey);
    border-radius: 5px;
    background-color: var(--contrast-normal);
}

.text-input > input::placeholder {
    color: var(--grey);
    font-weight: 400;
}

.text-input > input:focus {
    border-color: var(--pink);
    outline: none;
}

.text-input> span:has(+ input:focus) {
    color: var(--pink);
}
</style>