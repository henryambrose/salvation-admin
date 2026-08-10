<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100">
    <Head title="Access Restricted - Fund App" />

    <!-- Header with Logo -->
    <div class="border-b bg-[#ffffff] shadow-sm">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-600">
                <svg class="h-[1.5rem] w-[1.5rem] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                  ></path>
                </svg>
              </div>
            </div>
            <div class="ml-3">
              <h1 class="text-xl font-semibold text-gray-900">Our Lady of Salvation</h1>
              <p class="text-sm font-medium text-green-600">Fund Management Portal</p>
            </div>
          </div>

          <!-- App Switcher -->
          <div class="flex items-center space-x-4">
            <div class="app-switcher relative">
              <button
                @click="toggleAppSwitcher"
                class="flex items-center space-x-2 rounded-lg bg-gray-100 px-4 py-2 transition-colors duration-200 hover:bg-gray-200"
              >
                <span class="text-sm font-medium text-gray-700">{{ currentApp === 'fund' ? 'Fund App' : 'Members App' }}</span>
                <svg class="h-[1rem] w-[1rem] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>

              <!-- App Switcher Dropdown -->
              <div v-if="showAppSwitcher" class="absolute right-0 z-50 mt-2 w-48 rounded-lg border border-gray-200 bg-[#ffffff] shadow-xl">
                <div class="py-2">
                  <button
                    @click="switchToApp('members')"
                    class="flex w-full items-center space-x-2 px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                  >
                    <svg class="h-[1rem] w-[1rem] text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"
                      ></path>
                    </svg>
                    <span>Members App</span>
                  </button>
                  <button
                    @click="switchToApp('fund')"
                    class="flex w-full items-center space-x-2 px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                  >
                    <svg class="h-[1rem] w-[1rem] text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                      ></path>
                    </svg>
                    <span>Fund App</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- User Info -->
            <div class="flex items-center space-x-4">
              <div class="text-right">
                <p class="text-sm font-medium text-gray-900">{{ user?.name || 'Guest User' }}</p>
                <p class="text-xs text-gray-500">{{ user?.roles?.[0]?.name || 'No Role Assigned' }}</p>
              </div>
              <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300">
                <span class="text-sm font-medium text-gray-700">{{ user?.name?.charAt(0) || 'G' }}</span>
              </div>
              <!-- Logout Button -->
              <button @click="logout" class="rounded-md bg-red-100 px-3 py-1 text-xs text-red-700 transition-colors duration-200 hover:bg-red-200">
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex min-h-[calc(100vh-80px)] items-center justify-center p-4">
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
          <h1 class="mb-3 text-2xl font-bold text-gray-900">Fund Access Restricted</h1>

          <!-- Message -->
          <p class="mb-6 leading-relaxed text-gray-600">
            {{
              message ||
              'You do not have the required permissions to access the Fund Management system. Please contact your administrator if you believe this is an error.'
            }}
          </p>

          <!-- Action Buttons -->
          <div class="space-y-3">
            <!-- Switch to Members App Button -->
            <button
              @click="switchToApp('members')"
              class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3 font-medium text-white transition-colors duration-200 hover:bg-blue-700"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                ></path>
              </svg>
              Switch to Members App
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
            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              ></path>
            </svg>
            Fund App Permissions:
          </h3>
          <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-start gap-2">
              <span class="mt-2 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-green-600"></span>
              <span>Fund Management access</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-2 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-green-600"></span>
              <span>Annual Contributions management</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="mt-2 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-green-600"></span>
              <span>Mass Intentions tracking</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

interface Props {
  message?: string;
  user?: any;
}

withDefaults(defineProps<Props>(), {
  message: '',
  user: null,
});

// App switcher state
const showAppSwitcher = ref(false);
const currentApp = ref('fund');

// Determine current app based on URL
onMounted(() => {
  const path = window.location.pathname;
  if (path.startsWith('/fund')) {
    currentApp.value = 'fund';
  } else {
    currentApp.value = 'members';
  }

  // Close app switcher when clicking outside
  document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement;
    if (!target.closest('.app-switcher')) {
      showAppSwitcher.value = false;
    }
  });
});

const toggleAppSwitcher = () => {
  showAppSwitcher.value = !showAppSwitcher.value;
};

const switchToApp = (app: string) => {
  showAppSwitcher.value = false;
  currentApp.value = app;

  if (app === 'fund') {
    router.visit('/fund');
  } else {
    router.visit('/dashboard');
  }
};

const logout = () => {
  router.get('/logout');
};
</script>
