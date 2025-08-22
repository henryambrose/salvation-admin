<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Mass Intentions</h1>
            <Link 
              :href="route('fund.mass-intentions.create')"
              class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Book New Intention
            </Link>
          </div>
          
          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-green-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-green-900">Total This Month</h3>
              <p class="text-3xl font-bold text-green-600">0</p>
              <p class="text-sm text-green-700">Intentions</p>
            </div>
            
            <div class="bg-blue-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-blue-900">Pending</h3>
              <p class="text-3xl font-bold text-blue-600">0</p>
              <p class="text-sm text-blue-700">Awaiting Mass</p>
            </div>
            
            <div class="bg-purple-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-purple-900">Completed</h3>
              <p class="text-3xl font-bold text-purple-600">0</p>
              <p class="text-sm text-purple-700">This Month</p>
            </div>
            
            <div class="bg-orange-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-orange-900">Total Revenue</h3>
              <p class="text-3xl font-bold text-orange-600">₹0</p>
              <p class="text-sm text-orange-700">This Month</p>
            </div>
          </div>
          
          <!-- Placeholder for Data Table -->
          <div class="bg-gray-50 p-8 rounded-lg text-center">
            <div class="text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No mass intentions yet</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by booking the first mass intention.</p>
              <div class="mt-6">
                <Link 
                  :href="route('fund.mass-intentions.create')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  Book Intention
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
