<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="text-center">
      <h1 class="text-6xl font-bold text-yellow-600">503</h1>
      <h2 class="mt-4 text-2xl font-bold text-gray-900">Service Unavailable</h2>
      <p class="mt-2 text-gray-600">We're temporarily unavailable. Please try again later.</p>
    </div>
    <div class="mt-8 text-center">
      <Button @click="tryAgain">Try Again</Button>
    </div>
  </div>
</template>

<script setup>
import { Button } from '@/components/ui/button'
const tryAgain = () => window.location.reload()
</script>
