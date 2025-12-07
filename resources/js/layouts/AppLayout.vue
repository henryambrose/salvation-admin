<script setup lang="ts">
import Flash from '@/components/ui/toastr/Flash.vue';
import ToastContainer from '@/components/ui/toast/ToastContainer.vue';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { useTabScrollIntoView } from '@/composables/useTabScrollIntoView';
import type { BreadcrumbItemType } from '@/types';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

// Initialize auto-scroll behavior for tab navigation
// This applies globally to all forms in the application
useTabScrollIntoView({
  block: 'center',
  behavior: 'smooth',
  offset: 80,
  excludeSelectors: [
    '[data-no-autoscroll]',     // Allow opt-out via data attribute
    '.dropdown-menu',            // Exclude dropdown menus
    '[role="listbox"]',          // Exclude listbox items (SearchDropdown internals)
    '[role="option"]',           // Exclude option elements in dropdowns
    '.modal',                    // Exclude modal internals
    '[data-radix-popper-content-wrapper]', // Exclude Radix UI popovers/dropdowns
  ],
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
