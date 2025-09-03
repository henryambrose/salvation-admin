<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { reactiveOmit } from '@vueuse/core'
import { SelectItem, type SelectItemProps, useForwardProps } from 'reka-ui'

const props = withDefaults(defineProps<SelectItemProps & {
  class?: HTMLAttributes['class']
  inset?: boolean
}>(), {})

const delegatedProps = reactiveOmit(props, 'inset')
const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <SelectItem
    data-slot="select-item"
    :data-inset="inset ? '' : undefined"
    v-bind="forwardedProps"
    :class="cn('focus:bg-accent focus:text-accent-foreground relative flex cursor-default items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 data-[inset]:pl-8 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*=\'size-\'])]:size-4', props.class)"
  >
    <slot />
  </SelectItem>
</template>