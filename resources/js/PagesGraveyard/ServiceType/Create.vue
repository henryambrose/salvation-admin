<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Create Service Type" />

    <div class="mx-auto max-w-4xl">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Service Type</h1>
        <p class="mt-2 text-gray-600">Add a new service type to the graveyard system.</p>
      </div>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="rounded-lg bg-white p-6 shadow-sm">
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Service Name -->
            <div>
              <label for="name" class="mb-2 block text-sm font-medium text-gray-700"> Service Name * </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.name }"
                placeholder="Enter service name"
              />
              <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                {{ form.errors.name }}
              </p>
            </div>

            <!-- Category -->
            <div>
              <label for="category" class="mb-2 block text-sm font-medium text-gray-700"> Category * </label>
              <select
                id="category"
                v-model="form.category"
                required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.category }"
              >
                <option value="">Select Category</option>
                <option value="grave">Grave</option>
                <option value="funeral">Funeral</option>
                <option value="additional">Additional</option>
              </select>
              <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">
                {{ form.errors.category }}
              </p>
            </div>

            <!-- Type -->
            <div>
              <label for="type" class="mb-2 block text-sm font-medium text-gray-700"> Type * </label>
              <select
                id="type"
                v-model="form.type"
                required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.type }"
              >
                <option value="">Select Type</option>
                <option value="normal">Normal</option>
                <option value="concession">Concession</option>
                <option value="free">Free</option>
              </select>
              <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                {{ form.errors.type }}
              </p>
            </div>

            <!-- Cost -->
            <div>
              <label for="cost" class="mb-2 block text-sm font-medium text-gray-700"> Cost * </label>
              <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <span class="text-gray-500 sm:text-sm">₹</span>
                </div>
                <input
                  id="cost"
                  v-model="form.cost"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  class="block w-full rounded-md border-gray-300 pl-7 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-300': form.errors.cost }"
                  placeholder="0.00"
                />
              </div>
              <p v-if="form.errors.cost" class="mt-1 text-sm text-red-600">
                {{ form.errors.cost }}
              </p>
            </div>

            <!-- Sort Order -->
            <div>
              <label for="sort_order" class="mb-2 block text-sm font-medium text-gray-700"> Sort Order </label>
              <input
                id="sort_order"
                v-model="form.sort_order"
                type="number"
                min="1"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.sort_order }"
                placeholder="Auto-generated if left empty"
              />
              <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">
                {{ form.errors.sort_order }}
              </p>
            </div>

            <!-- Active Status -->
            <div>
              <label class="flex items-center space-x-2">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                <span class="text-sm font-medium text-gray-700">Active</span>
              </label>
              <p class="mt-1 text-xs text-gray-500">Inactive service types won't be available for selection</p>
            </div>
          </div>

          <!-- Description -->
          <div class="mt-6">
            <label for="description" class="mb-2 block text-sm font-medium text-gray-700"> Description </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              :class="{ 'border-red-300': form.errors.description }"
              placeholder="Enter service description (optional)"
            />
            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
              {{ form.errors.description }}
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
          <Link
            :href="route('graveyard.service-types.index')"
            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
          >
            <span v-if="form.processing">Creating...</span>
            <span v-else>Create Service Type</span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs = [
  { name: 'Graveyard', href: route('graveyard.dashboard') },
  { name: 'Service Types', href: route('graveyard.service-types.index') },
  { name: 'Create', href: null },
];

const form = useForm({
  name: '',
  description: '',
  category: '',
  type: '',
  cost: '',
  sort_order: '',
  is_active: true,
});

const submit = () => {
  form.post(route('graveyard.service-types.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>
