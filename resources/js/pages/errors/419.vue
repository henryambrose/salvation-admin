<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h1 class="text-6xl font-bold text-red-600">419</h1>
        <h2 class="mt-4 text-2xl font-bold text-gray-900">Page Expired</h2>
        <p class="mt-2 text-gray-600">Your session has expired. Please try again.</p>
      </div>
    </div>
    
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div class="space-y-4">
          <Button @click="tryAgain" class="w-full">
            Try Again
          </Button>
          <Button @click="goHome" variant="outline" class="w-full">
            Go Home
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Button } from '@/components/ui/button'

const tryAgain = () => {
  window.location.reload()
}

const goHome = () => {
  window.location.href = '/'
}
</script>
