<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthEnhancedLayout from '@/layouts/auth/AuthEnhancedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, Lock } from 'lucide-vue-next';

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

const AppName = import.meta.env.VITE_APP_NAME || 'Our Lady of Salvation';
</script>

<template>
  <AuthEnhancedLayout title="Our Lady of Salvation" description="Welcome back! Please enter your credentials to access your account.">
    <Head title="Log in" />

    <div v-if="status" class="mb-6 p-4 text-center text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-xl backdrop-blur-sm">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <div class="space-y-5">
        <div class="space-y-2">
          <Label for="email" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email address</Label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <Mail class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200" />
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
              class="pl-12 h-14 text-base border-2 border-gray-200 focus:border-blue-500 focus:ring-4 rounded-xl transition-all duration-200 bg-[#ffffff] bg-opacity-50 backdrop-blur-sm"
            />
          </div>
          <InputError :message="form.errors.email" />
        </div>

        <div class="space-y-2">
          <Label for="password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Password</Label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <Lock class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200" />
            </div>
            <Input
              id="password"
              type="password"
              required
              :tabindex="2"
              autocomplete="current-password"
              v-model="form.password"
              placeholder="Enter your password"
              class="pl-12 h-14 text-base border-2 border-gray-200 focus:border-blue-500 focus:ring-4rounded-xl transition-all duration-200 bg-[#ffffff] bg-opacity-50 backdrop-blur-sm"
            />
          </div>
          <InputError :message="form.errors.password" />
        </div>

        <div class="flex items-center justify-between">
          <Label for="remember" class="flex items-center space-x-3 cursor-pointer group">
            <div class="relative">
              <Checkbox 
                id="remember" 
                v-model="form.remember" 
                :tabindex="3"
                class="w-5 h-5 text-blue-600 border-2 border-gray-300 rounded-md focus:ring-4 transition-all duration-200"
              />
            </div>
            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-200">Remember me</span>
          </Label>
          
          <!-- <a 
            v-if="canResetPassword" 
            :href="route('password.request')" 
            class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200 font-medium hover:underline"
          >
            Forgot password?
          </a> -->
        </div>
      </div>

      <Button 
        type="submit" 
        class="w-full h-14 text-base font-semibold bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 " 
        :tabindex="4" 
        :disabled="form.processing"
      >
        <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin mr-3" />
        {{ form.processing ? 'Signing in...' : 'Sign in to your account' }}
      </Button>

      <!-- <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          By signing in, you agree to our 
          <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium hover:underline transition-colors duration-200">Terms of Service</a> 
          and 
          <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium hover:underline transition-colors duration-200">Privacy Policy</a>
        </p>
      </div> -->
    </form>
  </AuthEnhancedLayout>
</template>
