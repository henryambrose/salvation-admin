<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Lock, Shield, Eye, EyeOff } from 'lucide-vue-next';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Password settings',
    href: '/settings/password',
  },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

// Password visibility toggles
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const updatePassword = () => {
  form.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: (errors: any) => {
      // Focus on the first field with an error
      if (errors.password && passwordInput.value instanceof HTMLInputElement) {
        passwordInput.value.focus();
      } else if (errors.current_password && currentPasswordInput.value instanceof HTMLInputElement) {
        currentPasswordInput.value.focus();
      }
    },
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Password settings" />

    <SettingsLayout>
      <div class="space-y-8">
        <!-- Password Header -->
        <div class="text-center">
          <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-r from-green-500 to-blue-600 text-white shadow-lg">
            <Lock class="h-10 w-10" />
          </div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Password Security</h2>
          <p class="mt-2 text-gray-600 dark:text-gray-300">Keep your account secure with a strong password</p>
        </div>

        <!-- Security Tips -->
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-6 dark:border-blue-700 dark:bg-blue-900">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <Shield class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200">Password Security Tips</h3>
              <ul class="mt-2 space-y-1 text-sm text-blue-800 dark:text-blue-300">
                <li>• Use at least 8 characters with a mix of letters, numbers, and symbols</li>
                <li>• Avoid common words, personal information, or sequential patterns</li>
                <li>• Consider using a password manager for stronger security</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Password Form -->
        <div class="rounded-xl bg-gray-50 p-6 dark:bg-gray-900">
          <form @submit.prevent="updatePassword" class="space-y-6">
            <!-- Current Password -->
            <div>
              <Label for="current_password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Current Password</Label>
              <div class="relative mt-2">
                <Input
                  id="current_password"
                  ref="currentPasswordInput"
                  v-model="form.current_password"
                  :type="showCurrentPassword ? 'text' : 'password'"
                  class="rounded-lg border-gray-300 pr-10 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-800"
                  autocomplete="current-password"
                  placeholder="Enter your current password"
                />
                <button
                  type="button"
                  @click="showCurrentPassword = !showCurrentPassword"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                >
                  <Eye v-if="!showCurrentPassword" class="h-5 w-5" />
                  <EyeOff v-else class="h-5 w-5" />
                </button>
              </div>
              <InputError class="mt-2" :message="form.errors.current_password" />
            </div>

            <!-- New Password -->
            <div>
              <Label for="password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">New Password</Label>
              <div class="relative mt-2">
                <Input
                  id="password"
                  ref="passwordInput"
                  v-model="form.password"
                  :type="showNewPassword ? 'text' : 'password'"
                  class="rounded-lg border-gray-300 pr-10 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-800"
                  autocomplete="new-password"
                  placeholder="Enter your new password"
                />
                <button
                  type="button"
                  @click="showNewPassword = !showNewPassword"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                >
                  <Eye v-if="!showNewPassword" class="h-5 w-5" />
                  <EyeOff v-else class="h-5 w-5" />
                </button>
              </div>
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div>
              <Label for="password_confirmation" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Confirm New Password</Label>
              <div class="relative mt-2">
                <Input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  class="rounded-lg border-gray-300 pr-10 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-800"
                  autocomplete="new-password"
                  placeholder="Confirm your new password"
                />
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                >
                  <Eye v-if="!showConfirmPassword" class="h-5 w-5" />
                  <EyeOff v-else class="h-5 w-5" />
                </button>
              </div>
              <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between border-t border-gray-200 pt-6 dark:border-gray-700">
              <div class="flex items-center gap-4">
                <Button 
                  :disabled="form.processing"
                  class="rounded-lg bg-gradient-to-r from-green-600 to-blue-600 px-6 py-2 font-semibold text-white shadow-lg hover:from-green-700 hover:to-blue-700 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800"
                >
                  <span v-if="form.processing">Updating...</span>
                  <span v-else>Update Password</span>
                </Button>

                <Transition
                  enter-active-class="transition ease-in-out duration-300"
                  enter-from-class="opacity-0 transform scale-95"
                  leave-active-class="transition ease-in-out duration-300"
                  leave-to-class="opacity-0 transform scale-95"
                >
                  <div v-show="form.recentlySuccessful" class="flex items-center gap-2 rounded-lg bg-green-100 px-3 py-2 text-sm font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Password updated successfully!
                  </div>
                </Transition>
              </div>
            </div>
          </form>
        </div>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
