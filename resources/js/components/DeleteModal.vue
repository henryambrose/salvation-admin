<template>
  <div class="bg-opacity-50 fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600">
    <div class="relative top-20 mx-auto w-96 rounded-md border bg-[#ffffff] p-5 shadow-lg" @keydown="handleFocusTrap">
      <!-- Focus trap start -->
      <div ref="firstFocusRef" tabindex="0" class="sr-only"></div>

      <div class="mt-3 text-center">
        <!-- Warning Icon -->
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
          <svg class="h-[1.5rem] w-[1.5rem] text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
            ></path>
          </svg>
        </div>

        <!-- Title and Message -->
        <h3 class="mt-4 text-lg font-medium text-gray-900">{{ title }}</h3>
        <div class="mt-2 px-7">
          <p class="text-sm text-gray-500">{{ message }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-center space-x-3">
          <Button variant="outline" @click="$emit('close')" class="px-4 py-2"> Cancel </Button>
          <Button @click="$emit('confirm')" class="bg-red-600 px-4 py-2 hover:bg-red-700"> Delete </Button>
        </div>

        <!-- Focus trap end -->
        <div ref="lastFocusRef" tabindex="0" class="sr-only"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ref } from 'vue';

// Focus management refs
const firstFocusRef = ref<HTMLElement | null>(null);
const lastFocusRef = ref<HTMLElement | null>(null);

// Focus trap handler
const handleFocusTrap = (event: KeyboardEvent) => {
  if (event.key !== 'Tab') return;

  if (event.shiftKey) {
    // Shift+Tab on first element - move to last
    if (document.activeElement === firstFocusRef.value) {
      event.preventDefault();
      lastFocusRef.value?.focus();
    }
  } else {
    // Tab on last element - move to first
    if (document.activeElement === lastFocusRef.value) {
      event.preventDefault();
      firstFocusRef.value?.focus();
    }
  }
};

// Props
defineProps({
  item: {
    type: Object,
    default: null,
  },
  title: {
    type: String,
    default: 'Confirm Deletion',
  },
  message: {
    type: String,
    default: 'Are you sure you want to delete this item? This action cannot be undone.',
  },
});

// Emits
defineEmits(['close', 'confirm']);
</script>
