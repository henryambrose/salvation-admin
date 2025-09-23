<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Props {
  message: string;
}

defineProps<Props>();
</script>

<template>
  <Head title="Obituary Not Found" />

  <div class="flex min-h-screen flex-col justify-center bg-gray-50 py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-[448px]">
      <div class="text-center">
        <div class="mx-auto h-24 w-24 text-gray-400">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
        </div>
        <h1 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Obituary Not Found</h1>
        <p class="mt-4 text-lg text-gray-600">
          {{ message }}
        </p>
        <div class="mt-8">
          <p class="text-sm text-gray-500">If you believe this is an error, please check the URL or contact the family directly.</p>
        </div>
        <div class="mt-6">
          <a
            href="/"
            class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:outline-none"
          >
            Go to Homepage
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
