<script lang="ts" setup>

const props = defineProps<{
    name: string
}>();

const attrs = useAttrs()

const componentName = computed(() => {
  // Map "youtube" → "youtube-icon"
  return `${props.name.toLowerCase()}-icon`
})

const iconComponent = defineAsyncComponent(() =>
  import(`~/components/icons/${componentName.value}.vue`)
)
</script>

<template>
  <component :is="iconComponent" v-bind="attrs" class="icon"/>
</template>

<style lang="css" scoped>
</style>
