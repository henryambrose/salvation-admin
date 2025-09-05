<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="`Service Type: ${serviceType.name}`" />

    <div class="mx-auto max-w-4xl">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Service Type Details</h1>
        <p class="mt-2 text-gray-600">View details for {{ serviceType.name }}.</p>
      </div>

      <div class="space-y-6">
        <!-- Service Type Information -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
          <div class="mb-6 flex items-start justify-between">
            <h3 class="text-lg font-medium text-gray-900">Service Type Information</h3>
            <div class="flex space-x-2">
              <Link
                :href="route('graveyard.service-types.edit', serviceType.id)"
                class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm leading-4 font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
              >
                Edit
              </Link>
              <Link
                :href="route('graveyard.service-types.index')"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
              >
                Back to List
              </Link>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Service Name -->
            <div>
              <dt class="mb-1 text-sm font-medium text-gray-500">Service Name</dt>
              <dd class="text-sm text-gray-900">{{ serviceType.name }}</dd>
            </div>

            <!-- Category -->
            <div>
              <dt class="mb-1 text-sm font-medium text-gray-500">Category</dt>
              <dd class="text-sm text-gray-900">
                <span
                  class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                  :class="getCategoryColor(serviceType.category)"
                >
                  {{ serviceType.category }}
                </span>
              </dd>
            </div>

            <!-- Type -->
            <div>
              <dt class="mb-1 text-sm font-medium text-gray-500">Type</dt>
              <dd class="text-sm text-gray-900">
                <span
                  class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                  :class="getTypeColor(serviceType.type)"
                >
                  {{ serviceType.type }}
                </span>
              </dd>
            </div>

            <!-- Cost -->
            <div>
              <dt class="mb-1 text-sm font-medium text-gray-500">Cost</dt>
              <dd class="text-sm font-semibold text-gray-900">₹{{ formatPrice(serviceType.cost) }}</dd>
            </div>

            <!-- Sort Order -->
            <div>
              <dt class="mb-1 text-sm font-medium text-gray-500">Sort Order</dt>
              <dd class="text-sm text-gray-900">{{ serviceType.sort_order }}</dd>
            </div>

            <!-- Status -->
            <div>
              <dt class="mb-1 text-sm font-medium text-gray-500">Status</dt>
              <dd class="text-sm text-gray-900">
                <span
                  class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                  :class="serviceType.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ serviceType.is_active ? 'Active' : 'Inactive' }}
                </span>
              </dd>
            </div>
          </div>

          <!-- Description -->
          <div v-if="serviceType.description" class="mt-6">
            <dt class="mb-2 text-sm font-medium text-gray-500">Description</dt>
            <dd class="text-sm whitespace-pre-wrap text-gray-900">{{ serviceType.description }}</dd>
          </div>
        </div>

        <!-- Audit Information -->
        <div class="rounded-lg bg-white p-6 shadow-sm">
          <h3 class="mb-4 text-lg font-medium text-gray-900">Audit Information</h3>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Created Information -->
            <div class="space-y-4">
              <h4 class="text-sm font-medium text-gray-700">Created</h4>
              <div class="space-y-2">
                <div>
                  <dt class="text-xs font-medium text-gray-500">Date</dt>
                  <dd class="text-sm text-gray-900">{{ formatDateTime(serviceType.created_at) }}</dd>
                </div>
                <div v-if="serviceType.creator">
                  <dt class="text-xs font-medium text-gray-500">By</dt>
                  <dd class="text-sm text-gray-900">{{ serviceType.creator.name }}</dd>
                </div>
              </div>
            </div>

            <!-- Updated Information -->
            <div class="space-y-4">
              <h4 class="text-sm font-medium text-gray-700">Last Updated</h4>
              <div class="space-y-2">
                <div>
                  <dt class="text-xs font-medium text-gray-500">Date</dt>
                  <dd class="text-sm text-gray-900">{{ formatDateTime(serviceType.updated_at) }}</dd>
                </div>
                <div v-if="serviceType.updater">
                  <dt class="text-xs font-medium text-gray-500">By</dt>
                  <dd class="text-sm text-gray-900">{{ serviceType.updater.name }}</dd>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  serviceType: Object,
});

const breadcrumbs = [
  { name: 'Graveyard', href: route('graveyard.dashboard') },
  { name: 'Service Types', href: route('graveyard.service-types.index') },
  { name: 'Details', href: null },
];

const getCategoryColor = (category) => {
  const colors = {
    grave: 'bg-blue-100 text-blue-800',
    funeral: 'bg-purple-100 text-purple-800',
    additional: 'bg-green-100 text-green-800',
  };
  return colors[category] || 'bg-gray-100 text-gray-800';
};

const getTypeColor = (type) => {
  const colors = {
    normal: 'bg-blue-100 text-blue-800',
    concession: 'bg-yellow-100 text-yellow-800',
    free: 'bg-green-100 text-green-800',
  };
  return colors[type] || 'bg-gray-100 text-gray-800';
};

const formatPrice = (cost) => {
  return parseFloat(cost).toLocaleString('en-IN', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
};

const formatDateTime = (dateTime) => {
  if (!dateTime) return 'N/A';
  return new Date(dateTime).toLocaleString('en-IN', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true,
  });
};
</script>
