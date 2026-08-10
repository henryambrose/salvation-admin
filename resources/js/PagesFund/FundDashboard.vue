<template>
  <div class="py-6">
    <div class="mx-auto max-w-7xl">
      <div class="overflow-hidden bg-[#ffffff] shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h1 class="mb-6 text-2xl font-semibold">Fund Management Dashboard</h1>

          <!-- Quick Stats -->
          <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-4">
            <div class="rounded-lg bg-blue-50 p-6">
              <h3 class="text-lg font-medium text-blue-900">Annual Contributions</h3>
              <p class="text-3xl font-bold text-blue-600">₹0</p>
              <p class="text-sm text-blue-700">This Year</p>
            </div>

            <div class="rounded-lg bg-green-50 p-6">
              <h3 class="text-lg font-medium text-green-900">Mass Intentions</h3>
              <p class="text-3xl font-bold text-green-600">0</p>
              <p class="text-sm text-green-700">Active Bookings</p>
            </div>

            <div class="rounded-lg bg-purple-50 p-6">
              <h3 class="text-lg font-medium text-purple-900">Total Families</h3>
              <p class="text-3xl font-bold text-purple-600">0</p>
              <p class="text-sm text-purple-700">Contributing</p>
            </div>

            <div class="rounded-lg bg-orange-50 p-6">
              <h3 class="text-lg font-medium text-orange-900">Fund Categories</h3>
              <p class="text-3xl font-bold text-orange-600">0</p>
              <p class="text-sm text-orange-700">Active Categories</p>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-[#ffffff] p-6">
              <h3 class="mb-4 text-lg font-medium text-gray-900">Annual Contributions</h3>
              <p class="mb-4 text-gray-600">Manage parish member contributions and track annual giving.</p>
              <Link
                :href="route('fund.annual-contributions.index')"
                class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-blue-900"
              >
                Manage Contributions
              </Link>
            </div>

            <div class="rounded-lg border border-gray-200 bg-[#ffffff] p-6">
              <h3 class="mb-4 text-lg font-medium text-gray-900">Mass Intentions</h3>
              <p class="mb-4 text-gray-600">Schedule and manage mass intentions and special ceremonies.</p>
              <Link
                :href="route('fund.mass-intentions.index')"
                class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-green-900"
              >
                Manage Intentions
              </Link>
            </div>

            <div class="rounded-lg border border-gray-200 bg-[#ffffff] p-6">
              <h3 class="mb-4 text-lg font-medium text-gray-900">Fund Categories</h3>
              <p class="mb-4 text-gray-600">Manage fund categories for organizing contributions and donations.</p>
              <Link
                :href="route('fund.categories.index')"
                class="inline-flex items-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-orange-700 focus:bg-orange-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-orange-900"
              >
                Manage Categories
              </Link>
            </div>
          </div>

          <!-- Mass Intention Types Quick Action -->
          <div class="overflow-hidden rounded-lg bg-[#ffffff] shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <FileText class="h-[1.5rem] w-[1.5rem] text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Mass Intention Types</dt>
                    <dd class="text-lg font-medium text-gray-900">Manage Types</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
              <div class="text-sm">
                <Link :href="route('fund.mass-intention-types.index')" class="font-medium text-blue-700 hover:text-blue-900"> Manage Types </Link>
              </div>
            </div>
          </div>

          <!-- Mass Types Quick Action -->
          <div class="overflow-hidden rounded-lg bg-[#ffffff] shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <Clock class="h-[1.5rem] w-[1.5rem] text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Mass Types</dt>
                    <dd class="text-lg font-medium text-gray-900">Manage Schedules</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
              <div class="text-sm">
                <Link :href="route('fund.mass-types.index')" class="font-medium text-blue-700 hover:text-blue-900"> Manage Schedules </Link>
              </div>
            </div>
          </div>

          <!-- Payment Methods Quick Action -->
          <div class="overflow-hidden rounded-lg bg-[#ffffff] shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <IndianRupee class="h-[1.5rem] w-[1.5rem] text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Payment Methods</dt>
                    <dd class="text-lg font-medium text-gray-900">Manage Payment Options</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
              <div class="text-sm">
                <Link :href="route('fund.payment-methods.index')" class="font-medium text-blue-700 hover:text-blue-900"> Manage Methods </Link>
              </div>
            </div>
          </div>

          <!-- Book New Intention Quick Action -->
          <div class="overflow-hidden rounded-lg bg-[#ffffff] shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <Plus class="h-[1.5rem] w-[1.5rem] text-gray-400" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">Book New Intention</dt>
                    <dd class="text-lg font-medium text-gray-900">Schedule Mass</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
              <div class="text-sm">
                <Link :href="route('fund.mass-intentions.create')" class="font-medium text-blue-700 hover:text-blue-900"> Book Now </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Clock, FileText, IndianRupee, Plus } from 'lucide-vue-next';

defineOptions({
  layout: AppLayout,
});
</script>
