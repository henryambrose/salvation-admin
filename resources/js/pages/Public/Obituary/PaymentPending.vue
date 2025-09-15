<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Clock, CreditCard, AlertCircle } from 'lucide-vue-next';

defineProps({
  message: String,
  paymentStatus: String,
  obituaryName: String,
});

const getStatusIcon = (status: string) => {
  switch (status) {
    case 'pending':
      return Clock;
    case 'partial':
      return CreditCard;
    default:
      return AlertCircle;
  }
};

const getStatusColor = (status: string) => {
  switch (status) {
    case 'pending':
      return 'text-amber-600';
    case 'partial':
      return 'text-blue-600';
    default:
      return 'text-red-600';
  }
};

const getStatusBgColor = (status: string) => {
  switch (status) {
    case 'pending':
      return 'bg-amber-50 border-amber-200';
    case 'partial':
      return 'bg-blue-50 border-blue-200';
    default:
      return 'bg-red-50 border-red-200';
  }
};
</script>

<template>
  <Head title="Payment Required - Obituary Page" />

  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <!-- Status Icon -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-6"
             :class="getStatusBgColor(paymentStatus)">
          <component :is="getStatusIcon(paymentStatus)"
                     class="h-8 w-8"
                     :class="getStatusColor(paymentStatus)" />
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-900 mb-4">
          Payment Required
        </h1>

        <!-- Obituary Name -->
        <div v-if="obituaryName" class="mb-6">
          <p class="text-lg text-gray-700">
            Obituary for
          </p>
          <p class="text-xl font-semibold text-gray-900">
            {{ obituaryName }}
          </p>
        </div>

        <!-- Message -->
        <div class="mb-8 p-4 rounded-lg border" :class="getStatusBgColor(paymentStatus)">
          <p class="text-gray-800" :class="getStatusColor(paymentStatus)">
            {{ message }}
          </p>
        </div>

        <!-- Payment Status Details -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">What's happening?</h3>

          <div v-if="paymentStatus === 'pending'" class="space-y-3 text-left">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 bg-amber-400 rounded-full mt-2 flex-shrink-0"></div>
              <p class="text-gray-600">This obituary page is waiting for payment to be completed</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 bg-amber-400 rounded-full mt-2 flex-shrink-0"></div>
              <p class="text-gray-600">Once payment is processed, the page will become publicly available</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 bg-amber-400 rounded-full mt-2 flex-shrink-0"></div>
              <p class="text-gray-600">Please contact the administrator if you have questions about payment</p>
            </div>
          </div>

          <div v-else-if="paymentStatus === 'partial'" class="space-y-3 text-left">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 flex-shrink-0"></div>
              <p class="text-gray-600">This obituary page has a partial payment</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 flex-shrink-0"></div>
              <p class="text-gray-600">The remaining balance needs to be paid before publication</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 flex-shrink-0"></div>
              <p class="text-gray-600">Please contact the administrator to complete the payment</p>
            </div>
          </div>

          <div v-else class="space-y-3 text-left">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 bg-red-400 rounded-full mt-2 flex-shrink-0"></div>
              <p class="text-gray-600">There is an issue with the payment for this obituary page</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 bg-red-400 rounded-full mt-2 flex-shrink-0"></div>
              <p class="text-gray-600">Please contact the administrator for assistance</p>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-gray-100 rounded-lg p-6">
          <h3 class="text-md font-medium text-gray-900 mb-3">Need Help?</h3>
          <p class="text-sm text-gray-600 mb-4">
            If you believe this is an error or need assistance with payment, please contact our support team.
          </p>
          <div class="space-y-2 text-sm text-gray-700">
            <p>📧 Contact the administrator</p>
            <p>📞 Call for immediate assistance</p>
            <p>🕒 We're here to help resolve payment issues</p>
          </div>
        </div>

        <!-- Status Badge -->
        <div class="mt-8">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize"
                :class="getStatusBgColor(paymentStatus)">
            <component :is="getStatusIcon(paymentStatus)"
                       class="h-4 w-4 mr-2"
                       :class="getStatusColor(paymentStatus)" />
            Payment {{ paymentStatus || 'Unknown' }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Additional custom styles if needed */
.min-h-screen {
  background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
</style>