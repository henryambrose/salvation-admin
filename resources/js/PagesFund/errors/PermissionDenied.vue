<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100">
    <Head title="Access Restricted - Fund App" />
    
    <!-- Header with Logo -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
            </div>
            <div class="ml-3">
              <h1 class="text-xl font-semibold text-gray-900">Our Lady of Salvation</h1>
              <p class="text-sm text-green-600 font-medium">Fund Management Portal</p>
            </div>
          </div>
          
          <!-- User Info -->
          <div class="flex items-center space-x-4">
            <div class="text-right">
              <p class="text-sm font-medium text-gray-900">{{ user?.name || 'User' }}</p>
              <p class="text-xs text-gray-500">{{ user?.roles?.[0]?.name || 'Guest' }}</p>
            </div>
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
              <span class="text-sm font-medium text-gray-700">{{ user?.name?.charAt(0) || 'U' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex items-center justify-center min-h-[calc(100vh-80px)] p-4">
      <div class="max-w-md w-full">
        <!-- Error Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
          <!-- Icon -->
          <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>

          <!-- Title -->
          <h1 class="text-2xl font-bold text-gray-900 mb-3">
            Fund Access Restricted
          </h1>

          <!-- Message -->
          <p class="text-gray-600 mb-6 leading-relaxed">
            {{ message || 'You do not have the required permissions to access the Fund Management system. Please contact your administrator if you believe this is an error.' }}
          </p>

          <!-- Action Buttons -->
          <div class="space-y-3">
            <!-- Go Back Button -->
            <button
              @click="goBack"
              class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Go Back
            </button>

            <!-- Switch to Members Button -->
            <button
              @click="goToMembers"
              class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              Switch to Members App
            </button>
          </div>

          <!-- Help Text -->
          <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500">
              Need help? Contact your system administrator or check your role permissions.
            </p>
          </div>
        </div>

        <!-- Additional Info Card -->
        <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
          <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Fund App Permissions:
          </h3>
          <ul class="text-sm text-gray-600 space-y-2">
            <li class="flex items-start gap-2">
              <span class="w-1.5 h-1.5 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
              <span>Fund Management access</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="w-1.5 h-1.5 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
              <span>Annual Contributions management</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="w-1.5 h-1.5 bg-green-600 rounded-full mt-2 flex-shrink-0"></span>
              <span>Mass Intentions tracking</span>
            </li>
          </ul>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
          <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Alternative Actions:
          </h3>
          <div class="grid grid-cols-2 gap-3">
            <button
              @click="goToDashboard"
              class="text-left p-3 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200"
            >
              <div class="font-medium text-sm text-gray-900">Dashboard</div>
              <div class="text-xs text-gray-500">Main dashboard</div>
            </button>
            <button
              @click="goToUsers"
              class="text-left p-3 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors duration-200"
            >
              <div class="font-medium text-sm text-gray-900">User Management</div>
              <div class="text-xs text-gray-500">Manage permissions</div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

interface Props {
  message?: string;
  user?: any;
}

const props = withDefaults(defineProps<Props>(), {
  message: '',
  user: null
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

const goToMembers = () => {
  router.visit('/member');
};

const goToUsers = () => {
  router.visit('/users');
};
</script>
