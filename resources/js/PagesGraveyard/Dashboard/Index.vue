<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { MapPin, Users, Calendar, DollarSign, Cross } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout
});
interface Props {
  stats: {
    total_cemeteries: number;
    total_graves: number;
    occupied_graves: number;
    available_graves: number;
    recent_burials: any[];
    maintenance_alerts: any[];
    revenue_summary: {
      monthly: number;
      yearly: number;
    };
  };
}

const props = defineProps<Props>();
</script>

<template>
  <Head title="Graveyard Dashboard" />

  <div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-gray-700 to-gray-900 text-white shadow-lg">
        <Cross class="h-8 w-8" />
      </div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        <Cross class="inline h-8 w-8 mr-3 text-gray-700 dark:text-gray-300" />
        Graveyard Management
      </h1>
      <p class="mt-2 text-gray-600 dark:text-gray-300">Cemetery operations and burial records management</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
      <!-- Total Cemeteries -->
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <MapPin class="h-8 w-8 text-blue-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Cemeteries</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_cemeteries }}</p>
          </div>
        </div>
      </div>

      <!-- Total Graves -->
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <Users class="h-8 w-8 text-green-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Graves</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_graves }}</p>
          </div>
        </div>
      </div>

      <!-- Occupied Graves -->
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <Calendar class="h-8 w-8 text-red-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Occupied Graves</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.occupied_graves }}</p>
          </div>
        </div>
      </div>

      <!-- Monthly Revenue -->
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <DollarSign class="h-8 w-8 text-purple-600" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Monthly Revenue</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ stats.revenue_summary.monthly.toLocaleString() }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
      <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <a href="/graveyard/cemeteries" class="block rounded-lg bg-blue-50 p-4 text-center hover:bg-blue-100 dark:bg-blue-900 dark:hover:bg-blue-800">
          <MapPin class="mx-auto h-8 w-8 text-blue-600 dark:text-blue-400" />
          <p class="mt-2 font-medium text-blue-900 dark:text-blue-100">Manage Cemeteries</p>
        </a>

        <a href="/graveyard/graves" class="block rounded-lg bg-green-50 p-4 text-center hover:bg-green-100 dark:bg-green-900 dark:hover:bg-green-800">
          <Users class="mx-auto h-8 w-8 text-green-600 dark:text-green-400" />
          <p class="mt-2 font-medium text-green-900 dark:text-green-100">Manage Graves</p>
        </a>

        <a href="/graveyard/burials" class="block rounded-lg bg-purple-50 p-4 text-center hover:bg-purple-100 dark:bg-purple-900 dark:hover:bg-purple-800">
          <Calendar class="mx-auto h-8 w-8 text-purple-600 dark:text-purple-400" />
          <p class="mt-2 font-medium text-purple-900 dark:text-purple-100">Record Burials</p>
        </a>

        <a href="/graveyard/maintenance" class="block rounded-lg bg-orange-50 p-4 text-center hover:bg-orange-100 dark:bg-orange-900 dark:hover:bg-orange-800">
          <DollarSign class="mx-auto h-8 w-8 text-orange-600 dark:text-orange-400" />
          <p class="mt-2 font-medium text-orange-900 dark:text-orange-100">Maintenance</p>
        </a>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Recent Burials -->
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Recent Burials</h2>
        <div v-if="stats.recent_burials.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
          No recent burials recorded
        </div>
        <!-- TODO: Add recent burials list -->
      </div>

      <!-- Maintenance Alerts -->
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Maintenance Alerts</h2>
        <div v-if="stats.maintenance_alerts.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
          No maintenance alerts
        </div>
        <!-- TODO: Add maintenance alerts list -->
      </div>
    </div>
  </div>
</template>
