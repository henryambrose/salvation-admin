<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-700">Create Permanent Grave</h1>
            <Link 
              :href="route('graveyard.permanent-graves.index')"
              class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Back to List
            </Link>
          </div>

          <div class="max-w-4xl mx-auto">
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Basic Information Section -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Section *</label>
                    <input 
                      v-model="form.section" 
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Enter section name"
                      required
                    />
                    <p v-if="errors.section" class="mt-1 text-sm text-red-600">{{ errors.section }}</p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Row No *</label>
                    <input 
                      v-model="form.row_no" 
                      type="number" 
                      min="1"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Enter row number"
                      required
                    />
                    <p v-if="errors.row_no" class="mt-1 text-sm text-red-600">{{ errors.row_no }}</p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grave No *</label>
                    <input 
                      v-model="form.grave_no" 
                      type="number" 
                      min="1"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Enter grave number"
                      required
                    />
                    <p v-if="errors.grave_no" class="mt-1 text-sm text-red-600">{{ errors.grave_no }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Old Number</label>
                    <input 
                      v-model="form.oldno" 
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Enter old grave number"
                    />
                    <p v-if="errors.oldno" class="mt-1 text-sm text-red-600">{{ errors.oldno }}</p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select 
                      v-model="form.status" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required
                    >
                      <option value="">Select Status</option>
                      <option v-for="status in statuses" :key="status" :value="status">
                        {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                      </option>
                    </select>
                    <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
                  </div>
                </div>
              </div>

              <!-- Details Section -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Grave Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Owner Name</label>
                    <input 
                      v-model="form.owner_name" 
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Enter owner name"
                    />
                    <p v-if="errors.owner_name" class="mt-1 text-sm text-red-600">{{ errors.owner_name }}</p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plot Size (sq ft)</label>
                    <input 
                      v-model="form.plot_size" 
                      type="number" 
                      step="0.01"
                      min="0"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Enter plot size"
                    />
                    <p v-if="errors.plot_size" class="mt-1 text-sm text-red-600">{{ errors.plot_size }}</p>
                  </div>
                </div>

                <div class="mt-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Last Burial Date</label>
                  <input 
                    v-model="form.last_burial_date" 
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                  <p v-if="errors.last_burial_date" class="mt-1 text-sm text-red-600">{{ errors.last_burial_date }}</p>
                </div>

                <div class="mt-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                  <textarea 
                    v-model="form.remarks" 
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter any additional remarks"
                  ></textarea>
                  <p v-if="errors.remarks" class="mt-1 text-sm text-red-600">{{ errors.remarks }}</p>
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
                  <p v-if="errors.is_active" class="mt-1 text-sm text-red-600">{{ errors.is_active }}</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-3">
                <Link 
                  :href="route('graveyard.permanent-graves.index')"
                  class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  Cancel
                </Link>
                <button 
                  type="submit" 
                  :disabled="submitting"
                  class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ submitting ? 'Creating...' : 'Create Permanent Grave' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
  layout: AppLayout
});

interface Props {
  sections: string[];
  statuses: string[];
  errors?: any;
}

const props = defineProps<Props>();

const submitting = ref(false);

const form = useForm({
  section: '',
  row_no: '',
  grave_no: '',
  oldno: '',
  status: '',
  last_burial_date: '',
  owner_name: '',
  remarks: '',
  plot_size: '',
  is_active: true,
});

const submitForm = () => {
  submitting.value = true;
  
  form.post(route('graveyard.permanent-graves.store'), {
    onSuccess: () => {
      submitting.value = false;
    },
    onError: () => {
      submitting.value = false;
    },
  });
};
</script>
