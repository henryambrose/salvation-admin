<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-[#ffffff] overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex items-center mb-6">
            <Link 
              :href="route('fund.annual-contributions.index')"
              class="mr-4 text-gray-500 hover:text-gray-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </Link>
            <h1 class="text-2xl font-semibold">Annual Contribution Details</h1>
          </div>
          
          <!-- Details Placeholder -->
          <div class="bg-gray-50 p-8 rounded-lg text-center">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Details coming soon</h3>
              <p class="mt-1 text-sm text-gray-500">Annual contribution details will be displayed here.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout
});
</script>
