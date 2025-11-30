<script setup lang="ts">
const { t } = useI18n()

function switchLocale(currentLocale:string){
  useNuxtApp().$i18n.setLocale((currentLocale === 'de') ? 'en' : 'de');
}
</script>

<template>
  <Button @click="switchLocale($i18n.locale)" variant="tertiary" :title="t('title')" v-bind="$attrs">
    <LocaleIcon/>{{ $i18n.locale.toUpperCase() }}
  </Button>
</template>

<style scoped>
.button {
  padding: 10px;
  border-radius: 5px;
}
</style>

<i18n lang="json">
{
  "en": {
    "title": "change language"
  },
  "de": {
    "title": "Sprache wechseln"
  }
}
</i18n>
