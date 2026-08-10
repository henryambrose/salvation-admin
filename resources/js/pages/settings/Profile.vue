<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { User as UserIcon } from 'lucide-vue-next';

interface Props {
  mustVerifyEmail: boolean;
  status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Profile settings',
    href: '/settings/profile',
  },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
  name: user.name,
  email: user.email,
});

const submit = () => {
  form.patch(route('profile.update'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Profile settings" />

    <SettingsLayout>
      <div class="space-y-8">
        <!-- Profile Header -->
        <div class="text-center">
          <div
            class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg"
          >
            <UserIcon class="h-10 w-10" />
          </div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Profile Information</h2>
          <p class="mt-2 text-gray-600 dark:text-gray-300">Update your personal details and contact information</p>
        </div>

        <!-- Profile Form -->
        <div class="rounded-xl bg-gray-50 p-6 dark:bg-gray-900">
          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <Label for="name" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Full Name</Label>
                <Input
                  id="name"
                  class="mt-2 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800"
                  v-model="form.name"
                  required
                  autocomplete="name"
                  placeholder="Enter your full name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
              </div>

              <div>
                <Label for="email" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email Address</Label>
                <Input
                  id="email"
                  type="email"
                  class="mt-2 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800"
                  v-model="form.email"
                  required
                  autocomplete="username"
                  placeholder="Enter your email address"
                />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>
            </div>

            <!-- Email Verification Notice -->
            <div
              v-if="mustVerifyEmail && !user.email_verified_at"
              class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-700 dark:bg-yellow-900"
            >
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path
                      fill-rule="evenodd"
                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Email Verification Required</h3>
                  <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button" class="font-medium underline hover:no-underline">
                      Click here to resend the verification email.
                    </Link>
                  </p>

                  <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-700 dark:text-green-300">
                    ✓ A new verification link has been sent to your email address.
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between border-t border-gray-200 pt-6 dark:border-gray-700">
              <div class="flex items-center gap-4">
                <Button
                  :disabled="form.processing"
                  class="rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-2 font-semibold text-white shadow-lg hover:from-blue-700 hover:to-purple-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800"
                >
                  <span v-if="form.processing">Saving...</span>
                  <span v-else>Save Changes</span>
                </Button>

                <Transition
                  enter-active-class="transition ease-in-out duration-300"
                  enter-from-class="opacity-0 transform scale-95"
                  leave-active-class="transition ease-in-out duration-300"
                  leave-to-class="opacity-0 transform scale-95"
                >
                  <div
                    v-show="form.recentlySuccessful"
                    class="flex items-center gap-2 rounded-lg bg-green-100 px-3 py-2 text-sm font-medium text-green-800 dark:bg-green-900 dark:text-green-200"
                  >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    Changes saved successfully!
                  </div>
                </Transition>
              </div>
            </div>
          </form>
        </div>

        <!-- Delete Account Section -->
        <div class="border-t border-gray-200 pt-8 dark:border-gray-700">
          <DeleteUser />
        </div>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
