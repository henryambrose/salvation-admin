<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Annual Contributions</h1>
            <Link 
              :href="route('fund.annual-contributions.create')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Add New Contribution
            </Link>
          </div>
          
          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-blue-900">Total This Year</h3>
              <p class="text-3xl font-bold text-blue-600">₹0</p>
              <p class="text-sm text-blue-700">2024</p>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-green-900">Total Families</h3>
              <p class="text-3xl font-bold text-green-600">0</p>
              <p class="text-sm text-green-700">Contributing</p>
            </div>
            
            <div class="bg-purple-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-purple-900">Monthly Average</h3>
              <p class="text-3xl font-bold text-purple-600">₹0</p>
              <p class="text-sm text-purple-700">Per Month</p>
            </div>
            
            <div class="bg-orange-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-orange-900">Pending</h3>
              <p class="text-3xl font-bold text-orange-600">0</p>
              <p class="text-sm text-orange-700">Families</p>
            </div>
          </div>
          
          <!-- Placeholder for Data Table -->
          <div class="bg-gray-50 p-8 rounded-lg text-center">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No contributions yet</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by adding the first annual contribution.</p>
              <div class="mt-6">
                <Link 
                  :href="route('fund.annual-contributions.create')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Add Contribution
                </Link>
              </div>
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
