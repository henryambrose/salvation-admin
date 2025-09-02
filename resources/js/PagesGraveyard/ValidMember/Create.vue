<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Create Valid Members" />
    
    <div class="mx-auto max-w-4xl">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add Valid Members</h1>
        <p class="mt-2 text-gray-600">Add up to 5 members who are authorized to use this grave.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Grave Selection -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="mb-4 text-lg font-semibold text-gray-900">Select Grave</h2>
          
          <div class="space-y-4">
            <!-- Grave Type Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Grave Type</label>
              <div class="flex gap-4">
                <label class="flex items-center">
                  <input
                    v-model="form.grave_type"
                    type="radio"
                    value="permanent_grave"
                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm text-gray-700">Permanent Grave</span>
                </label>
                <label class="flex items-center">
                  <input
                    v-model="form.grave_type"
                    type="radio"
                    value="niche"
                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                  />
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
                <option 
                  v-for="grave in permanentGraves" 
                  :key="grave.id" 
                  :value="grave.id"
                >
                  G{{ grave.grave_no }} - Section {{ grave.section }}, Row {{ grave.row_no }}
                </option>
              </select>

              <select
                v-if="form.grave_type === 'niche'"
                v-model="form.niche_id"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">Choose a niche...</option>
                <option 
                  v-for="niche in niches" 
                  :key="niche.id" 
                  :value="niche.id"
                >
                  N{{ niche.niche_no }} - {{ niche.location }}
                </option>
              </select>

              <div v-if="form.errors.permanent_grave_id || form.errors.niche_id" class="text-sm text-red-600">
                {{ form.errors.permanent_grave_id || form.errors.niche_id }}
              </div>
            </div>
          </div>
        </div>

        <!-- Members Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Valid Members</h2>
            <div class="text-sm text-gray-600">
              {{ form.members.length }} of 5 members added
            </div>
          </div>

          <div class="space-y-4">
            <!-- Member Forms -->
            <div
              v-for="(member, index) in form.members"
              :key="index"
              class="relative rounded-lg border border-gray-200 p-4 bg-gray-50"
            >
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-medium text-gray-900">Member {{ index + 1 }}</h3>
                <button
                  v-if="form.members.length > 1"
                  type="button"
                  @click="removeMember(index)"
                  class="rounded-full bg-red-100 p-1 text-red-600 hover:bg-red-200 transition"
                >
                  <X class="h-4 w-4" />
                </button>
              </div>

              <!-- Member Type Selection -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Member Type</label>
                <div class="flex gap-4">
                  <label class="flex items-center">
                    <input
                      v-model="member.member_type"
                      type="radio"
                      value="parish"
                      class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700">Parish Member</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      v-model="member.member_type"
                      type="radio"
                      value="external"
                      class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700">External Person</span>
                  </label>
                </div>
                <div v-if="form.errors[`members.${index}.member_type`]" class="mt-1 text-sm text-red-600">
                  {{ form.errors[`members.${index}.member_type`] }}
                </div>
              </div>

              <!-- Parish Member Selection -->
              <div v-if="member.member_type === 'parish'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Search Parish Member</label>
                  <div class="relative">
                    <input
                      v-model="member.member_search"
                      @input="searchParishMembers(index, $event.target.value)"
                      type="text"
                      placeholder="Type to search members..."
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                    
                    <!-- Search Results Dropdown -->
                    <div
                      v-if="member.member_search && member.search_results && member.search_results.length > 0"
                      class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto"
                    >
                      <button
                        v-for="result in member.search_results"
                        :key="result.id"
                        type="button"
                        @click="selectParishMember(index, result)"
                        class="w-full px-3 py-2 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none"
                      >
                        <div class="font-medium">{{ result.name }}</div>
                        <div class="text-sm text-gray-500">
                          Family: {{ result.family_number }} | Contact: {{ result.contact_no || 'N/A' }}
                        </div>
                      </button>
                    </div>
                  </div>
                  <div v-if="form.errors[`members.${index}.member_id`]" class="mt-1 text-sm text-red-600">
                    {{ form.errors[`members.${index}.member_id`] }}
                  </div>
                </div>

                <!-- Selected Parish Member Display -->
                <div v-if="member.selected_member" class="p-3 bg-blue-50 rounded-md">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="font-medium text-blue-900">{{ member.selected_member.name }}</div>
                      <div class="text-sm text-blue-700">
                        Family: {{ member.selected_member.family_number }}
                      </div>
                    </div>
                    <button
                      type="button"
                      @click="clearParishMemberSelection(index)"
                      class="text-blue-600 hover:text-blue-800"
                    >
                      <X class="h-4 w-4" />
                    </button>
                  </div>
                </div>

                <!-- Optional Contact Override -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Contact Number (optional override)
                  </label>
                  <input
                    v-model="member.contact_no"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Override contact number if needed"
                  />
                </div>
              </div>

              <!-- External Member Form -->
              <div v-if="member.member_type === 'external'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                  <input
                    v-model="member.first_name"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-300': form.errors[`members.${index}.first_name`] }"
                  />
                  <div v-if="form.errors[`members.${index}.first_name`]" class="mt-1 text-sm text-red-600">
                    {{ form.errors[`members.${index}.first_name`] }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                  <input
                    v-model="member.last_name"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-300': form.errors[`members.${index}.last_name`] }"
                  />
                  <div v-if="form.errors[`members.${index}.last_name`]" class="mt-1 text-sm text-red-600">
                    {{ form.errors[`members.${index}.last_name`] }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                  <input
                    v-model="member.contact_no"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Aadhar Number</label>
                  <input
                    v-model="member.aadhar_no"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Optional"
                  />
                </div>
              </div>
            </div>

            <!-- Add Member Button -->
            <div v-if="form.members.length < 5" class="text-center">
              <button
                type="button"
                @click="addMember"
                class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                <Plus class="h-4 w-4" />
                Add Another Member
              </button>
            </div>

            <div v-if="form.errors.members" class="text-sm text-red-600">
              {{ form.errors.members }}
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4">
          <Button
            type="button"
            variant="outline"
            @click="router.visit('/graveyard/valid-members')"
          >
            Cancel
          </Button>
          <Button
            type="submit"
            :disabled="form.processing"
            class="bg-blue-600 hover:bg-blue-700"
          >
            <span v-if="form.processing">Creating...</span>
            <span v-else>Create Valid Members</span>
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
import { Plus, X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
  permanentGraves: Array<{
    id: number;
    grave_no: string;
    location: string;
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
  { title: 'Create', href: '/graveyard/valid-members/create' },
];

// Form data
const form = useForm({
  grave_type: '',
  permanent_grave_id: '',
  niche_id: '',
  members: [
    {
      member_type: '',
      member_id: '',
      member_search: '',
      search_results: [],
      selected_member: null,
      first_name: '',
      last_name: '',
      contact_no: '',
      aadhar_no: '',
    }
  ]
});

let searchTimeout: any = null;

// Add member
const addMember = () => {
  if (form.members.length < 5) {
    form.members.push({
      member_type: '',
      member_id: '',
      member_search: '',
      search_results: [],
      selected_member: null,
      first_name: '',
      last_name: '',
      contact_no: '',
      aadhar_no: '',
    });
  }
};

// Remove member
const removeMember = (index: number) => {
  form.members.splice(index, 1);
};

// Search parish members
const searchParishMembers = (index: number, query: string) => {
  const member = form.members[index];
  member.member_search = query;
  
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  
  if (query.length < 2) {
    member.search_results = [];
    return;
  }
  
  searchTimeout = setTimeout(async () => {
    try {
      const response = await fetch(`/graveyard/valid-members/search-members?search=${encodeURIComponent(query)}`);
      const results = await response.json();
      member.search_results = results;
    } catch (error) {
      console.error('Error searching members:', error);
      member.search_results = [];
    }
  }, 300);
};

// Select parish member
const selectParishMember = (index: number, selectedMember: any) => {
  const member = form.members[index];
  member.selected_member = selectedMember;
  member.member_id = selectedMember.id;
  member.member_search = selectedMember.name;
  member.search_results = [];
  member.contact_no = selectedMember.contact_no || '';
};

// Clear parish member selection
const clearParishMemberSelection = (index: number) => {
  const member = form.members[index];
  member.selected_member = null;
  member.member_id = '';
  member.member_search = '';
  member.search_results = [];
  member.contact_no = '';
};

// Submit form
const submitForm = () => {
  // Prepare the data for submission
  const submitData = {
    grave_type: form.grave_type,
    permanent_grave_id: form.grave_type === 'permanent_grave' ? form.permanent_grave_id : null,
    niche_id: form.grave_type === 'niche' ? form.niche_id : null,
    members: form.members.map(member => ({
      member_type: member.member_type,
      member_id: member.member_type === 'parish' ? member.member_id : null,
      first_name: member.member_type === 'external' ? member.first_name : '',
      last_name: member.member_type === 'external' ? member.last_name : '',
      contact_no: member.contact_no || null,
      aadhar_no: member.member_type === 'external' ? (member.aadhar_no || null) : null,
    }))
  };
  
  form.transform(() => submitData).post('/graveyard/valid-members', {
    onSuccess: () => {
      // Success is handled by the redirect
    },
    onError: (errors) => {
      console.error('Form errors:', errors);
    }
  });
};
</script>

<style scoped>
/* Custom styles for better form appearance */
</style>