<template>
  <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
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
        <h1 class="mb-3 text-2xl font-bold text-gray-900">Access Restricted</h1>

        <!-- Message -->
        <p class="mb-6 leading-relaxed text-gray-600">
          {{
            message ||
            'You do not have the required permissions to access this page. Please contact your administrator if you believe this is an error.'
          }}
        </p>

        <!-- Action Buttons -->
        <div class="space-y-3">
          <!-- Go Back Button -->
          <button
            @click="goBack"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 font-medium text-white transition-colors duration-200 hover:bg-indigo-700"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Go Back
          </button>

          <!-- Dashboard Button -->
          <button
            @click="goToDashboard"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-3 font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-200"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"
              ></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
            </svg>
            Go to Dashboard
          </button>
        </div>

        <!-- Help Text -->
        <div class="mt-6 border-t border-gray-200 pt-6">
          <p class="text-sm text-gray-500">Need help? Contact your system administrator or check your role permissions.</p>
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
          What you can do:
        </h3>
        <ul class="space-y-2 text-sm text-gray-600">
          <li class="flex items-start gap-2">
            <span class="mt-2 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-600"></span>
            <span>Check if you're logged in with the correct account</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="mt-2 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-600"></span>
            <span>Verify your role has the required permissions</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="mt-2 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-600"></span>
            <span>Contact your administrator for access</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';

interface Props {
  message?: string;
}

const props = withDefaults(defineProps<Props>(), {
  message: '',
});

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.visit('/');
  }
};

const goToDashboard = () => {
  router.visit('/dashboard');
};
</script>
