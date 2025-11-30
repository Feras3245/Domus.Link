<script setup lang="ts">
import { ref, type Component } from 'vue';

const props = defineProps<{
    options: {
        icon?: Component,
        text?: string,
        title?: string,
        value: any
    }[],
    allowNone?: boolean
}>();

const allowNone = ref<boolean>((props.allowNone || props.options.length===1)? true : false);

const selected = defineModel<any>();

function select(value:any) {
    if ((value === selected.value) && allowNone.value) {
        selected.value = undefined
    } else {
        selected.value = value;
    }
}
</script>

<template>
    <div class="toggle-buttons" v-bind="$attrs" v-if="options.length >= 1">
        <a class="toggle-button" v-for="(option, index) in props.options"
        :data-selected="(option.value === selected) ? 'yes' : 'no'"
        @click="select(option.value)" :title="option.title">
            <component :is="option.icon" class="icon"></component>
            <span v-if="option.text">{{ option.text }}</span>
        </a>
    </div>
</template>

<style lang="css" scoped>
.toggle-button[data-selected="yes"] {
    background-color: var(--pink);
    color: var(--white);
    border: 1px solid var(--pink);
}

.toggle-button[data-selected="yes"]:hover {
    color: var(--white);
}

.toggle-button[data-selected="yes"] + .toggle-button {
    border-left: none;
}

.toggle-button:has(+ .toggle-button[data-selected="yes"] + ) {
    border-right: none;
}

.icon {
    height: var(--font-size);
}

.toggle-buttons {
    display: inline-flex;
    flex-flow: row nowrap;
    align-items: stretch;
    justify-content: space-evenly;
    box-sizing: border-box;
}

.toggle-button {
    padding: 0.5rem;
    box-sizing: border-box;
    border: 1px solid var(--contrast-10-percent);
    background-color: var(--contrast-10-percent);
    margin-left: -1px;
    position: relative;
    z-index: 1;
    gap: 5px;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1);
    user-select: none;
}

.toggle-button:first-child {
    margin-left: 0;
    border-top-left-radius: 0.5rem;
    border-bottom-left-radius: 0.5rem;
}

.toggle-button:last-child {
    border-top-right-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.toggle-button:hover {
    color: var(--pink);
    border: 1px solid var(--pink);
    z-index: 2;
}

</style>