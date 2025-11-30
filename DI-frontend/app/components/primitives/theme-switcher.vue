<script setup lang="ts">
const { t } = useI18n()

const theme = useState<'light' | 'dark'>(() => {
  // check if theme cookie has been previously set
  const themeCookie = useCookie<'light'|'dark'>('theme');
  if(typeof themeCookie === 'undefined'){
    // default theme is 'light'
    return 'light';
  } else {
    return themeCookie.value;
  }
});

function switchTheme(){
  theme.value = (theme.value === 'light') ? 'dark' : 'light';
  document.body.style.colorScheme = theme.value;
  // set theme cookie with expires and max-age for one year from now
  const nextYearDate = new Date();
  nextYearDate.setFullYear(nextYearDate.getFullYear() + 1);
  const themeCookie = useCookie<'light'|'dark'>('theme', {maxAge:31536000, expires:nextYearDate});
  themeCookie.value = theme.value;
}

// set color-scheme css property for document body to theme on app mount
onMounted(() => {
  document.body.style.colorScheme = theme.value;
})
</script>

<template>
<Button @click="switchTheme" variant="tertiary" :title="t('title')" v-bind="$attrs">
  <DarkThemeIcon v-if="theme === 'light'"/>
  <LightThemeIcon v-else/>
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
    "title": "change theme"
  },
  "de": {
    "title": "Farbschema wechseln"
  }
}
</i18n>
