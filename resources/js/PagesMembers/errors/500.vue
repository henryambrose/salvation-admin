<template>
  <div class="flex min-h-screen flex-col justify-center bg-gray-50 py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-[448px]">
      <div class="text-center">
        <h1 class="text-6xl font-bold text-red-600">500</h1>
        <h2 class="mt-4 text-2xl font-bold text-gray-900">Server Error</h2>
        <p class="mt-2 text-gray-600">Something went wrong on our end. Please try again later.</p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-[448px]">
      <div class="bg-[#ffffff] px-4 py-8 shadow sm:rounded-lg sm:px-10">
        <div class="space-y-4">
          <Button @click="tryAgain" class="w-full"> Try Again </Button>
          <Button @click="goHome" variant="outline" class="w-full"> Go Home </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Button } from '@/components/ui/button';

const tryAgain = () => {
  window.location.reload();
};

const goHome = () => {
  window.location.href = '/';
};
</script>
