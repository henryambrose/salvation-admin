<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
  password: '',
});

const deleteUser = (e: Event) => {
  e.preventDefault();

  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  form.clearErrors();
  form.reset();
};
</script>

<template>
  <div class="space-y-6">
    <div class="text-center">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
        <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
      </div>
      <h3 class="text-xl font-bold text-red-900 dark:text-red-200">Danger Zone</h3>
      <p class="mt-2 text-sm text-red-700 dark:text-red-300">Permanently delete your account and all associated data</p>
    </div>

    <div class="rounded-xl border border-red-200 bg-red-50 p-6 dark:border-red-700 dark:bg-red-900">
      <div class="space-y-4">
        <div class="flex items-start space-x-3">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <h4 class="text-lg font-semibold text-red-900 dark:text-red-200">Delete Account</h4>
            <p class="mt-1 text-sm text-red-700 dark:text-red-300">
              Once you delete your account, there is no going back. This action cannot be undone and will permanently remove:
            </p>
            <ul class="mt-2 space-y-1 text-sm text-red-700 dark:text-red-300">
              <li>• Your profile and personal information</li>
              <li>• All your saved preferences and settings</li>
              <li>• Access to all connected services</li>
            </ul>
          </div>
        </div>
        
        <Dialog>
          <DialogTrigger as-child>
            <Button variant="destructive" class="w-full rounded-lg bg-red-600 px-6 py-3 font-semibold text-white shadow-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-800">
              I understand, delete my account
            </Button>
          </DialogTrigger>
          <DialogContent class="rounded-2xl">
            <form class="space-y-6" @submit="deleteUser">
              <DialogHeader class="space-y-4 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                  <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <DialogTitle class="text-xl font-bold text-red-900 dark:text-red-200">Confirm Account Deletion</DialogTitle>
                <DialogDescription class="text-gray-600 dark:text-gray-300">
                  This action is permanent and cannot be undone. All your data will be permanently deleted from our servers.
                </DialogDescription>
              </DialogHeader>

              <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/50">
                <p class="mb-3 text-sm font-medium text-red-800 dark:text-red-200">
                  To confirm deletion, please enter your password:
                </p>
                <div class="grid gap-2">
                  <Label for="password" class="sr-only">Password</Label>
                  <Input 
                    id="password" 
                    type="password" 
                    name="password" 
                    ref="passwordInput" 
                    v-model="form.password" 
                    placeholder="Enter your password" 
                    class="rounded-lg border-red-300 focus:border-red-500 focus:ring-red-500"
                  />
                  <InputError :message="form.errors.password" />
                </div>
              </div>

              <DialogFooter class="gap-3">
                <DialogClose as-child>
                  <Button variant="secondary" @click="closeModal" class="rounded-lg px-6 py-2">
                    Cancel
                  </Button>
                </DialogClose>

                <Button 
                  variant="destructive" 
                  :disabled="form.processing" 
                  class="rounded-lg bg-red-600 px-6 py-2 font-semibold text-white hover:bg-red-700"
                >
                  <span v-if="form.processing">Deleting...</span>
                  <span v-else>Delete Account Forever</span>
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </div>
    </div>
  </div>
</template>
