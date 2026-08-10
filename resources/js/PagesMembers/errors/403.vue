<template>
  <div class="flex min-h-screen flex-col justify-center bg-gray-50 py-12 sm:px-6 lg:px-8">
    <div class="text-center">
      <h1 class="text-6xl font-bold text-red-600">403</h1>
      <h2 class="mt-4 text-2xl font-bold text-gray-900">Access Denied</h2>
      <p class="mt-2 text-gray-600">You don't have permission to access this resource.</p>
    </div>
    <div class="mt-8 text-center">
      <Button @click="goHome">Go Home</Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
const goHome = () => (window.location.href = '/');
</script>
