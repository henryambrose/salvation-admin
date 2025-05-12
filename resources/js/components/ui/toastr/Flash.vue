<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const page = usePage();

const flashMessage = ref(page.props.flash?.success || page.props.flash?.error || null);

// Optional: determine message type
const flashType = ref(
  page.props.flash?.success ? 'success' : page.props.flash?.error ? 'error' : null
);

// Watch for flash updates
watch(
  () => page.props.flash,
  () => {
    if (page.props.flash?.success || page.props.flash?.error) {
      flashType.value = page.props.flash.success ? 'success' : 'error';
      flashMessage.value = page.props.flash.success || page.props.flash.error;
      setTimeout(() => {
        flashMessage.value = null;
      }, 3000); // auto-dismiss after 3 seconds
    }
  },
  { immediate: true }
);

// Close manually
const closeFlash = () => {
  flashMessage.value = null;
};
</script>

<template>
    <div
        v-if="flashMessage"
        :class="[
            'fixed top-15 left-1/2 transform -translate-x-1/2 px-4 py-3 rounded shadow flex items-center space-x-2',
            flashType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        ]"
        >
        <span class="flex-grow">{{ flashMessage }}</span>
        <button @click="closeFlash" class="text-white text-xl leading-none">&times;</button>
    </div>
</template>
