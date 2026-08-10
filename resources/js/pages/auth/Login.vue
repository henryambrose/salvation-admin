<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthEnhancedLayout from '@/layouts/auth/AuthEnhancedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Lock, Mail } from 'lucide-vue-next';

defineProps<{
  status?: string;
  canResetPassword: boolean;
}>();

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <AuthEnhancedLayout title="Our Lady of Salvation" description="Welcome back! Please enter your credentials to access your account.">
    <Head title="Log in" />

    <div
      v-if="status"
      class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-center text-sm font-medium text-green-600 backdrop-blur-sm"
    >
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <div class="space-y-5">
        <div class="space-y-2">
          <Label for="email" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email address</Label>
          <div class="group relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
              <Mail class="h-5 w-5 text-gray-400 transition-colors duration-200 group-focus-within:text-blue-500" />
            </div>
            <Input
              id="email"
              type="email"
              required
              autofocus
              :tabindex="1"
              autocomplete="email"
              v-model="form.email"
              placeholder="Enter your email address"
              class="bg-opacity-50 h-14 rounded-xl border-2 border-gray-200 bg-[#ffffff] pl-12 text-base backdrop-blur-sm transition-all duration-200 focus:border-blue-500 focus:ring-4"
            />
          </div>
          <InputError :message="form.errors.email" />
        </div>

        <div class="space-y-2">
          <Label for="password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Password</Label>
          <div class="group relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
              <Lock class="h-5 w-5 text-gray-400 transition-colors duration-200 group-focus-within:text-blue-500" />
            </div>
            <Input
              id="password"
              type="password"
              required
              :tabindex="2"
              autocomplete="current-password"
              v-model="form.password"
              placeholder="Enter your password"
              class="focus:ring-4rounded-xl bg-opacity-50 h-14 border-2 border-gray-200 bg-[#ffffff] pl-12 text-base backdrop-blur-sm transition-all duration-200 focus:border-blue-500"
            />
          </div>
          <InputError :message="form.errors.password" />
        </div>

        <div class="flex items-center justify-between">
          <Label for="remember" class="group flex cursor-pointer items-center space-x-3">
            <div class="relative">
              <Checkbox
                id="remember"
                v-model="form.remember"
                :tabindex="3"
                class="h-5 w-5 rounded-md border-2 border-gray-300 text-blue-600 transition-all duration-200 focus:ring-4"
              />
            </div>
            <span
              class="text-sm text-gray-700 transition-colors duration-200 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-white"
              >Remember me</span
            >
          </Label>
        </div>
      </div>

      <Button
        type="submit"
        class="h-14 w-full transform rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 text-base font-semibold text-white shadow-lg transition-all duration-200 hover:scale-[1.02] hover:from-blue-700 hover:to-purple-700 hover:shadow-xl"
        :tabindex="4"
        :disabled="form.processing"
      >
        <LoaderCircle v-if="form.processing" class="mr-3 h-5 w-5 animate-spin" />
        {{ form.processing ? 'Signing in...' : 'Sign in to your account' }}
      </Button>
    </form>
  </AuthEnhancedLayout>
</template>
