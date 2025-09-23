<template>
  <div v-if="error" class="flex min-h-screen items-center justify-center bg-gradient-to-br from-red-50 to-pink-100 p-4">
    <div class="w-full max-w-[448px]">
      <!-- Error Card -->
      <div class="rounded-2xl bg-[#ffffff] p-8 text-center shadow-xl">
        <!-- Icon -->
        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-red-100">
          <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
            ></path>
          </svg>
        </div>

        <!-- Title -->
        <h1 class="mb-3 text-2xl font-bold text-gray-900">
          {{ errorTitle }}
        </h1>

        <!-- Message -->
        <p class="mb-6 leading-relaxed text-gray-600">
          {{ errorMessage }}
        </p>

        <!-- Action Buttons -->
        <div class="space-y-3">
          <!-- Retry Button -->
          <button
            v-if="canRetry"
            @click="retry"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3 font-medium text-white transition-colors duration-200 hover:bg-blue-700"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              ></path>
            </svg>
            Try Again
          </button>

          <!-- Go Back Button -->
          <button
            @click="goBack"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-3 font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-200"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Go Back
          </button>

          <!-- Home Button -->
          <button
            @click="goHome"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 font-medium text-white transition-colors duration-200 hover:bg-indigo-700"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
              ></path>
            </svg>
            Go Home
          </button>
        </div>

        <!-- Help Text -->
        <div class="mt-6 border-t border-gray-200 pt-6">
          <p class="text-sm text-gray-500">
            {{ helpText }}
          </p>
        </div>
      </div>

      <!-- Additional Info Card -->
      <div class="mt-6 rounded-xl bg-[#ffffff] p-6 shadow-lg">
        <h3 class="mb-3 flex items-center gap-2 font-semibold text-gray-900">
          <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            ></path>
          </svg>
          {{ additionalInfoTitle }}
        </h3>
        <ul class="space-y-2 text-sm text-gray-600">
          <li v-for="(item, index) in additionalInfoItems" :key="index" class="flex items-start gap-2">
            <span class="mt-2 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-600"></span>
            <span>{{ item }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Default Slot -->
  <slot v-else />
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
  error?: any;
  canRetry?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  error: null,
  canRetry: false,
});

const error = ref(props.error);

const errorTitle = computed(() => {
  if (!error.value) return 'An Error Occurred';

  if (error.value.status === 403) return 'Access Restricted';
  if (error.value.status === 404) return 'Page Not Found';
  if (error.value.status === 500) return 'Server Error';
  if (error.value.status === 419) return 'Session Expired';

  return 'An Error Occurred';
});

const errorMessage = computed(() => {
  if (!error.value) return 'Something went wrong. Please try again.';

  if (error.value.status === 403) {
    return (
      error.value.message || 'You do not have permission to access this resource. Please contact your administrator if you believe this is an error.'
    );
  }
  if (error.value.status === 404) {
    return 'The page you are looking for could not be found.';
  }
  if (error.value.status === 500) {
    return 'An internal server error occurred. Please try again later.';
  }
  if (error.value.status === 419) {
    return 'Your session has expired. Please refresh the page and try again.';
  }

  return error.value.message || 'Something went wrong. Please try again.';
});

const helpText = computed(() => {
  if (error.value?.status === 403) {
    return 'Need help? Contact your system administrator or check your role permissions.';
  }
  if (error.value?.status === 419) {
    return 'Try refreshing the page or logging in again.';
  }
  return 'If the problem persists, please contact support.';
});

const additionalInfoTitle = computed(() => {
  if (error.value?.status === 403) return 'What you can do:';
  if (error.value?.status === 404) return 'Possible solutions:';
  return 'Troubleshooting steps:';
});

const additionalInfoItems = computed(() => {
  if (error.value?.status === 403) {
    return [
      "Check if you're logged in with the correct account",
      'Verify your role has the required permissions',
      'Contact your administrator for access',
    ];
  }
  if (error.value?.status === 404) {
    return ['Check the URL for typos', 'Use the navigation menu to find the page', 'Go back to the previous page'];
  }
  if (error.value?.status === 419) {
    return ['Refresh the page', 'Clear your browser cache', 'Log in again if needed'];
  }
  return ['Check your internet connection', 'Try refreshing the page', 'Contact support if the issue persists'];
});

const retry = () => {
  if (error.value?.retry) {
    error.value.retry();
  } else {
    window.location.reload();
  }
};

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.visit('/');
  }
};

const goHome = () => {
  router.visit('/dashboard');
};

// Expose methods for parent components
defineExpose({
  setError: (err: any) => {
    error.value = err;
  },
  clearError: () => {
    error.value = null;
  },
});
</script>
