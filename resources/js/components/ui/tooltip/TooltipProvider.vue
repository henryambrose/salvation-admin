<script setup lang="ts">
import { provide, toRef } from 'vue'

interface TooltipProviderProps {
  delayDuration?: number
}

const props = withDefaults(defineProps<TooltipProviderProps>(), {
  delayDuration: 0,
})

const delayDuration = toRef(props, 'delayDuration')

provide('tooltip', {
  delayDuration,
})
</script>

<template>
  <slot />
</template>
