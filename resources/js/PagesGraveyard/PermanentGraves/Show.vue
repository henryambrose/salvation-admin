<template>
  <div class="py-6">
    <div class="mx-auto max-w-7xl">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-blue-700">Permanent Grave Details</h1>
            <div class="flex space-x-3">
              <Link
                :href="route('graveyard.permanent-graves.edit', permanentGrave.id)"
                class="inline-flex items-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-yellow-700 focus:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:outline-none active:bg-yellow-900"
              >
                Edit
              </Link>
              <Link
                :href="route('graveyard.permanent-graves.index')"
                class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-900"
              >
                Back to List
              </Link>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main Information -->
            <div class="space-y-6 lg:col-span-2">
              <!-- Basic Information -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Basic Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                    <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.old_no || 'N/A' }}</p>
                  </div>
                </div>
              </div>

              <!-- Status and Details -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Status & Details</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                      <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="getStatusClass(permanentGrave.status)">
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
                    <p class="mt-1 text-sm text-gray-900">
                      {{ permanentGrave.last_burial_date ? formatDate(permanentGrave.last_burial_date) : 'N/A' }}
                    </p>
                  </div>
                </div>
                <div class="mt-4">
                  <label class="block text-sm font-medium text-gray-500">Remarks</label>
                  <p class="mt-1 text-sm text-gray-900">{{ permanentGrave.remarks || 'No remarks' }}</p>
                </div>
              </div>

              <!-- Burial History -->
              <div v-if="permanentGrave.bookings && permanentGrave.bookings.length > 0" class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Burial History</h3>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Deceased Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Date of Death</th>
                        <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Burial Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Age</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="booking in permanentGrave.bookings" :key="booking.id" class="hover:bg-blue-50">
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-900">
                          {{ getDeceasedName(booking) }}
                        </td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-500">
                          {{ booking.died_on ? formatDate(booking.died_on) : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-500">
                          {{ booking.buried_on ? formatDate(booking.buried_on) : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap text-gray-500">
                          {{ getAgeDisplay(booking) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 lg:col-span-1">
              <!-- Quick Actions -->
              <div class="rounded-lg bg-blue-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Quick Actions</h3>
                <div class="space-y-3">
                  <Link
                    :href="route('graveyard.permanent-graves.edit', permanentGrave.id)"
                    class="flex w-full items-center justify-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 focus:outline-none"
                  >
                    Edit Grave
                  </Link>
                  <button
                    @click="deleteGrave"
                    class="flex w-full items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:outline-none"
                  >
                    Delete Grave
                  </button>
                </div>
              </div>

              <!-- Grave Information -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Grave Information</h3>
                <div class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Full Identifier</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                      {{ permanentGrave.section }}-R{{ permanentGrave.row_no }}-G{{ permanentGrave.grave_no }}
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Active Status</label>
                    <p class="mt-1">
                      <span
                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                        :class="permanentGrave.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                      >
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
    <div v-if="showDeleteModal" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black">
      <div class="mx-4 w-full max-w-[448px] rounded-lg bg-white shadow-xl">
        <div class="p-6">
          <div class="mb-4 flex items-center">
            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <AlertTriangle class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Permanent Grave</h3>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-sm text-gray-500">Are you sure you want to delete this permanent grave? This action cannot be undone.</p>
          </div>
          <div class="mt-4 flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >
              Cancel
            </button>
            <button
              @click="confirmDelete"
              class="rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:outline-none"
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
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { AlertTriangle } from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({
  layout: AppLayout,
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
  return booking.dead_first_name && booking.dead_last_name ? `${booking.dead_first_name} ${booking.dead_last_name}` : 'N/A';
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
