<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Props {
  message: string;
}

defineProps<Props>();
</script>

<template>
  <Head title="Obituary Not Found" />

  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <div class="mx-auto h-24 w-24 text-gray-400">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <h1 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">
          Obituary Not Found
        </h1>
        <p class="mt-4 text-lg text-gray-600">
          {{ message }}
        </p>
        <div class="mt-8">
          <p class="text-sm text-gray-500">
            If you believe this is an error, please check the URL or contact the family directly.
          </p>
        </div>
        <div class="mt-6">
          <a
            href="/"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
          >
            Go to Homepage
          </a>
        </div>
      </div>
    </div>
  </div>
</template>