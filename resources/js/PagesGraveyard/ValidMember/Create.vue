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
              <label class="mb-2 block text-sm font-medium text-gray-700">Grave Type</label>
              <div class="flex gap-4">
                <label class="flex items-center">
                  <input
                    v-model="formData.grave_type"
                    type="radio"
                    value="permanent_grave"
                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="ml-2 text-sm text-gray-700">Permanent Grave</span>
                </label>
                <label class="flex items-center">
                  <input v-model="formData.grave_type" type="radio" value="niche" class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500" />
                  <span class="ml-2 text-sm text-gray-700">Niche</span>
                </label>
              </div>
              <div v-if="form.errors.grave_type" class="mt-1 text-sm text-red-600">
                {{ form.errors.grave_type }}
              </div>
            </div>

            <!-- Grave Selection -->
            <div v-if="formData.grave_type" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                {{ formData.grave_type === 'permanent_grave' ? 'Search Permanent Grave' : 'Search Niche' }}
              </label>

              <!-- Searchable Dropdown -->
              <div class="relative">
                <input
                  v-model="graveSearchTerm"
                  type="text"
                  :placeholder="
                    formData.grave_type === 'permanent_grave'
                      ? 'Search by owner, contact, grave number, member name...'
                      : 'Search by niche number, location, owner, contact, member name...'
                  "
                  class="w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  @input="debouncedSearchGraves"
                  @focus="showDropdown = true"
                />
                <div v-if="isSearching" class="absolute top-1/2 right-3 -translate-y-1/2 transform">
                  <div class="h-4 w-4 animate-spin rounded-full border-2 border-blue-600 border-t-transparent"></div>
                </div>

                <!-- Selected Grave Display -->
                <div v-if="selectedGrave" class="mt-2 rounded-md border border-green-200 bg-green-50 p-3">
                  <div class="flex items-start justify-between">
                    <div>
                      <h4 class="font-medium text-green-800">{{ selectedGrave.display_name }}</h4>
                      <p class="text-sm text-green-600">Owner: {{ selectedGrave.details.owner_name }}</p>
                      <p v-if="selectedGrave.details.member" class="text-sm text-blue-600">
                        Member: {{ selectedGrave.details.member.full_name }} ({{ selectedGrave.details.member.family_no }})
                      </p>
                    </div>
                    <button type="button" @click="clearSelection" class="text-green-600 hover:text-green-800">
                      <X class="h-4 w-4" />
                    </button>
                  </div>
                </div>

                <!-- Search Results Dropdown -->
                <div
                  v-if="showDropdown && searchResults.length > 0"
                  class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-md border border-gray-300 bg-white shadow-lg"
                >
                  <div
                    v-for="result in searchResults"
                    :key="result.id"
                    class="cursor-pointer border-b border-gray-100 p-3 last:border-b-0 hover:bg-gray-50"
                    @click="selectGrave(result)"
                  >
                    <h4 class="font-medium text-gray-900">{{ result.display_name }}</h4>
                    <p class="text-sm text-gray-600">Owner: {{ result.details.owner_name }}</p>
                    <p v-if="result.details.member" class="text-sm text-blue-600">
                      Member: {{ result.details.member.full_name }} ({{ result.details.member.family_no }})
                    </p>
                    <p v-if="result.details.contact_no" class="text-sm text-gray-500">Contact: {{ result.details.contact_no }}</p>
                  </div>
                </div>

                <!-- No Results -->
                <div
                  v-if="graveSearchTerm.length >= 2 && !isSearching && searchResults.length === 0 && showDropdown"
                  class="absolute z-10 mt-1 w-full rounded-md border border-gray-300 bg-white p-3 shadow-lg"
                >
                  <p class="text-sm text-gray-500">
                    No {{ formData.grave_type === 'permanent_grave' ? 'permanent graves' : 'niches' }} found for "{{ graveSearchTerm }}"
                  </p>
                </div>
              </div>

              <div v-if="form.errors.permanent_grave_id || form.errors.niche_id" class="text-sm text-red-600">
                {{ form.errors.permanent_grave_id || form.errors.niche_id }}
              </div>
            </div>
          </div>
        </div>

        <!-- Members Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Valid Members</h2>
            <div class="text-sm text-gray-600">{{ formData.members.length }} of 5 members added</div>
          </div>

          <div class="space-y-4">
            <!-- Member Forms -->
            <div v-for="(member, index) in formData.members" :key="index" class="relative rounded-lg border border-gray-200 bg-gray-50 p-4">
              <div class="mb-4 flex items-center justify-between">
                <h3 class="font-medium text-gray-900">Member {{ index + 1 }}</h3>
                <button
                  v-if="formData.members.length > 1"
                  type="button"
                  @click="removeMember(index)"
                  class="rounded-full bg-red-100 p-1 text-red-600 transition hover:bg-red-200"
                >
                  <X class="h-4 w-4" />
                </button>
              </div>

              <!-- Member Type Selection -->
              <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-gray-700">Member Type</label>
                <div class="flex gap-4">
                  <label class="flex items-center">
                    <input
                      v-model="member.member_type"
                      type="radio"
                      value="member"
                      class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700">Parish Member</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      v-model="member.member_type"
                      type="radio"
                      value="external"
                      class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700">External Person</span>
                  </label>
                </div>
                <div v-if="getFieldError(`members.${index}.member_type`)" class="mt-1 text-sm text-red-600">
                  {{ getFieldError(`members.${index}.member_type`) }}
                </div>
              </div>

              <!-- Parish Member Selection -->
              <div v-if="member.member_type === 'member'" class="space-y-4">
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">Search Parish Member</label>
                  <div class="relative">
                    <input
                      v-model="member.member_search"
                      @input="searchParishMembers(index, ($event.target as HTMLInputElement).value)"
                      type="text"
                      placeholder="Type to search members..."
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />

                    <!-- Search Results Dropdown -->
                    <div
                      v-if="member.member_search && member.search_results && member.search_results.length > 0"
                      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-200 bg-white shadow-lg"
                    >
                      <button
                        v-for="result in member.search_results"
                        :key="result.id"
                        type="button"
                        @click="selectParishMember(index, result)"
                        class="w-full px-3 py-2 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none"
                      >
                        <div class="font-medium">{{ result.name }}</div>
                        <div class="text-sm text-gray-500">Family: {{ result.family_number }} | Contact: {{ result.contact_no || 'N/A' }}</div>
                      </button>
                    </div>
                  </div>
                  <div v-if="getFieldError(`members.${index}.member_id`)" class="mt-1 text-sm text-red-600">
                    {{ getFieldError(`members.${index}.member_id`) }}
                  </div>
                </div>

                <!-- Selected Parish Member Display -->
                <div v-if="member.selected_member" class="rounded-md bg-blue-50 p-3">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="font-medium text-blue-900">{{ member.selected_member.name }}</div>
                      <div class="text-sm text-blue-700">Family: {{ member.selected_member.family_number }}</div>
                    </div>
                    <button type="button" @click="clearParishMemberSelection(index)" class="text-blue-600 hover:text-blue-800">
                      <X class="h-4 w-4" />
                    </button>
                  </div>
                </div>

                <!-- Optional Contact Override -->
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700"> Contact Number (optional override) </label>
                  <input
                    v-model="member.contact_no"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Override contact number if needed"
                  />
                </div>
              </div>

              <!-- External Member Form -->
              <div v-if="member.member_type === 'external'" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">First Name *</label>
                  <input
                    v-model="member.first_name"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-300': getFieldError(`members.${index}.first_name`) }"
                  />
                  <div v-if="getFieldError(`members.${index}.first_name`)" class="mt-1 text-sm text-red-600">
                    {{ getFieldError(`members.${index}.first_name`) }}
                  </div>
                </div>

                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">Last Name *</label>
                  <input
                    v-model="member.last_name"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-300': getFieldError(`members.${index}.last_name`) }"
                  />
                  <div v-if="getFieldError(`members.${index}.last_name`)" class="mt-1 text-sm text-red-600">
                    {{ getFieldError(`members.${index}.last_name`) }}
                  </div>
                </div>

                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
                  <input
                    v-model="member.contact_no"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  />
                </div>

                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700">Aadhar Number</label>
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
            <div v-if="formData.members.length < 5" class="text-center">
              <button
                type="button"
                @click="addMember"
                class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
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
          <Button type="button" variant="outline" @click="router.visit('/graveyard/valid-members')"> Cancel </Button>
          <Button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700">
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
import { reactive, ref, watch } from 'vue';

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

