<script setup lang="ts">
import Flash from '@/components/ui/toastr/Flash.vue';
import ToastContainer from '@/components/ui/toast/ToastContainer.vue';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Flash />
    <ToastContainer />
    <div class="rounded p-4 shadow">
      <slot />
    </div>
  </AppLayout>
</template>
