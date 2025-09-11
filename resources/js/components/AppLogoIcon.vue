<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
  inheritAttrs: false,
});

interface Props {
  className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
   <img src="/church-logo-blue.svg" style="width: 93px; height: 82px">
</template>

<style>
/* Global dropdown option text color */
select option,
.dropdown-menu option,
.dropdown-menu__option {
  color: #333333 !important;
}
</style>
