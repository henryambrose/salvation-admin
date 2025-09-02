<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-700">Niche Details</h1>
            <div class="flex space-x-3">
              <Link 
                :href="route('graveyard.niches.edit', niche.id)"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Edit
              </Link>
              <Link 
                :href="route('graveyard.niches.index')"
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
                    <label class="block text-sm font-medium text-gray-500">Niche No</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.niche_no }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Sr No</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.sr_no }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Location</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.location }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Full Identifier</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900">N{{ niche.niche_no }}-{{ niche.sr_no }}</p>
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
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(niche.status)">
                        {{ niche.status.charAt(0).toUpperCase() + niche.status.slice(1) }}
                      </span>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Last Occupation Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.last_occupation_date ? formatDate(niche.last_occupation_date) : 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Owner Name</label>
                    <p class="mt-1 text-sm text-gray-900">
                      {{ niche.owner_name || (niche.member ? `${niche.member.first_name} ${niche.member.last_name}` : 'N/A') }}
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Contact Number</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.contact_no || 'N/A' }}</p>
                  </div>
                </div>
                <div class="mt-4">
                  <label class="block text-sm font-medium text-gray-500">Remarks</label>
                  <p class="mt-1 text-sm text-gray-900">{{ niche.remarks || 'No remarks' }}</p>
                </div>
              </div>

              <!-- Dimensions -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dimensions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Width</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.size_width ? `${niche.size_width}"` : 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Height</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.size_height ? `${niche.size_height}"` : 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Depth</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.size_depth ? `${niche.size_depth}"` : 'N/A' }}</p>
                  </div>
                </div>
                <div class="mt-4" v-if="getTotalVolume()">
                  <label class="block text-sm font-medium text-gray-500">Total Volume</label>
                  <p class="mt-1 text-sm font-semibold text-gray-900">{{ getTotalVolume() }} cubic inches</p>
                </div>
              </div>

              <!-- Member Information (if applicable) -->
              <div v-if="niche.member" class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Associated Member</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.member.first_name }} {{ niche.member.last_name }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Family No</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.member.family_no || 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Contact</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.member.contact_no_1 || 'N/A' }}</p>
                  </div>
                  <div v-if="niche.member.community">
                    <label class="block text-sm font-medium text-gray-500">Community</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.member.community.name || 'N/A' }}</p>
                  </div>
                </div>
                <div class="mt-4" v-if="niche.member.current_add1">
                  <label class="block text-sm font-medium text-gray-500">Address</label>
                  <p class="mt-1 text-sm text-gray-900">{{ niche.member.current_add1 }}</p>
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
                    :href="route('graveyard.niches.edit', niche.id)"
                    class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                  >
                    Edit Niche
                  </Link>
                  <button
                    @click="deleteNiche"
                    class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                  >
                    Delete Niche
                  </button>
                </div>
              </div>

              <!-- Niche Information -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Niche Information</h3>
                <div class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Full Identifier</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">N{{ niche.niche_no }}-{{ niche.sr_no }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Active Status</label>
                    <p class="mt-1">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="niche.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ niche.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Available</label>
                    <p class="mt-1">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="isAvailable() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        {{ isAvailable() ? 'Yes' : 'No' }}
                      </span>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.created_at ? formatDate(niche.created_at) : 'N/A' }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.updated_at ? formatDate(niche.updated_at) : 'N/A' }}</p>
                  </div>
                </div>
              </div>

              <!-- Created/Updated By -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Audit Trail</h3>
                <div class="space-y-3">
                  <div v-if="niche.creator">
                    <label class="block text-sm font-medium text-gray-500">Created By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.creator.name }}</p>
                  </div>
                  <div v-if="niche.updater">
                    <label class="block text-sm font-medium text-gray-500">Last Updated By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ niche.updater.name }}</p>
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
              <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Niche</h3>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              Are you sure you want to delete this niche? This action cannot be undone.
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
  niche: any;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const deleteNiche = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(route('graveyard.niches.destroy', props.niche.id), {
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
    maintenance: 'bg-orange-100 text-orange-800',
  };
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-IN');
};

const getTotalVolume = () => {
  if (props.niche.size_width && props.niche.size_height && props.niche.size_depth) {
    const volume = parseFloat(props.niche.size_width) * parseFloat(props.niche.size_height) * parseFloat(props.niche.size_depth);
    return volume.toFixed(2);
  }
  return null;
};

const isAvailable = () => {
  return props.niche.status === 'available' && props.niche.is_active;
};
</script>