<script setup lang="ts">
const props = defineProps({
  variant: {
    type: String as PropType<'primary' | 'secondary' | 'tertiary'>,
    required: false,
    validator: (value: string) => ['primary', 'secondary', 'tertiary'].includes(value),
    default: 'primary'
  },
  size: {
    type: Number,
    default: 1
  }
});
</script>

<template>
  <NuxtLink v-if="(typeof $attrs.form === 'undefined')" v-bind="$attrs" :class="`button ${variant}`">
    <slot>Link Button</slot>
  </NuxtLink>

  <button v-else v-bind="$attrs" :class="`button ${variant}`">
    <slot>Form Button</slot>
  </button>
</template>

<style scoped>
.button {
  --size: calc(var(--font-size) * v-bind(size));

  all: unset;
  padding: 10px 20px;
  border-radius: 0.5rem;
  user-select: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-weight: 500;
  font-size: var(--size);
  transition: all 0.3s ease-in-out;
  color: var(--white);
  &:deep(svg) {
		height: var(--size);

		/* Add subtle transition for icons */
		transition: transform 200ms ease;
	}
}

.button.primary {
  background: var(--pink);
  color: var(--white);
}

.button.primary:hover {
  background: var(--purple);
  color: var(--white);
  transform: translateY(-2px);
}

.button.primary:active {
  background: var(--orange);
  transform: translateY(0);
}

.button.secondary {
  box-shadow:inset 0px 0px 0px 1px var(--pink);
  color: var(--pink);
}

.button.secondary:hover {
  background: var(--pink);
  color: var(--white);
  box-shadow: none;
  transform: translateY(-2px);
}

.button.secondary:active {
  background: var(--orange);
  color: var(--white);
  box-shadow: none;
  transform: translateY(0);
}

.button.tertiary {
  color: var(--pink);
}

.button.tertiary:hover {
  color: var(--purple);
}

.button.tertiary:active {
  color: var(--orange);
}
</style>
