<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Edit Valid Member" />

    <div class="mx-auto max-w-2xl">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Valid Member</h1>
        <p class="mt-2 text-gray-600">Update the details of this valid member.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Grave Selection -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="mb-4 text-lg font-semibold text-gray-900">Grave Details</h2>

          <div class="space-y-4">
            <!-- Grave Type Selection -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700">Grave Type</label>
              <div class="flex gap-4">
                <label class="flex items-center">
                  <input
                    v-model="form.grave_type"
                    type="radio"
                    value="permanent_grave"
                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm text-gray-700">Permanent Grave</span>
                </label>
                <label class="flex items-center">
                  <input v-model="form.grave_type" type="radio" value="niche" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500" />
                  <span class="ml-2 text-sm text-gray-700">Niche</span>
                </label>
              </div>
              <div v-if="form.errors.grave_type" class="mt-1 text-sm text-red-600">
                {{ form.errors.grave_type }}
              </div>
            </div>

            <!-- Grave Selection -->
            <div v-if="form.grave_type" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                {{ form.grave_type === 'permanent_grave' ? 'Select Permanent Grave' : 'Select Niche' }}
              </label>

              <select
                v-if="form.grave_type === 'permanent_grave'"
                v-model="form.permanent_grave_id"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">Choose a permanent grave...</option>
                <option v-for="grave in permanentGraves" :key="grave.id" :value="grave.id">
                  G{{ grave.grave_no }} - Section {{ grave.section }}, Row {{ grave.row_no }}
                </option>
              </select>

              <select
                v-if="form.grave_type === 'niche'"
                v-model="form.niche_id"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">Choose a niche...</option>
                <option v-for="niche in niches" :key="niche.id" :value="niche.id">N{{ niche.niche_no }} - {{ niche.location }}</option>
              </select>

              <div v-if="form.errors.permanent_grave_id || form.errors.niche_id" class="text-sm text-red-600">
                {{ form.errors.permanent_grave_id || form.errors.niche_id }}
              </div>
            </div>
          </div>
        </div>

        <!-- Member Details -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="mb-4 text-lg font-semibold text-gray-900">Member Details</h2>

          <!-- Member Type Selection -->
          <div class="mb-6">
            <label class="mb-2 block text-sm font-medium text-gray-700">Member Type</label>
            <div class="flex gap-4">
              <label class="flex items-center">
                <input v-model="form.member_type" type="radio" value="parish" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span class="ml-2 text-sm text-gray-700">Parish Member</span>
              </label>
              <label class="flex items-center">
                <input v-model="form.member_type" type="radio" value="external" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500" />
                <span class="ml-2 text-sm text-gray-700">External Person</span>
              </label>
            </div>
            <div v-if="form.errors.member_type" class="mt-1 text-sm text-red-600">
              {{ form.errors.member_type }}
            </div>
          </div>

          <!-- Parish Member Selection -->
          <div v-if="form.member_type === 'parish'" class="space-y-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700">Search Parish Member</label>
              <div class="relative">
                <input
                  v-model="memberSearch"
                  @input="searchParishMembers(($event.target as HTMLInputElement).value)"
                  type="text"
                  placeholder="Type to search members..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />

                <!-- Search Results Dropdown -->
                <div
                  v-if="memberSearch && searchResults.length > 0"
                  class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-lg"
                >
                  <button
                    v-for="result in searchResults"
                    :key="result.id"
                    type="button"
                    @click="selectParishMember(result)"
                    class="w-full px-3 py-2 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none"
                  >
                    <div class="font-medium">{{ result.name }}</div>
                    <div class="text-sm text-gray-500">Family: {{ result.family_number }} | Contact: {{ result.contact_no || 'N/A' }}</div>
                  </button>
                </div>
              </div>
              <div v-if="form.errors.member_id" class="mt-1 text-sm text-red-600">
                {{ form.errors.member_id }}
              </div>
            </div>

            <!-- Selected Parish Member Display -->
            <div v-if="selectedMember" class="rounded-md bg-blue-50 p-3">
              <div class="flex items-center justify-between">
                <div>
                  <div class="font-medium text-blue-900">{{ selectedMember.name }}</div>
                  <div class="text-sm text-blue-700">Family: {{ selectedMember.family_number }}</div>
                </div>
                <button type="button" @click="clearParishMemberSelection" class="text-blue-600 hover:text-blue-800">
                  <X class="h-4 w-4" />
                </button>
              </div>
            </div>

            <!-- Optional Contact Override -->
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700"> Contact Number (optional override) </label>
              <input
                v-model="form.contact_no"
                type="text"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Override contact number if needed"
              />
            </div>
          </div>

          <!-- External Member Form -->
          <div v-if="form.member_type === 'external'" class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700">First Name *</label>
              <input
                v-model="form.first_name"
                type="text"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.first_name }"
              />
              <div v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">
                {{ form.errors.first_name }}
              </div>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700">Last Name *</label>
              <input
                v-model="form.last_name"
                type="text"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.last_name }"
              />
              <div v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">
                {{ form.errors.last_name }}
              </div>
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
              <input
                v-model="form.contact_no"
                type="text"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700">Aadhar Number</label>
              <input
                v-model="form.aadhar_no"
                type="text"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Optional"
              />
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4">
          <Button type="button" variant="outline" @click="router.visit('/graveyard/valid-members')"> Cancel </Button>
          <Button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700">
            <span v-if="form.processing">Updating...</span>
            <span v-else>Update Valid Member</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Props {
  validMember: {
    id: number;
    permanent_grave_id: number | null;
    niche_id: number | null;
    member_id: number | null;
    first_name: string;
    last_name: string;
    contact_no: string | null;
    aadhar_no: string | null;
    member?: {
      id: number;
      first_name: string;
      last_name: string;
      family_number: string;
      contact_no: string | null;
    };
    permanent_grave?: {
      id: number;
      grave_no: string;
      location: string;
    };
    niche?: {
      id: number;
      niche_no: string;
      location: string;
    };
  };
  permanentGraves: Array<{
    id: number;
    grave_no: string;
    location: string;
    section: string;
    row_no: string;
  }>;
  niches: Array<{
    id: number;
    niche_no: string;
    location: string;
  }>;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Valid Members', href: '/graveyard/valid-members' },
  { title: 'Edit', href: `/graveyard/valid-members/${props.validMember.id}/edit` },
];

