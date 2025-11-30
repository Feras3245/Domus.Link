<script setup lang="ts">
const { t } = useI18n();
const hidden = defineModel<boolean>({required: true});
</script>

<template>
    <div popover class="popover" v-if="!hidden">
        <div class="popover-content">
            <Button variant="tertiary" @click="hidden=true" :Title="t('close')"><CloseIcon/></Button>
            <slot></slot>
        </div>
    </div>
</template>

<style lang="css" scoped>
.popover-content>:deep(.button) {
    align-self: flex-end;
    padding: 0;
}

.popover-content {
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.popover {
    display: absolute;
    width: 100%;
    height: 100%;
    background-color: rgba(from var(--contrast-normal) r g b / 50%);
    backdrop-filter: blur(10px);
    display: flex;
    flex-flow: column nowrap;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}
</style>

<i18n lang="json">
{
    "en": {
        "close": "Close"
    },
    "de": {
        "close": "Schließen"
    }
}
</i18n>