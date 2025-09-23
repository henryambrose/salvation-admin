<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { AlertCircle, Clock, CreditCard } from 'lucide-vue-next';

interface Props {
  message?: string;
  paymentStatus?: string;
  obituaryName?: string;
  issueType?: 'payment' | 'review' | 'private' | 'inactive';
}

const props = defineProps<Props>();

const getStatusIcon = (issueType?: string, status?: string) => {
  if (issueType === 'payment') {
    return status === 'partial' ? CreditCard : Clock;
  } else if (issueType === 'review') {
    return Clock;
  } else {
    return AlertCircle;
  }
};

const getStatusColor = (issueType?: string, status?: string) => {
  if (issueType === 'payment') {
    return status === 'partial' ? 'text-blue-600' : 'text-amber-600';
  } else if (issueType === 'review') {
    return 'text-green-600';
  } else if (issueType === 'private') {
    return 'text-gray-600';
  } else {
    return 'text-red-600';
  }
};

const getStatusBgColor = (issueType?: string, status?: string) => {
  if (issueType === 'payment') {
    return status === 'partial' ? 'bg-blue-50 border-blue-200' : 'bg-amber-50 border-amber-200';
  } else if (issueType === 'review') {
    return 'bg-green-50 border-green-200';
  } else if (issueType === 'private') {
    return 'bg-gray-50 border-gray-200';
  } else {
    return 'bg-red-50 border-red-200';
  }
};

const getPageTitle = (issueType?: string) => {
  switch (issueType) {
    case 'payment':
      return 'Payment Required - Obituary Page';
    case 'review':
      return 'Under Review - Obituary Page';
    case 'private':
      return 'Private Page - Obituary';
    case 'inactive':
      return 'Page Unavailable - Obituary';
    default:
      return 'Not Available - Obituary Page';
  }
};
</script>

<template>
  <Head :title="getPageTitle(props.issueType || 'payment')" />

  <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-[448px] space-y-8">
      <div class="text-center">
        <!-- Status Icon -->
        <div
          class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full"
          :class="getStatusBgColor(props.issueType || 'payment', props.paymentStatus)"
        >
          <component
            :is="getStatusIcon(props.issueType || 'payment', props.paymentStatus)"
            class="h-8 w-8"
            :class="getStatusColor(props.issueType || 'payment', props.paymentStatus)"
          />
        </div>

        <!-- Title -->
        <h1 class="mb-4 text-2xl font-bold text-gray-900">
          {{ props.issueType === 'review' ? 'Under Review' : props.issueType === 'payment' ? 'Payment Required' : 'Page Unavailable' }}
        </h1>

        <!-- Obituary Name -->
        <div v-if="props.obituaryName" class="mb-6">
          <p class="text-lg text-gray-700">Obituary for</p>
          <p class="text-xl font-semibold text-gray-900">
            {{ props.obituaryName }}
          </p>
        </div>

        <!-- Message -->
        <div class="mb-8 rounded-lg border p-4" :class="getStatusBgColor(props.issueType || 'payment', props.paymentStatus)">
          <p class="text-gray-800" :class="getStatusColor(props.issueType || 'payment', props.paymentStatus)">
            {{ props.message }}
          </p>
        </div>

        <!-- Payment Status Details -->
        <div class="mb-8 rounded-lg border bg-white p-6 shadow-sm">
          <h3 class="mb-4 text-lg font-medium text-gray-900">What's happening?</h3>

          <div v-if="props.paymentStatus === 'pending'" class="space-y-3 text-left">
            <div class="flex items-start gap-3">
              <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-amber-400"></div>
              <p class="text-gray-600">This obituary page is waiting for payment to be completed</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-amber-400"></div>
              <p class="text-gray-600">Once payment is processed, the page will become publicly available</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-amber-400"></div>
              <p class="text-gray-600">Please contact the administrator if you have questions about payment</p>
            </div>
          </div>

          <div v-else-if="props.paymentStatus === 'partial'" class="space-y-3 text-left">
            <div class="flex items-start gap-3">
              <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-blue-400"></div>
              <p class="text-gray-600">This obituary page has a partial payment</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-blue-400"></div>
              <p class="text-gray-600">The remaining balance needs to be paid before publication</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-blue-400"></div>
              <p class="text-gray-600">Please contact the administrator to complete the payment</p>
            </div>
          </div>

          <div v-else class="space-y-3 text-left">
            <div class="flex items-start gap-3">
              <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-red-400"></div>
              <p class="text-gray-600">There is an issue with the payment for this obituary page</p>
            </div>
            <div class="flex items-start gap-3">
              <div class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-red-400"></div>
              <p class="text-gray-600">Please contact the administrator for assistance</p>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="rounded-lg bg-gray-100 p-6">
          <h3 class="text-md mb-3 font-medium text-gray-900">Need Help?</h3>
          <p class="mb-4 text-sm text-gray-600">If you believe this is an error or need assistance with payment, please contact our support team.</p>
          <div class="space-y-2 text-sm text-gray-700">
            <p>📧 Contact the administrator</p>
            <p>📞 Call for immediate assistance</p>
            <p>🕒 We're here to help resolve payment issues</p>
          </div>
        </div>

        <!-- Status Badge -->
        <div class="mt-8">
          <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium capitalize" :class="getStatusBgColor(props.paymentStatus)">
            <component :is="getStatusIcon(props.paymentStatus)" class="mr-2 h-4 w-4" :class="getStatusColor(props.paymentStatus)" />
            Payment {{ props.paymentStatus || 'Unknown' }}
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
