<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Palette, Monitor, Sun, Moon } from 'lucide-vue-next';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Appearance settings',
    href: '/settings/appearance',
  },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Appearance settings" />

    <SettingsLayout>
      <div class="space-y-8">
        <!-- Appearance Header -->
        <div class="text-center">
          <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg">
            <Palette class="h-10 w-10" />
          </div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Appearance Settings</h2>
          <p class="mt-2 text-gray-600 dark:text-gray-300">Customize how the application looks and feels</p>
        </div>

        <!-- Theme Preview Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
          <div class="rounded-xl border-2 border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-center">
              <Sun class="h-8 w-8 text-yellow-500" />
            </div>
            <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-white">Light Mode</h3>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">Clean and bright interface</p>
          </div>

          <div class="rounded-xl border-2 border-gray-200 bg-gray-900 p-6 shadow-lg">
            <div class="mb-4 flex items-center justify-center">
              <Moon class="h-8 w-8 text-blue-400" />
            </div>
            <h3 class="text-center text-lg font-semibold text-white">Dark Mode</h3>
            <p class="mt-2 text-center text-sm text-gray-300">Easy on the eyes</p>
          </div>

          <div class="rounded-xl border-2 border-gray-200 bg-gradient-to-br from-white to-gray-100 p-6 shadow-lg dark:border-gray-700 dark:from-gray-800 dark:to-gray-900">
            <div class="mb-4 flex items-center justify-center">
              <Monitor class="h-8 w-8 text-gray-600 dark:text-gray-400" />
            </div>
            <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-white">System</h3>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">Follows your OS preference</p>
          </div>
        </div>

        <!-- Theme Selector -->
        <div class="rounded-xl bg-gray-50 p-6 dark:bg-gray-900">
          <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Choose Your Theme</h3>
          <p class="mb-6 text-sm text-gray-600 dark:text-gray-300">
            Select a theme that matches your preference. The system option will automatically switch between light and dark modes based on your device's settings.
          </p>
          
          <div class="flex justify-center">
            <AppearanceTabs />
          </div>
          
          <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200">Theme Tip</h4>
                <p class="mt-1 text-sm text-blue-800 dark:text-blue-300">
                  Your theme preference is automatically saved and will be remembered across all your devices.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
