<script setup lang="ts">
const props = defineProps<{
    pages: number,
    current: number
}>();
const emits = defineEmits(['change-page']);

function prev() {
    if (props.current > 1) {
        emits('change-page', (props.current - 1));
    }
}

function next() {
    if (props.current < props.pages) {
        emits('change-page', (props.current + 1));
    }
}

function goto(page:number) {
    if ((page >= 1) && (page <= props.pages) && (page !== props.current)) {
        emits('change-page', page);
    }
}
</script>

<template>
    <div class="paginator">
        <Button variant="tertiary" :size="1.1" @click="prev"><ArrowIcon direction="left"/></Button>
        <div class="pages">
            <Button v-for="(page, index) in Array.from({ length: pages }, (_, i) => i + 1)" :variant="(current === (page))? 'primary':'tertiary'" @click="goto(page)" :key="index">{{ page }}</Button>
        </div>
        <Button variant="tertiary" :size="1.1" @click="next"><ArrowIcon direction="right"/></Button>
    </div>
</template>

<style lang="css" scoped>
.pages {
    display: inline-flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 0.5rem;
}

.pages>:deep(.button) {
    padding: 0.1rem 0.3rem;
}

.paginator>:deep(.button) {
    padding: 0;
}

.paginator {
    display: inline-flex;
    flex-flow: row nowrap;
    align-items: center;
    gap: 1rem;
}
</style>