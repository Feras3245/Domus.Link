<script setup lang="ts">
const props = defineProps<{
    id: string;
    label?: string;
    options?: string[];
    orientation?: 'v'|'h';
}>();

const text = defineModel<string>('text', {default: ''});
const checked = defineModel<boolean>('checked', {default: false});
const selected = defineModel<number>('index', {default: 0});
const files = defineModel<File[]>('files', {default: []});
const number = defineModel<number>('number', {default: 0});

const fileInput = ref<HTMLInputElement>();
const attrs = useAttrs();
const field = ref<number>(0);

if (attrs.type === 'textarea') {
    field.value = 1;
} else if (attrs.type === 'radio') {
    if (props.options && props.options.length > 1) {
        field.value = 2;
    } else {
        field.value = -1;
        throw Error('radio input field must have at least two options!');
    }
} else if (attrs.type === 'dropdown') {
    if (props.options && props.options.length > 1) {
        field.value = 3;
    } else {
        field.value = -1;
        throw Error('dropdown field must have at least two options!');
    }
} else if (attrs.type === 'checkbox') {
    field.value = 4;
} else if (attrs.type === 'file') {
    field.value = 5;
} else if (attrs.type === 'number') {
    field.value = 6;
} else if (attrs.type === 'range') {
    field.value = 7;
} else {
    field.value = 0;
}

function uploadFiles() {
    if (fileInput.value && fileInput.value.files) {
        files.value = Array.from(fileInput.value.files);
    }
}
</script>

<template>
    <div class="input" :class="orientation" v-if="field === 0">
        <label class="label" v-if="label" :for="id">{{ label }}</label>
        <input v-model="text" :name="id" :id="id" v-bind="$attrs">
    </div>
    <div class="input v" v-if="field === 1">
        <label class="label" v-if="label" :for="id">{{ label }}</label>
        <textarea v-model="text" :name="id" :id="id" v-bind="$attrs"></textarea>
    </div>
    <div class="radio" :class="orientation" v-if="field === 2">
        <span class="label">{{ label }}</span>
        <div class="option" v-for="(option, index) in options" :key="index">
            <input v-model="selected" :id="id + '-' + index" :name="id" type="radio" v-bind="$attrs" :value="index"></input>
            <label :for="id + '-' + index" :title="option">{{ option }}</label>
        </div>
    </div>
    <div class="dropdown" :class="orientation" v-if="field === 3">
        <span class="label">{{ label }}</span>
        <select :name="id" v-bind="$attrs" :id="id" v-model="selected">
            <option v-for="(option, index) in options" :value="index">{{ option }}</option>
        </select>
    </div>
    <div class="checkbox" :class="orientation" v-if="field === 4">
        <input v-model="checked" :name="id" :id="id" type="checkbox" v-bind="$attrs">
        <label v-if="label" :for="id">{{ label }}</label>
    </div>
    <div class="input" :class="orientation" v-if="field === 5">
        <label class="label" v-if="label" type="" :for="id">{{ label }}</label>
        <input type="file" ref="fileInput" @input="uploadFiles" :name="id" :id="id" v-bind="$attrs">
    </div>
    <div class="input" :class="orientation" v-if="field === 6">
        <label class="label" v-if="label" :for="id">{{ label }}</label>
        <input v-model="number" type="number" :name="id" :id="id" v-bind="$attrs">
    </div>
</template>

<style lang="css" scoped>
.checkbox>input[type="checkbox"] {
    padding: var(--font-size);
    transform: scale(1.2);
    accent-color: var(--pink);
}

.checkbox {
    display: flex;
    flex-flow: row nowrap;
    align-items: baseline;
    gap: 0.5rem;
}

.dropdown>select {
  appearance: base-select;
  border-radius: 5px;
  padding: 0.5rem;
  border: 1px solid var(--grey);
  position: relative;
  outline: none;
  flex: 1 1 auto;
  background-color: var(--input-background);
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  border: 1px solid var(--contrast-10-percent);
  border-radius: 0.5rem;
  box-sizing: border-box;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1);
  min-height: calc(var(--font-size) + 20px);
}

.dropdown>select::picker-icon {
    color: var(--contrast-reverse);
    transition: 0.4s rotate;
}

.dropdown>select:hover::picker-icon{
  color: var(--pink);
  overflow: visible;
}

.dropdown>select:open {
  border: 1px solid var(--pink);
}


.dropdown>select::picker(select) {
  appearance: base-select;
  border-radius: 0.5rem;
  border: 1px solid var(--contrast-10-percent);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1);
 }

.dropdown>select>option::checkmark {
  display: none;
 }

.dropdown>select:open::picker-icon {
  color: var(--pink);
  rotate: 180deg;
}

.dropdown {
    display: flex;
    flex-flow: row wrap;
    align-items: center;
    gap: 0.5rem;
}

.dropdown.h {
    flex-flow: row nowrap;
}

.dropdown.v {
    flex-flow: column nowrap;
    align-items: start;
}

.dropdown.v>select {
    width: 100%;
}

.dropdown>select:open>option {
    padding: 0.5rem;
    box-sizing: border-box;
 }


.dropdown>select:open>option:hover {
  background-color: var(--pink);
  color: var(--white);
 }

.radio>.option>input[type="radio"] {
    accent-color: var(--pink);
}

.radio>.option {
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 0.5rem;
}

.radio {
    display: flex;
    flex-flow: row wrap;
    align-items: flex-start;
    gap: 0.5rem;
}

.radio.h {
    flex-flow: row nowrap;
}

.radio.v {
    flex-flow: column nowrap;
}

.label {
    line-height: 1.25rem;
    font-weight: 600;
}

.input>textarea {
    resize: none;
    min-height: 200px;
}

.input>input:focus, .input>textarea:focus {
    border-color: var(--pink);
}

.input>input, .input>textarea {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    outline: none;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    box-sizing: border-box;
    padding: 0.5rem;
    border: 1px solid var(--contrast-10-percent);
    flex: 1 1 auto;
}

.input {
    display: flex;
    flex-flow: row wrap;
    align-items: center;
    gap: 0.5rem;
}

.input.v {
    flex-flow: column nowrap;
    align-items: start;
}

.input.h {
    flex-flow: row nowrap;
}

.input.v>input, .input.v>textarea {
    width: 100%;
}
</style>