// Define interface for parish member search results
interface ParishMemberSearchResult {
  id: number;
  name: string;
  family_number: string;
  contact_no?: string;
}

// Define interface for member form data
interface MemberFormData {
  member_type: string;
  member_id: string;
  member_search: string;
  search_results: ParishMemberSearchResult[];
  selected_member: ParishMemberSearchResult | null;
  first_name: string;
  last_name: string;
  contact_no: string;
  aadhar_no: string;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Valid Members', href: '/graveyard/valid-members' },
  { title: 'Create', href: '/graveyard/valid-members/create' },
];

// Form data using reactive for better TypeScript support
const formData = reactive({
  grave_type: '',
  permanent_grave_id: '',
  niche_id: '',
  members: [
    {
      member_type: '',
      member_id: '',
      member_search: '',
      search_results: [] as ParishMemberSearchResult[],
      selected_member: null as ParishMemberSearchResult | null,
      first_name: '',
      last_name: '',
      contact_no: '',
      aadhar_no: '',
    },
  ] as MemberFormData[],
});

// Form for Inertia
const form = useForm({
  grave_type: '',
  permanent_grave_id: '',
  niche_id: '',
  members: [] as any[],
});

let searchTimeout: any = null;

// Define interface for grave search results
interface GraveSearchResult {
  id: number;
  display_name: string;
  details: {
    owner_name: string;
    contact_no?: string;
    member?: {
      full_name: string;
      family_no: string;
    };
  };
}

