<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Create Service Type" />
    
    <div class="mx-auto max-w-4xl">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Service Type</h1>
        <p class="mt-2 text-gray-600">Add a new service type to the graveyard system.</p>
      </div>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Service Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Service Name *
              </label>
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
              <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                Category *
              </label>
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
              <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                Type *
              </label>
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
              <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                Cost *
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">₹</span>
                </div>
                <input
                  id="cost"
                  v-model="form.cost"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
              <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                Sort Order
              </label>
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
              <p class="mt-1 text-xs text-gray-500">
                Inactive service types won't be available for selection
              </p>
            </div>
          </div>

          <!-- Description -->
          <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
              Description
            </label>
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
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
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
import { ref } from 'vue'
import { router, useForm, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const breadcrumbs = [
  { name: 'Graveyard', href: route('graveyard.dashboard') },
  { name: 'Service Types', href: route('graveyard.service-types.index') },
  { name: 'Create', href: null }
]

const form = useForm({
  name: '',
  description: '',
  category: '',
  type: '',
  cost: '',
  sort_order: '',
  is_active: true
})

const submit = () => {
  form.post(route('graveyard.service-types.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>