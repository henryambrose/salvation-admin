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
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 scale-95"
        enter-to-class="transform opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform opacity-100 scale-100"
        leave-to-class="transform opacity-0 scale-95"
    >
        <div
            v-if="flashMessage"
            :class="[
                'fixed top-20 left-1/2 transform -translate-x-1/2 px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2 z-[9999] min-w-[300px] max-w-[600px]',
                flashType === 'success' ? 'bg-green-500 text-white border border-green-600' : 'bg-red-500 text-white border border-red-600'
            ]"
            >
            <span class="flex-grow">{{ flashMessage }}</span>
            <button @click="closeFlash" class="text-white text-xl leading-none hover:text-gray-200 transition-colors">&times;</button>
        </div>
    </Transition>
</template>
