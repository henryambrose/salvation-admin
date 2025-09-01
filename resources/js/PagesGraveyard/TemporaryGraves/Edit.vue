<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-700">Edit Temporary Grave</h1>
            <Link 
              :href="route('graveyard.temporary-graves.index')"
              class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Back to List
            </Link>
          </div>

          <form @submit.prevent="submit">
            <!-- Basic Information -->
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label for="section" class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                  <input
                    id="section"
                    v-model="form.section"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.section }"
                    required
                  />
                  <p v-if="form.errors.section" class="mt-1 text-sm text-red-600">{{ form.errors.section }}</p>
                </div>

                <div>
                  <label for="row_no" class="block text-sm font-medium text-gray-700 mb-2">Row No</label>
                  <input
                    id="row_no"
                    v-model="form.row_no"
                    type="number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.row_no }"
                    required
                  />
                  <p v-if="form.errors.row_no" class="mt-1 text-sm text-red-600">{{ form.errors.row_no }}</p>
                </div>

                <div>
                  <label for="grave_no" class="block text-sm font-medium text-gray-700 mb-2">Grave No</label>
                  <input
                    id="grave_no"
                    v-model="form.grave_no"
                    type="number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.grave_no }"
                    required
                  />
                  <p v-if="form.errors.grave_no" class="mt-1 text-sm text-red-600">{{ form.errors.grave_no }}</p>
                </div>
              </div>
            </div>

            <!-- Status and Details -->
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Status & Details</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.status }"
                    required
                  >
                    <option value="">Select Status</option>
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="reserved">Reserved</option>
                    <option value="maintenance">Maintenance</option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                </div>

                <div>
                  <label for="plot_size" class="block text-sm font-medium text-gray-700 mb-2">Plot Size (sq ft)</label>
                  <input
                    id="plot_size"
                    v-model="form.plot_size"
                    type="number"
                    step="0.01"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.plot_size }"
                  />
                  <p v-if="form.errors.plot_size" class="mt-1 text-sm text-red-600">{{ form.errors.plot_size }}</p>
                </div>

                <div>
                  <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">Owner Name</label>
                  <input
                    id="owner_name"
                    v-model="form.owner_name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.owner_name }"
                  />
                  <p v-if="form.errors.owner_name" class="mt-1 text-sm text-red-600">{{ form.errors.owner_name }}</p>
                </div>
              </div>

              <div class="mt-4">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                <textarea
                  id="remarks"
                  v-model="form.remarks"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :class="{ 'border-red-500': form.errors.remarks }"
                ></textarea>
                <p v-if="form.errors.remarks" class="mt-1 text-sm text-red-600">{{ form.errors.remarks }}</p>
              </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="oldno" class="block text-sm font-medium text-gray-700 mb-2">Old Number</label>
                  <input
                    id="oldno"
                    v-model="form.oldno"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.oldno }"
                  />
                  <p v-if="form.errors.oldno" class="mt-1 text-sm text-red-600">{{ form.errors.oldno }}</p>
                </div>

                <div>
                  <label for="last_burial_date" class="block text-sm font-medium text-gray-700 mb-2">Last Burial Date</label>
                  <input
                    id="last_burial_date"
                    v-model="form.last_burial_date"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': form.errors.last_burial_date }"
                  />
                  <p v-if="form.errors.last_burial_date" class="mt-1 text-sm text-red-600">{{ form.errors.last_burial_date }}</p>
                </div>
              </div>

              <div class="mt-4">
                <label class="flex items-center">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
              <Link
                :href="route('graveyard.temporary-graves.index')"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
              >
                {{ form.processing ? 'Updating...' : 'Update Temporary Grave' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
  layout: AppLayout
});

interface Props {
  temporaryGrave: any;
}

const props = defineProps<Props>();

const form = useForm({
  section: props.temporaryGrave.section || '',
  row_no: props.temporaryGrave.row_no || '',
  grave_no: props.temporaryGrave.grave_no || '',
  status: props.temporaryGrave.status || '',
  plot_size: props.temporaryGrave.plot_size || '',
  owner_name: props.temporaryGrave.owner_name || '',
  remarks: props.temporaryGrave.remarks || '',
  oldno: props.temporaryGrave.oldno || '',
  last_burial_date: props.temporaryGrave.last_burial_date || '',
  is_active: props.temporaryGrave.is_active !== undefined ? props.temporaryGrave.is_active : true,
});

const submit = () => {
  form.put(route('graveyard.temporary-graves.update', props.temporaryGrave.id));
};
</script>
