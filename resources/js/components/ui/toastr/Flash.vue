<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const page = usePage();

const flashMessage = ref(page.props.flash?.success || page.props.flash?.error || null);
const receiptUrl = ref(page.props.flash?.receipt_url || null);

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
      receiptUrl.value = page.props.flash.receipt_url || null;

      // Only auto-dismiss if there's no receipt URL
      if (!receiptUrl.value) {
        setTimeout(() => {
          flashMessage.value = null;
        }, 3000); // auto-dismiss after 3 seconds
      }
    }
  },
  { immediate: true }
);

// Close manually
const closeFlash = () => {
  flashMessage.value = null;
  receiptUrl.value = null;
};

// Download receipt
const downloadReceipt = () => {
  if (receiptUrl.value) {
    window.open(receiptUrl.value, '_blank');
  }
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
                'fixed top-20 left-1/2 transform -translate-x-1/2 px-4 py-3 rounded-lg shadow-lg z-[9999] min-w-[300px] max-w-[600px]',
                flashType === 'success' ? 'bg-green-500 text-white border border-green-600' : 'bg-red-500 text-white border border-red-600'
            ]"
            >
            <div class="flex items-center space-x-2">
                <span class="flex-grow">{{ flashMessage }}</span>
                <button @click="closeFlash" class="text-white text-xl leading-none hover:text-gray-200 transition-colors">&times;</button>
            </div>

            <!-- Download Receipt Button -->
            <div v-if="receiptUrl" class="mt-3 pt-3 border-t border-white/20">
                <button
                    @click="downloadReceipt"
                    class="w-full bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded transition-colors flex items-center justify-center space-x-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Download Receipt</span>
                </button>
            </div>
        </div>
    </Transition>
</template>
