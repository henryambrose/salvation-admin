<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, Cross, IndianRupee, MapPin, Users } from 'lucide-vue-next';

defineOptions({
  layout: AppLayout,
});

interface Occupancy {
  total: number;
  occupied: number;
  available: number;
}

interface Props {
  stats: {
    permanent_graves: Occupancy;
    temporary_graves: Occupancy;
    niches: Occupancy;
    pending_bookings: number;
    recent_burials: { name: string | null; location: string; date: string }[];
    maintenance_alerts: {
      total_due: number;
      count: number;
      items: { name: string | null; location: string; amount: number }[];
    };
    revenue_summary: {
      monthly: number;
      yearly: number;
    };
  };
}

defineProps<Props>();

const formatCurrency = (value: number) => '₹' + Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
</script>

<template>
  <Head title="Graveyard Dashboard" />

  <div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
      <div
        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-gray-700 to-gray-900 text-white shadow-lg"
      >
        <Cross class="h-8 w-8" />
      </div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Graveyard Management</h1>
      <p class="mt-2 text-gray-600 dark:text-gray-300">Graves, niches, bookings and payments</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center">
          <MapPin class="h-8 w-8 flex-shrink-0 text-blue-600" />
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Permanent Graves</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.permanent_graves.total }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              {{ stats.permanent_graves.occupied }} occupied · {{ stats.permanent_graves.available }} available
            </p>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center">
          <Users class="h-8 w-8 flex-shrink-0 text-green-600" />
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Temporary Graves</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.temporary_graves.total }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              {{ stats.temporary_graves.occupied }} occupied · {{ stats.temporary_graves.available }} available
            </p>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center">
          <Calendar class="h-8 w-8 flex-shrink-0 text-red-600" />
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Niches</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.niches.total }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ stats.niches.occupied }} occupied · {{ stats.niches.available }} available</p>
          </div>
        </div>
      </div>

      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center">
          <IndianRupee class="h-8 w-8 flex-shrink-0 text-purple-600" />
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Revenue This Month</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(stats.revenue_summary.monthly) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatCurrency(stats.revenue_summary.yearly) }} this year</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
      <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Quick Actions</h2>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Link
          :href="route('graveyard.permanent-grave-bookings.index')"
          class="block rounded-lg bg-blue-50 p-4 text-center hover:bg-blue-100 dark:bg-blue-900 dark:hover:bg-blue-800"
        >
          <MapPin class="mx-auto h-8 w-8 text-blue-600 dark:text-blue-400" />
          <p class="mt-2 font-medium text-blue-900 dark:text-blue-100">Permanent Bookings</p>
        </Link>

        <Link
          :href="route('graveyard.temporary-grave-bookings.index')"
          class="block rounded-lg bg-green-50 p-4 text-center hover:bg-green-100 dark:bg-green-900 dark:hover:bg-green-800"
        >
          <Users class="mx-auto h-8 w-8 text-green-600 dark:text-green-400" />
          <p class="mt-2 font-medium text-green-900 dark:text-green-100">Temporary Bookings</p>
        </Link>

        <Link
          :href="route('graveyard.payments.index')"
          class="block rounded-lg bg-purple-50 p-4 text-center hover:bg-purple-100 dark:bg-purple-900 dark:hover:bg-purple-800"
        >
          <IndianRupee class="mx-auto h-8 w-8 text-purple-600 dark:text-purple-400" />
          <p class="mt-2 font-medium text-purple-900 dark:text-purple-100">Payments</p>
          <p v-if="stats.pending_bookings" class="text-xs text-purple-800 dark:text-purple-200">{{ stats.pending_bookings }} pending bookings</p>
        </Link>

        <Link
          :href="route('graveyard.annual-maintenance-fees.index')"
          class="block rounded-lg bg-orange-50 p-4 text-center hover:bg-orange-100 dark:bg-orange-900 dark:hover:bg-orange-800"
        >
          <Calendar class="mx-auto h-8 w-8 text-orange-600 dark:text-orange-400" />
          <p class="mt-2 font-medium text-orange-900 dark:text-orange-100">Maintenance Fees</p>
        </Link>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Recent Burials</h2>
        <div v-if="stats.recent_burials.length === 0" class="py-8 text-center text-gray-500 dark:text-gray-400">No recent burials recorded</div>
        <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
          <li v-for="(b, i) in stats.recent_burials" :key="i" class="flex items-center justify-between py-2">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">{{ b.name || '—' }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ b.location }}</p>
            </div>
            <span class="text-sm text-gray-600 dark:text-gray-300">{{ b.date }}</span>
          </li>
        </ul>
      </div>

      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Pending Maintenance Dues</h2>
        <div v-if="stats.maintenance_alerts.count === 0" class="py-8 text-center text-gray-500 dark:text-gray-400">No pending dues</div>
        <template v-else>
          <p class="mb-2 text-sm text-gray-600 dark:text-gray-300">
            {{ stats.maintenance_alerts.count }} outstanding · {{ formatCurrency(stats.maintenance_alerts.total_due) }} total (top 5 shown)
          </p>
          <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            <li v-for="(a, i) in stats.maintenance_alerts.items" :key="i" class="flex items-center justify-between py-2">
              <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ a.name || '—' }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ a.location }}</p>
              </div>
              <span class="font-medium text-red-600">{{ formatCurrency(a.amount) }}</span>
            </li>
          </ul>
        </template>
      </div>
    </div>
  </div>
</template>
