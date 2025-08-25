<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h1 class="text-2xl font-semibold mb-6">Fund Management Dashboard</h1>
          
          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-blue-900">Annual Contributions</h3>
              <p class="text-3xl font-bold text-blue-600">₹0</p>
              <p class="text-sm text-blue-700">This Year</p>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-green-900">Mass Intentions</h3>
              <p class="text-3xl font-bold text-green-600">0</p>
              <p class="text-sm text-green-700">Active Bookings</p>
            </div>
            
            <div class="bg-purple-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-purple-900">Total Families</h3>
              <p class="text-3xl font-bold text-purple-600">0</p>
              <p class="text-sm text-purple-700">Contributing</p>
            </div>
            
            <div class="bg-orange-50 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-orange-900">Fund Categories</h3>
              <p class="text-3xl font-bold text-orange-600">0</p>
              <p class="text-sm text-orange-700">Active Categories</p>
            </div>
          </div>
          
          <!-- Quick Actions -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Annual Contributions</h3>
              <p class="text-gray-600 mb-4">Manage parish member contributions and track annual giving.</p>
              <Link 
                :href="route('fund.annual-contributions.index')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Manage Contributions
              </Link>
            </div>
            
            <div class="bg-white border border-gray-200 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Mass Intentions</h3>
              <p class="text-gray-600 mb-4">Schedule and manage mass intentions and special ceremonies.</p>
              <Link 
                :href="route('fund.mass-intentions.index')"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Manage Intentions
              </Link>
            </div>
            
            <div class="bg-white border border-gray-200 p-6 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Fund Categories</h3>
              <p class="text-gray-600 mb-4">Manage fund categories for organizing contributions and donations.</p>
              <Link 
                :href="route('fund.categories.index')"
                class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Manage Categories
              </Link>
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
