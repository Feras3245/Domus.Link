<script lang="ts" setup>
const { t } = useI18n()

defineProps({
    size: {
    type: Number,
    required: false,
    default: 1
  },

  orientation: {
    type: String as PropType<'v' | 'h'>,
    required: false,
    default: 'h'
  }
})
</script>

<template>
<NuxtLink to="/" class="logo" :class="orientation" v-bind="$attrs">
    <NuxtImg class="logo-image" src="/DI-Link-Logo.png"/>
    <div class="title">
        <span class="text"><strong class="p1">Domus</strong><b>.</b><strong class="p2">Link</strong></span>
        <span class="subtext">{{ t('von') }} <strong><strong>Deutschland</strong>.Immobilien</strong> GmbH</span>
    </div>
</NuxtLink>
</template>

<style lang="css" scoped>
.logo {
    white-space: nowrap;
    display: flex;
}

.title {
    display: flex;
    flex-flow: column;
    gap: calc(var(--font-size) * 0.1 * v-bind(size));
}

.logo.h > .title{
    align-items: flex-start;
}

.logo.v > .title{
    align-items: center;
}

.logo.v {
    flex-flow: column nowrap;
    align-items: center;
    text-align: center;
}

.logo.h {
    flex-flow: row nowrap;
    align-items: center;
    gap: calc(var(--font-size) * 0.2 * v-bind(size));
}

.logo.h > .logo-image {
    height: calc(var(--font-size) * 2 * v-bind(size));
}

.logo.v > .logo-image {
    height: calc(var(--font-size) * 4 * v-bind(size));
}
.subtext {
    font-weight: 300;
    font-size: calc(var(--font-size) * 0.35 * v-bind(size));
}

.text {
    font-size: calc(var(--font-size) * v-bind(size));
}

.subtext>strong {
    font-weight: 500;
    font-family: "Work Sans";
}

.subtext>strong>strong {
    font-family: "Playfair Display";
}

.text>.p2 {
    font-weight: 300;
}
.text>.p1 {
    font-family: var(--font-alt);
    font-weight: 400;
}

.text>b {
    font-weight: 900;
    color: var(--pink);
    font-family: "Playfair Display";
}


a:link {
  color: unset;
  text-decoration: none;
}

a:visited {
  color: unset;
}

a:hover {
  color: unset;
}

a:active {
  color: unset;
}
</style>

<i18n lang="json">
{
  "en": {
    "von": "by"
  },
  "de": {
    "von": "von"
  }
}
</i18n>