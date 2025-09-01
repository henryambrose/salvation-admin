<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-700">Permanent Grave Details</h1>
            <div class="flex space-x-3">
              <Link 
                :href="route('graveyard.permanent-graves.edit', permanentGrave.id)"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Edit
              </Link>
              <Link 
                :href="route('graveyard.permanent-graves.index')"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Back to List
              </Link>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Basic Information -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Section</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.section }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Row No</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.row_no }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Grave No</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.grave_no }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Old Number</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.oldno || 'N/A' }}</p>
                  </div>
                </div>
              </div>

              <!-- Status and Details -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status & Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(permanentGrave.status)">
                        {{ permanentGrave.status.charAt(0).toUpperCase() + permanentGrave.status.slice(1) }}
                      </span>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Plot Size</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.plot_size ? `${permanentGrave.plot_size} sq ft` : 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Owner Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.owner_name || 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Last Burial Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.last_burial_date ? formatDate(permanentGrave.last_burial_date) : 'N/A' }}</p>
                  </div>
                </div>
                <div class="mt-4">
                  <label class="block text-sm font-medium text-gray-500">Remarks</label>
                  <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.remarks || 'No remarks' }}</p>
                </div>
              </div>

              <!-- Burial History -->
              <div v-if="permanentGrave.bookings && permanentGrave.bookings.length > 0" class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Burial History</h3>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deceased Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Death</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Burial Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="booking in permanentGrave.bookings" :key="booking.id" class="hover:bg-blue-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                          {{ getDeceasedName(booking) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                          {{ booking.died_on ? formatDate(booking.died_on) : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                          {{ booking.buried_on ? formatDate(booking.buried_on) : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                          {{ getAgeDisplay(booking) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
              <!-- Quick Actions -->
              <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                  <Link
                    :href="route('graveyard.permanent-graves.edit', permanentGrave.id)"
                    class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                  >
                    Edit Grave
                  </Link>
                  <button
                    @click="deleteGrave"
                    class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                  >
                    Delete Grave
                  </button>
                </div>
              </div>

              <!-- Grave Information -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Grave Information</h3>
                <div class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Full Identifier</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ permanentGrave.section }}-R{{ permanentGrave.row_no }}-G{{ permanentGrave.grave_no }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Active Status</label>
                    <p class="mt-1">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="permanentGrave.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ permanentGrave.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.created_at ? formatDate(permanentGrave.created_at) : 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.updated_at ? formatDate(permanentGrave.updated_at) : 'N/A' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <AlertTriangle class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Permanent Grave</h3>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              Are you sure you want to delete this permanent grave? This action cannot be undone.
            </p>
          </div>
          <div class="mt-4 flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              Cancel
            </button>
            <button
              @click="confirmDelete"
              class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { AlertTriangle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
  layout: AppLayout
});

interface Props {
  permanentGrave: any;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const deleteGrave = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(route('graveyard.permanent-graves.destroy', props.permanentGrave.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
};

const getStatusClass = (status: string) => {
  const classes = {
    available: 'bg-green-100 text-green-800',
    occupied: 'bg-red-100 text-red-800',
    reserved: 'bg-yellow-100 text-yellow-800',
    maintenance: 'bg-gray-100 text-gray-800',
  };
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-IN');
};

const getDeceasedName = (booking: any) => {
  if (booking.member) {
    return `${booking.member.first_name} ${booking.member.last_name}`;
  }
  return booking.dead_first_name && booking.dead_last_name 
    ? `${booking.dead_first_name} ${booking.dead_last_name}`
    : 'N/A';
};

const getAgeDisplay = (booking: any) => {
  if (booking.age) {
    let ageText = `${booking.age} years`;
    if (booking.months) ageText += ` ${booking.months} months`;
    if (booking.days) ageText += ` ${booking.days} days`;
    return ageText;
  }
  return 'N/A';
};
</script>
