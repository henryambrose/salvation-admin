<template>
  <div class="py-6">
    <div class="mx-auto max-w-7xl">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-blue-700">Niche Details</h1>
            <div class="flex space-x-3">
              <Link
                :href="route('graveyard.niches.edit', niche.id)"
                class="inline-flex items-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-yellow-700 focus:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:outline-none active:bg-yellow-900"
              >
                Edit
              </Link>
              <Link
                :href="route('graveyard.niches.index')"
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
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Status & Details</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                      <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" :class="getStatusClass(niche.status)">
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
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Dimensions</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
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
              <div v-if="niche.member" class="rounded-lg bg-blue-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Associated Member</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
            <div class="space-y-6 lg:col-span-1">
              <!-- Quick Actions -->
              <div class="rounded-lg bg-blue-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Quick Actions</h3>
                <div class="space-y-3">
                  <Link
                    :href="route('graveyard.niches.edit', niche.id)"
                    class="flex w-full items-center justify-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 focus:outline-none"
                  >
                    Edit Niche
                  </Link>
                  <button
                    @click="deleteNiche"
                    class="flex w-full items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:outline-none"
                  >
                    Delete Niche
                  </button>
                </div>
              </div>

              <!-- Niche Information -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Niche Information</h3>
                <div class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Full Identifier</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">N{{ niche.niche_no }}-{{ niche.sr_no }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Active Status</label>
                    <p class="mt-1">
                      <span
                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                        :class="niche.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                      >
                        {{ niche.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-500">Available</label>
                    <p class="mt-1">
                      <span
                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                        :class="isAvailable() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                      >
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
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Audit Trail</h3>
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
    <div v-if="showDeleteModal" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black">
      <div class="mx-4 w-full max-w-[448px] rounded-lg bg-white shadow-xl">
        <div class="p-6">
          <div class="mb-4 flex items-center">
            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <AlertTriangle class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Niche</h3>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-sm text-gray-500">Are you sure you want to delete this niche? This action cannot be undone.</p>
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