// Form data
const form = useForm({
  grave_type: props.validMember.permanent_grave_id ? 'permanent_grave' : 'niche',
  permanent_grave_id: props.validMember.permanent_grave_id || '',
  niche_id: props.validMember.niche_id || '',
  member_type: props.validMember.member_id ? 'parish' : 'external',
  member_id: props.validMember.member_id || '',
  first_name: props.validMember.first_name || '',
  last_name: props.validMember.last_name || '',
  contact_no: props.validMember.contact_no || '',
  aadhar_no: props.validMember.aadhar_no || '',
});

// Search functionality
const memberSearch = ref('');
const searchResults = ref<Array<{ id: number; name: string; family_number: string; contact_no?: string }>>([]);
const selectedMember = ref<{
  id: number;
  name: string;
  family_number: string;
  contact_no: string | null;
} | null>(null);
let searchTimeout: any = null;

// Initialize with existing parish member if applicable
onMounted(() => {
  if (props.validMember.member && props.validMember.member_id) {
    selectedMember.value = {
      id: props.validMember.member.id,
      name: `${props.validMember.member.first_name} ${props.validMember.member.last_name}`,
      family_number: props.validMember.member.family_number,
      contact_no: props.validMember.member.contact_no,
    };
    memberSearch.value = selectedMember.value ? selectedMember.value.name : '';
  }
});

// Search parish members
const searchParishMembers = (query: string) => {
  memberSearch.value = query;

  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  if (query.length < 2) {
    searchResults.value = [];
    return;
  }

  searchTimeout = setTimeout(async () => {
    try {
      const response = await fetch(`/graveyard/valid-members/search-members?search=${encodeURIComponent(query)}`);
      const results = await response.json();
      searchResults.value = results;
    } catch (error) {
      console.error('Error searching members:', error);
      searchResults.value = [];
    }
  }, 300);
};

// Select parish member
const selectParishMember = (member: any) => {
  selectedMember.value = member;
  form.member_id = member.id;
  memberSearch.value = member.name;
  searchResults.value = [];
  form.contact_no = member.contact_no || '';
};

// Clear parish member selection
const clearParishMemberSelection = () => {
  selectedMember.value = null;
  form.member_id = '';
  memberSearch.value = '';
  searchResults.value = [];
  form.contact_no = '';
};

// Submit form
const submitForm = () => {
  // Prepare the data for submission
  const submitData = {
    grave_type: form.grave_type,
    permanent_grave_id: form.grave_type === 'permanent_grave' ? form.permanent_grave_id : null,
    niche_id: form.grave_type === 'niche' ? form.niche_id : null,
    member_type: form.member_type,
    member_id: form.member_type === 'parish' ? form.member_id : null,
    first_name: form.member_type === 'external' ? form.first_name : '',
    last_name: form.member_type === 'external' ? form.last_name : '',
    contact_no: form.contact_no || null,
    aadhar_no: form.member_type === 'external' ? form.aadhar_no || null : null,
  };

  form
    .transform(() => submitData)
    .put(`/graveyard/valid-members/${props.validMember.id}`, {
      onSuccess: () => {
        // Success is handled by the redirect
      },
      onError: (errors) => {
        console.error('Form errors:', errors);
      },
    });
};
</script>

<style scoped>
/* Custom styles for better form appearance */
</style>
