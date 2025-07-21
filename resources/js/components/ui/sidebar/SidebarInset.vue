<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useSidebar } from './utils';

const { state } = useSidebar()
const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <main
    data-slot="sidebar-inset"
    :class="cn(
      ' relative flex w-full flex-1 flex-col',
      'md:peer-data-[variant=inset]:rounded-xl md:peer-data-[variant=inset]:shadow-sm',
      state === 'collapsed' ? 'w-[calc(100%-64px)]' : 'w-[calc(100%-16rem)]',
      props.class,
    )"
  >
    <slot />
  </main>
</template>
