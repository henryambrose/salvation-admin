<template>
  <transition name="fade-scale">
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="closeModal">
      <div class="w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">Add New Member</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6">
          <form @submit.prevent="submitForm">
            <!-- Family Type Selection -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Family Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- New Family Option -->
                <div class="space-y-4">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input 
                      type="radio" 
                      v-model="familyType" 
                      value="new" 
                      class="text-blue-600 focus:ring-blue-500"
                    />
                    <span class="text-gray-700">New Family</span>
                  </label>
                  <div v-if="familyType === 'new'" class="ml-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                      A new family number will be automatically generated in the format: <strong>SAL-XXX-YYY</strong>
                    </p>
                  </div>
                </div>
                
                <!-- Existing Family Option -->
                <div class="space-y-4">
                  <label class="flex items-center space-x-3 cursor-pointer">
                    <input 
                      type="radio" 
                      v-model="familyType" 
                      value="existing" 
                      class="text-blue-600 focus:ring-blue-500"
                    />
                    <span class="text-gray-700">Existing Family</span>
                  </label>
                  <div v-if="familyType === 'existing'" class="ml-6 space-y-3">
                    <input 
                      v-model="existingFamilyNo" 
                      type="text"
                      placeholder="Enter family number (e.g., SAL-001-001)"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <button 
                      type="button"
                      @click="searchFamilies"
                      :disabled="isSearching"
                      class="text-sm text-blue-600 hover:text-blue-800 underline disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span v-if="isSearching">Searching...</span>
                      <span v-else>Search existing families</span>
                    </button>
                    <p class="text-xs text-gray-500">Enter the existing family number to add this member to that family</p>
                    
                    <!-- Family Search Results -->
                    <div v-if="showFamilySearch" class="mt-4 p-4 bg-gray-50 rounded-lg">
                      <h4 class="text-sm font-semibold text-gray-800 mb-2">Search Families</h4>
                      <input 
                        v-model="familySearchQuery" 
                        type="text" 
                        placeholder="Search by family number or member name..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3"
                      />
                      <div v-if="familySearchResults.length > 0" class="space-y-2 max-h-40 overflow-y-auto">
                        <div 
                          v-for="family in familySearchResults" 
                          :key="family.family_no"
                          @click="selectFamily(family.family_no)"
                          class="p-2 bg-white border border-gray-200 rounded cursor-pointer hover:bg-blue-50 transition"
                        >
                          <div class="flex items-center justify-between">
                            <span class="font-mono text-sm">{{ family.family_no }}</span>
                            <span class="text-xs text-gray-500">{{ family.member_count }} members</span>
                          </div>
                          <div class="text-xs text-gray-600 mt-1">
                            {{ family.members.join(', ') }}
                          </div>
                        </div>
                      </div>
                      <div v-else-if="familySearchQuery && !isSearching" class="text-sm text-gray-500">
                        No families found
                      </div>
                      <div v-if="isSearching" class="text-sm text-gray-500">
                        Searching...
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Member Information -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Member Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                  <input 
                    v-model="form.first_name" 
                    type="text" 
                    required
                    placeholder="Enter first name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                  <input 
                    v-model="form.last_name" 
                    type="text" 
                    placeholder="Enter last name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                  <input 
                    v-model="form.middle_name" 
                    type="text" 
                    placeholder="Enter middle name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                  <input 
                    v-model="form.date_of_birth" 
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                  <input 
                    v-model="form.contact_no_1" 
                    type="tel" 
                    placeholder="Enter contact number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input 
                    v-model="form.email" 
                    type="email" 
                    placeholder="Enter email address"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </div>
            </div>

            <!-- Community Information -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Community Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Community <span class="text-red-500">*</span></label>
                  <select 
                    v-model="form.community_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Select Community</option>
                    <option v-for="community in communities" :key="community.id" :value="community.id">
                      {{ community.name }}
                    </option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Relationship <span class="text-red-500">*</span></label>
                  <select 
                    v-model="form.relationship_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Select Relationship</option>
                    <option v-for="relationship in relationships" :key="relationship.id" :value="relationship.id">
                      {{ relationship.name }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Preview Section -->
            <div v-if="familyType === 'new' && form.first_name" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
              <h4 class="text-sm font-semibold text-green-800 mb-2">Preview</h4>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="font-medium text-gray-700">Family Number:</span>
                  <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium font-mono">
                    {{ previewFamilyNo === 'Loading...' ? 'Loading...' : previewFamilyNo }}
                  </span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Member Number:</span>
                  <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium font-mono">
                    {{ previewMemberNo === 'Loading...' ? 'Loading...' : previewMemberNo }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Error Messages -->
            <div v-if="errors.length > 0" class="mb-6 p-4 bg-red-50 rounded-lg border border-red-200">
              <h4 class="text-sm font-semibold text-red-800 mb-2">Please fix the following errors:</h4>
              <ul class="text-sm text-red-700 space-y-1">
                <li v-for="error in errors" :key="error">{{ error }}</li>
              </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3">
              <button 
                type="button" 
                @click="closeModal"
                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
              >
                Cancel
              </button>
              <button 
                type="submit" 
                :disabled="isSubmitting"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                <span v-if="isSubmitting">Creating...</span>
                <span v-else>Create Member</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

interface Props {
  modelValue: boolean;
  communities: Array<{ id: string | number; name: string }>;
  relationships: Array<{ id: string | number; name: string }>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:modelValue': [value: boolean];
}>();

// Form data
const familyType = ref<'new' | 'existing'>('new');
const existingFamilyNo = ref('');
const isSubmitting = ref(false);
const errors = ref<string[]>([]);

// Family search
const showFamilySearch = ref(false);
const familySearchQuery = ref('');
const familySearchResults = ref<any[]>([]);
const isSearching = ref(false);

const form = ref({
  first_name: '',
  last_name: '',
  middle_name: '',
  date_of_birth: '',
  contact_no_1: '',
  email: '',
  community_id: '',
  relationship_id: '',
  existing_family_no: ''
});

// Preview data
const previewFamilyNo = ref('Loading...');
const previewMemberNo = ref('Loading...');

// Fetch next available numbers
const fetchNextNumbers = async () => {
  try {
    console.log('Fetching next numbers...');
    const response = await fetch('/api/members/next-numbers');
    if (response.ok) {
      const data = await response.json();
      console.log('Received data:', data);
      previewFamilyNo.value = data.next_family_no;
      previewMemberNo.value = data.next_member_no;
      console.log('Updated preview - Family:', previewFamilyNo.value, 'Member:', previewMemberNo.value);
    } else {
      console.error('API response not ok:', response.status);
      previewFamilyNo.value = 'Error loading';
      previewMemberNo.value = 'Error loading';
    }
  } catch (error) {
    console.error('Error fetching next numbers:', error);
    previewFamilyNo.value = 'Error loading';
    previewMemberNo.value = 'Error loading';
  }
};

// Family search functions
const searchFamilies = () => {
  showFamilySearch.value = !showFamilySearch.value;
  if (showFamilySearch.value) {
    performFamilySearch();
  }
};

const performFamilySearch = async () => {
  if (!familySearchQuery.value.trim()) {
    familySearchResults.value = [];
    return;
  }
  
  isSearching.value = true;
  try {
    console.log('Searching for:', familySearchQuery.value);
    const response = await fetch(`/api/families/search?q=${encodeURIComponent(familySearchQuery.value)}`);
    console.log('Response status:', response.status);
    
    if (response.ok) {
      const data = await response.json();
      console.log('Search results:', data);
      familySearchResults.value = data;
    } else {
      console.error('Search failed with status:', response.status);
      const errorText = await response.text();
      console.error('Error response:', errorText);
    }
  } catch (error) {
    console.error('Error searching families:', error);
    familySearchResults.value = [];
  } finally {
    isSearching.value = false;
  }
};

const selectFamily = (familyNo: string) => {
  existingFamilyNo.value = familyNo;
  showFamilySearch.value = false;
  familySearchQuery.value = '';
  familySearchResults.value = [];
};

// Watch for search query changes with debounce
let searchTimeout: number;
watch(familySearchQuery, (newQuery) => {
  clearTimeout(searchTimeout);
  
  if (newQuery.trim()) {
    searchTimeout = setTimeout(() => {
      performFamilySearch();
    }, 300); // 300ms debounce
  } else {
    familySearchResults.value = [];
  }
});

// Validation
const validateForm = () => {
  errors.value = [];
  
  if (!form.value.first_name.trim()) {
    errors.value.push('First name is required');
  }
  
  if (!form.value.community_id) {
    errors.value.push('Community is required');
  }
  
  if (!form.value.relationship_id) {
    errors.value.push('Relationship is required');
  }
  
  if (familyType.value === 'existing' && !existingFamilyNo.value.trim()) {
    errors.value.push('Existing family number is required');
  }
  
  if (familyType.value === 'existing' && existingFamilyNo.value.trim()) {
    // Validate family number format
    const familyNoPattern = /^SAL-\d{3}-\d{3}$/;
    if (!familyNoPattern.test(existingFamilyNo.value)) {
      errors.value.push('Family number must be in format: SAL-XXX-YYY');
    }
  }
  
  return errors.value.length === 0;
};

// Submit form
const submitForm = async () => {
  if (!validateForm()) {
    return;
  }
  
  isSubmitting.value = true;
  errors.value = [];
  
  try {
    const formData = {
      ...form.value,
      existing_family_no: familyType.value === 'existing' ? existingFamilyNo.value : null
    };
    
    await router.post(route('member.store'), formData, {
      onSuccess: () => {
        closeModal();
        // Reset form
        resetForm();
      },
      onError: (backendErrors) => {
        // Handle validation errors from backend
        const errorMessages = Object.values(backendErrors).flat() as string[];
        errors.value = errorMessages;
      }
    });
  } catch (error) {
    console.error('Error creating member:', error);
    errors.value.push('An error occurred while creating the member. Please try again.');
  } finally {
    isSubmitting.value = false;
  }
};

// Reset form
const resetForm = () => {
  form.value = {
    first_name: '',
    last_name: '',
    middle_name: '',
    date_of_birth: '',
    contact_no_1: '',
    email: '',
    community_id: '',
    relationship_id: '',
    existing_family_no: ''
  };
  familyType.value = 'new';
  existingFamilyNo.value = '';
  errors.value = [];
  showFamilySearch.value = false;
  familySearchQuery.value = '';
  familySearchResults.value = [];
  isSearching.value = false;
  previewFamilyNo.value = 'Loading...';
  previewMemberNo.value = 'Loading...';
};

// Close modal
const closeModal = () => {
  emit('update:modelValue', false);
  resetForm();
};

// Watch for modal state changes
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    // Modal opened - fetch next numbers
    fetchNextNumbers();
  } else {
    resetForm();
  }
});

// Watch for family type changes
watch(familyType, (newType) => {
  if (newType === 'new') {
    // User selected new family - refresh numbers
    fetchNextNumbers();
  }
});
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style> 