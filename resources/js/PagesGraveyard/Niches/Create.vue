<template>
  <div class="py-6">
    <div class="mx-auto max-w-7xl">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-blue-700">Create Niche</h1>
            <Link
              :href="route('graveyard.niches.index')"
              class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-900"
            >
              Back to List
            </Link>
          </div>

          <div class="mx-auto max-w-4xl">
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Basic Information Section -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Basic Information</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                  <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Niche No *</label>
                    <input
                      v-model="form.niche_no"
                      type="number"
                      min="1"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      placeholder="Enter niche number"
                      required
                    />
                    <p v-if="errors.niche_no" class="mt-1 text-sm text-red-600">{{ errors.niche_no }}</p>
                  </div>

                  <!-- <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Sr No *</label>
                    <input
                      v-model="form.sr_no"
                      type="number"
                      min="1"
                      value="1"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      placeholder="Enter serial number"
                      disabled
                      required
                    />
                    <p v-if="errors.sr_no" class="mt-1 text-sm text-red-600">{{ errors.sr_no }}</p>
                  </div> -->

                  <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Location *</label>
                    <input
                      v-model="form.location"
                      type="text"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      placeholder="Enter location (e.g., Wall A, Level 2)"
                      required
                    />
                    <p v-if="errors.location" class="mt-1 text-sm text-red-600">{{ errors.location }}</p>
                  </div>
                </div>

                <div class="mt-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Status *</label>
                  <select
                    v-model="form.status"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
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

              <!-- Member Information Section -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Owner Information</h3>

                <!-- Member Type Selection -->
                <div class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Member Type</label>
                  <div class="flex space-x-4">
                    <label class="flex items-center">
                      <input
                        v-model="form.member_type"
                        type="radio"
                        value="member"
                        class="mr-2 text-blue-600 focus:ring-[#3b82f6]"
                        @change="handleMemberTypeChange"
                      />
                      <span class="text-sm text-gray-700">Parish Member</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        v-model="form.member_type"
                        type="radio"
                        value="external"
                        class="mr-2 text-blue-600 focus:ring-[#3b82f6]"
                        @change="handleMemberTypeChange"
                      />
                      <span class="text-sm text-gray-700">Non-Member</span>
                    </label>
                  </div>
                </div>

                <!-- Member Search (for parish members) -->
                <div v-if="form.member_type === 'member'" class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Search Member</label>
                  <div class="relative">
                    <input
                      v-model="memberSearchQuery"
                      type="text"
                      placeholder="Search by name, family number, or phone..."
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                      @input="searchMembers"
                    />

                    <!-- Search Results Dropdown -->
                    <div
                      v-if="memberSearchResults.length > 0 && memberSearchQuery"
                      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-300 bg-[#ffffff] shadow-lg"
                    >
                      <div
                        v-for="member in memberSearchResults"
                        :key="member.id"
                        @click="selectMember(member)"
                        class="cursor-pointer border-b border-gray-100 px-4 py-2 last:border-b-0 hover:bg-gray-100"
                      >
                        <div class="font-medium">{{ member.full_name }}</div>
                        <div class="text-sm text-gray-500">
                          Community No: {{ member.community?.name?.split('-')[0]?.trim() }} | Family: {{ member.family_no || 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500">Address: {{ member.current_add1 }} | {{ member.contact_no_1 }}</div>
                      </div>
                    </div>
                  </div>

                  <!-- Selected Member Display -->
                  <div v-if="selectedMember" class="mt-3 rounded-md bg-blue-50 p-3">
                    <div class="flex items-start justify-between">
                      <div>
                        <div class="text-sm text-blue-700">
                          <div class="font-medium">{{ selectedMember.full_name }}</div>
                          <div class="text-sm text-gray-500">
                            Community No: {{ selectedMember.community?.name?.split('-')[0]?.trim() }} | Family:
                            {{ selectedMember.family_no || 'N/A' }}
                          </div>
                          <div class="text-sm text-gray-500">Address: {{ selectedMember.current_add1 }} | {{ selectedMember.contact_no_1 }}</div>
                        </div>
                      </div>
                      <button @click="clearSelectedMember" type="button" class="text-blue-600 hover:text-blue-800">
                        <X class="h-[1rem] w-[1rem]" />
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Non-Member Input -->
                <div v-if="form.member_type === 'external'" class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Name</label>
                  <input
                    v-model="form.owner_name"
                    type="text"
                    placeholder="Enter full name"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                  />
                  <p v-if="errors.owner_name" class="mt-1 text-sm text-red-600">{{ errors.owner_name }}</p>
                </div>

                <!-- Phone Number -->
                <div class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Phone Number</label>
                  <input
                    v-model="form.contact_no"
                    type="tel"
                    placeholder="Enter phone number"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                  />
                  <p v-if="errors.contact_no" class="mt-1 text-sm text-red-600">{{ errors.contact_no }}</p>
                </div>
              </div>

              <!-- Niche Details Section -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Niche Details</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                  <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Width (inches)</label>
                    <input
                      v-model="form.size_width"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      placeholder="Enter width"
                    />
                    <p v-if="errors.size_width" class="mt-1 text-sm text-red-600">{{ errors.size_width }}</p>
                  </div>

                  <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Height (inches)</label>
                    <input
                      v-model="form.size_height"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      placeholder="Enter height"
                    />
                    <p v-if="errors.size_height" class="mt-1 text-sm text-red-600">{{ errors.size_height }}</p>
                  </div>

                  <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Depth (inches)</label>
                    <input
                      v-model="form.size_depth"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      placeholder="Enter depth"
                    />
                    <p v-if="errors.size_depth" class="mt-1 text-sm text-red-600">{{ errors.size_depth }}</p>
                  </div>
                </div>

                <div class="mt-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Last Occupation Date</label>
                  <DateInput v-model="form.last_occupation_date" class="w-full" />
                  <p v-if="errors.last_occupation_date" class="mt-1 text-sm text-red-600">{{ errors.last_occupation_date }}</p>
                </div>

                <div class="mt-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Remarks</label>
                  <textarea
                    v-model="form.remarks"
                    rows="3"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Enter any additional remarks"
                  ></textarea>
                  <p v-if="errors.remarks" class="mt-1 text-sm text-red-600">{{ errors.remarks }}</p>
                </div>

                <div class="mt-4">
                  <label class="flex items-center">
                    <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                  </label>
                  <p v-if="errors.is_active" class="mt-1 text-sm text-red-600">{{ errors.is_active }}</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('graveyard.niches.index')"
                  class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="submitting"
                  class="rounded-md bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                >
                  {{ submitting ? 'Creating...' : 'Create Niche' }}
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
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import { ref } from 'vue';
import { DateInput } from '@/components/ui/date-input';
import { formatDateForDisplay } from '@/lib/utils';

defineOptions({
  layout: AppLayout,
});

interface Props {
  locations: string[];
  statuses: string[];
  errors?: any;
}

const props = defineProps<Props>();

const submitting = ref(false);

// Member search state
const memberSearchQuery = ref('');
const memberSearchResults = ref<any[]>([]);
const selectedMember = ref<any>(null);

const form = useForm({
  member_type: 'member',
  niche_no: '',
  sr_no: 1,
  location: '',
  status: '',
  last_occupation_date: '',
  owner_name: '',
  member_id: null as number | null,
  contact_no: '',
  remarks: '',
  size_width: '',
  size_height: '',
  size_depth: '',
  is_active: true,
});

const submitForm = () => {
  submitting.value = true;

  form.post(route('graveyard.niches.store'), {
    onSuccess: () => {
      submitting.value = false;
    },
    onError: () => {
      submitting.value = false;
    },
  });
};

// Member search functions
async function searchMembers() {
  if (memberSearchQuery.value.length < 2) {
    memberSearchResults.value = [];
    return;
  }

  try {
    const response = await fetch('/graveyard/niches/search-members?query=' + encodeURIComponent(memberSearchQuery.value));
    const data = await response.json();
    memberSearchResults.value = data;
  } catch (error) {
    console.error('Error searching members:', error);
    memberSearchResults.value = [];
  }
}

function selectMember(member: any) {
  selectedMember.value = member;
  form.member_id = member.id;
  form.contact_no = member.contact_no_1 || '';
  memberSearchQuery.value = '';
  memberSearchResults.value = [];
}

function clearSelectedMember() {
  selectedMember.value = null;
  form.member_id = null;
  form.contact_no = '';
}

function handleMemberTypeChange() {
  if (form.member_type === 'member') {
    // Clear non-member data
    form.owner_name = '';
  } else {
    // Clear member data
    form.member_id = null;
    selectedMember.value = null;
    memberSearchQuery.value = '';
    memberSearchResults.value = [];
  }
}
</script>