// Grave search functionality
const graveSearchTerm = ref('');
const searchResults = ref<GraveSearchResult[]>([]);
const isSearching = ref(false);
const showDropdown = ref(false);
const selectedGrave = ref<any>(null);

// Debounced search for graves
let graveSearchTimeout: any = null;
const debouncedSearchGraves = () => {
  if (graveSearchTimeout) {
    clearTimeout(graveSearchTimeout);
  }

  graveSearchTimeout = setTimeout(() => {
    searchGraves();
  }, 300);
};

// Search graves based on selected grave type
const searchGraves = async () => {
  if (graveSearchTerm.value.length < 2) {
    searchResults.value = [];
    showDropdown.value = false;
    return;
  }

  isSearching.value = true;
  showDropdown.value = true;

  try {
    const response = await fetch('/graveyard/valid-members/search-graves', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        search_term: graveSearchTerm.value,
        grave_type: formData.grave_type,
      }),
    });

    const data = await response.json();
    searchResults.value = data.results;
  } catch (error) {
    console.error('Search failed:', error);
    searchResults.value = [];
  } finally {
    isSearching.value = false;
  }
};

// Select a grave from search results
const selectGrave = (grave: any) => {
  selectedGrave.value = grave;
  graveSearchTerm.value = '';
  searchResults.value = [];
  showDropdown.value = false;

  // Update form based on grave type
  if (formData.grave_type === 'permanent_grave') {
    formData.permanent_grave_id = grave.id;
    formData.niche_id = '';
  } else {
    formData.niche_id = grave.id;
    formData.permanent_grave_id = '';
  }
};

// Clear grave selection
const clearSelection = () => {
  selectedGrave.value = null;
  graveSearchTerm.value = '';
  searchResults.value = [];
  showDropdown.value = false;
  formData.permanent_grave_id = '';
  formData.niche_id = '';
};

// Hide dropdown when clicking outside
document.addEventListener('click', (e) => {
  const target = e.target as HTMLElement;
  if (!target.closest('.relative')) {
    showDropdown.value = false;
  }
});

// Helper function to get form errors safely
const getFieldError = (fieldPath: string): string | undefined => {
  return (form.errors as any)[fieldPath];
};

// Add member
const addMember = () => {
  if (formData.members.length < 5) {
    formData.members.push({
      member_type: '',
      member_id: '',
      member_search: '',
      search_results: [] as ParishMemberSearchResult[],
      selected_member: null as ParishMemberSearchResult | null,
      first_name: '',
      last_name: '',
      contact_no: '',
      aadhar_no: '',
    });
  }
};

// Remove member
const removeMember = (index: number) => {
  formData.members.splice(index, 1);
};

// Search parish members
const searchParishMembers = (index: number, query: string) => {
  const member = formData.members[index];
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
const selectParishMember = (index: number, selectedMember: ParishMemberSearchResult) => {
  const member = formData.members[index];
  member.selected_member = selectedMember;
  member.member_id = selectedMember.id.toString();
  member.member_search = selectedMember.name;
  member.search_results = [];
  member.contact_no = selectedMember.contact_no || '';
};

// Clear parish member selection
const clearParishMemberSelection = (index: number) => {
  const member = formData.members[index];
  member.selected_member = null;
  member.member_id = '';
  member.member_search = '';
  member.search_results = [];
  member.contact_no = '';
};

// Watch for member_type changes to clear form fields
watch(
  () => formData.members.map(member => member.member_type),
  (newTypes, oldTypes) => {
    if (oldTypes) {
      newTypes.forEach((newType, index) => {
        const oldType = oldTypes[index];
        if (newType !== oldType && oldType !== '') {
          // Clear fields when member_type changes
          const member = formData.members[index];
          if (newType === 'external') {
            // Clear parish member fields
            member.member_id = '';
            member.member_search = '';
            member.search_results = [];
            member.selected_member = null;
          } else if (newType === 'member') {
            // Clear external member fields
            member.first_name = '';
            member.last_name = '';
            member.aadhar_no = '';
          }
          // Always clear contact as it's used by both types
          member.contact_no = '';
        }
      });
    }
  },
  { deep: true }
);

// Submit form
const submitForm = () => {
  // Prepare the data for submission
  const submitData = {
    grave_type: formData.grave_type,
    permanent_grave_id: formData.grave_type === 'permanent_grave' ? formData.permanent_grave_id : null,
    niche_id: formData.grave_type === 'niche' ? formData.niche_id : null,
    members: formData.members.map((member) => ({
      member_type: member.member_type,
      member_id: member.member_type === 'member' ? member.member_id : null,
      first_name: member.member_type === 'external' ? member.first_name : '',
      last_name: member.member_type === 'external' ? member.last_name : '',
      contact_no: member.contact_no || null,
      aadhar_no: member.member_type === 'external' ? member.aadhar_no || null : null,
    })),
  };

  form
    .transform(() => submitData)
    .post('/graveyard/valid-members', {
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
