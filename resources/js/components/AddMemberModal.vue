<template>
  <transition name="fade-scale">
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="closeModal">
      <div class="w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-[#ffffff] rounded-2xl shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">Add New Member</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-[1.5rem] h-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                      class="text-blue-600 focus:ring-[#3b82f6]"
                    />
                    <span class="text-gray-700">New Family</span>
                  </label>
                  <div v-if="familyType === 'new'" class="ml-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                      A new family number will be automatically generated in the format: <strong>{{ page.props.church_code }}-XXXX</strong>
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
                      class="text-blue-600 focus:ring-[#3b82f6]"
                    />
                    <span class="text-gray-700">Existing Family</span>
                  </label>
                  <div v-if="familyType === 'existing'" class="ml-6 space-y-3">
                    <input 
                      v-model="existingFamilyNo" 
                      type="text"
                      placeholder="Enter family number (e.g., {{ page.props.church_code }}-001-001)"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
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
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500 mb-3"
                      />
                      <div v-if="familySearchResults.length > 0" class="space-y-2 max-h-40 overflow-y-auto">
                        <div 
                          v-for="family in familySearchResults" 
                          :key="family.family_no"
                          @click="selectFamily(family.family_no)"
                          class="p-2 bg-[#ffffff] border border-gray-200 rounded cursor-pointer hover:bg-blue-50 transition"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                  <input 
                    v-model="form.last_name" 
                    type="text" 
                    placeholder="Enter last name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                  <input 
                    v-model="form.middle_name" 
                    type="text" 
                    placeholder="Enter middle name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                  <DateInput v-model="form.date_of_birth" id="date_of_birth" class="w-full" />
                </div>
                

              </div>
            </div>

            <!-- Community Information -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Community Information</h3>
              
              <!-- Pre-populated indicator -->
              <div v-if="familyType === 'existing' && existingFamilyNo && isPrePopulated" 
                   class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-2">
                  <svg class="w-[1rem] h-[1rem] text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-sm text-blue-800">
                    Community and Cluster pre-selected from family {{ existingFamilyNo }}
                  </span>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Community <span class="text-red-500">*</span></label>
                  <select 
                    v-model="form.community_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
                  >
                    <option value="">Select Community</option>
                    <option v-for="community in communities" :key="community.id" :value="community.id">
                      {{ community.name }}
                    </option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Cluster <span class="text-red-500">*</span></label>
                  <select 
                    v-model="form.community_cluster_id" 
                    :disabled="!form.community_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                  >
                    <option value="">{{ form.community_id ? 'Select Cluster' : 'Select Community First' }}</option>
                    <option v-for="cluster in filteredClusters" :key="cluster.id" :value="cluster.id">
                      {{ cluster.name }}
                    </option>
                  </select>
                  <p v-if="!form.community_id" class="text-xs text-gray-500 mt-1">
                    Please select a community first to choose a cluster (required)
                  </p>
                </div>
                
                <div>
                  <label for="relationship_id" class="block text-sm font-medium text-gray-700">
                    Relationship <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-500 font-normal">(with the head of the family)</span>
                  </label>
                  <select 
                    v-model="form.relationship_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
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
import { ref, computed, watch, nextTick } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { DateInput } from '@/components/ui/date-input';
import { formatDateForDisplay } from '@/lib/utils';



interface Props {
  modelValue: boolean;
  communities: Array<{ id: string | number; name: string }>;
  relationships: Array<{ id: string | number; name: string }>;
  communityClusters: Array<{ id: string | number; name: string; community_id: string | number }>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:modelValue': [value: boolean];
}>();

const page = usePage();



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
const isPrePopulated = ref(false);

const form = ref({
  first_name: '',
  last_name: '',
  middle_name: '',
  date_of_birth: '',
  community_id: '',
  community_cluster_id: '',
  relationship_id: '',
  existing_family_no: ''
});

// Preview data
const previewFamilyNo = ref('Loading...');
const previewMemberNo = ref('Loading...');

// Computed property for filtered clusters
const filteredClusters = computed(() => {
  if (!form.value.community_id) {
    // If no community is selected, show no clusters (dropdown is disabled)
    return [];
  }
  
  // Filter clusters by selected community
  return (props.communityClusters || []).filter(cluster => 
    Number(cluster.community_id) === Number(form.value.community_id)
  );
});

// Watch for community changes to reset cluster selection
watch(() => form.value.community_id, (newCommunityId) => {
  // Reset cluster selection when community changes
  form.value.community_cluster_id = '';
});

// Watch for existing family number changes with debounce
let familyNoTimeout: number;
watch(existingFamilyNo, async (newFamilyNo) => {
  clearTimeout(familyNoTimeout);
  
  if (familyType.value === 'existing' && newFamilyNo.trim()) {
    // Validate family number format
    const churchCode = page.props.church_code;
    const familyNoPattern = new RegExp(`^${churchCode}-\\d{3,4}$`);
    
    if (familyNoPattern.test(newFamilyNo.trim())) {
      // Valid family number format - fetch details with debounce
      familyNoTimeout = setTimeout(() => {
        fetchFamilyDetails(newFamilyNo.trim());
      }, 500); // 500ms debounce
    } else {
      // Invalid format - reset pre-populated flag
      isPrePopulated.value = false;
    }
  } else {
    // No family number or not existing family type - reset
    isPrePopulated.value = false;
  }
});

// Function to fetch family details (extracted from selectFamily)
const fetchFamilyDetails = async (familyNo: string) => {
  try {
    const response = await fetch(`/member/family-details/${familyNo}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    });
    
    if (response.ok) {
      const familyDetails = await response.json();
      // Store the cluster ID before setting community (which triggers the watcher)
      const clusterId = familyDetails.community_cluster_id?.toString() || '';
      
      // Set community first (this will trigger the watcher and reset cluster)
      form.value.community_id = familyDetails.community_id?.toString() || '';
      
      // Use nextTick to ensure the watcher has run and then set the cluster
      await nextTick();
      form.value.community_cluster_id = clusterId;
      
      // Set pre-populated flag
      isPrePopulated.value = true;
      
    } else if (response.status === 401) {
      // Unauthorized - redirect to login
      window.location.href = '/login';
      return;
    } else {
      console.error('Failed to fetch family details');
      isPrePopulated.value = false;
    }
  } catch (error) {
    console.error('Error fetching family details:', error);
    isPrePopulated.value = false;
  }
};

// Test authentication by trying to access a simple member route
const testAuthentication = async () => {
  try {
    const response = await fetch('/member/index', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    });
    
    return response.ok;
  } catch (error) {
    console.error('Auth test error:', error);
    return false;
  }
};

// Fetch next available numbers
const fetchNextNumbers = async () => {
  try {
    
    // Check if user is authenticated
    if (!document.querySelector('meta[name="csrf-token"]')) {
      console.error('No CSRF token found - user may not be authenticated');
      previewFamilyNo.value = 'Authentication required';
      previewMemberNo.value = 'Authentication required';
      return;
    }
    
    // Test authentication first
    const isAuthenticated = await testAuthentication();
    if (!isAuthenticated) {
      console.error('User not authenticated');
      previewFamilyNo.value = 'Authentication required';
      previewMemberNo.value = 'Authentication required';
      return;
    }
    
    const response = await fetch('/member/next-available-numbers', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    });
    
    
    if (response.ok) {
      const data = await response.json();
      previewFamilyNo.value = data.next_family_no;
      previewMemberNo.value = data.next_member_no;
    } else if (response.status === 401) {
      // Unauthorized - redirect to login
      console.error('User not authenticated, redirecting to login');
      window.location.href = '/login';
      return;
    } else if (response.status === 419) {
      // CSRF token mismatch - refresh page
      console.error('CSRF token mismatch, refreshing page');
      window.location.reload();
      return;
    } else {
      console.error('API response not ok:', response.status);
      const errorText = await response.text();
      console.error('Error response:', errorText);
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
    const response = await fetch(`/member/search-families?q=${encodeURIComponent(familySearchQuery.value)}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    });
    
    if (response.ok) {
      const data = await response.json();
      if (Array.isArray(data)) {
        familySearchResults.value = data.map(item => ({
          family_no: item.family_no || '',
          member_count: item.member_count || 0,
          community_name: item.community_name || 'Unknown Community',
          members: item.members || []
        }));
      } else {
        familySearchResults.value = [];
      }
    } else if (response.status === 401) {
      // Unauthorized - redirect to login
      window.location.href = '/login';
      return;
    } else if (response.status === 419) {
      // CSRF token mismatch - refresh page
      window.location.reload();
      return;
    } else {
      const errorText = await response.text();
      console.error('Family search failed:', response.status, errorText);
      familySearchResults.value = [];
    }
  } catch (error) {
    console.error('Family search error:', error);
    familySearchResults.value = [];
  } finally {
    isSearching.value = false;
  }
};

const selectFamily = async (familyNo: string) => {
  existingFamilyNo.value = familyNo;
  showFamilySearch.value = false;
  familySearchQuery.value = '';
  familySearchResults.value = [];
  
  // Fetch family details to pre-populate community and cluster
  await fetchFamilyDetails(familyNo);
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
  
  if (!form.value.community_cluster_id) {
    errors.value.push('Cluster is required');
  }
  
  if (!form.value.relationship_id) {
    errors.value.push('Relationship is required');
  }
  
  if (familyType.value === 'existing' && !existingFamilyNo.value.trim()) {
    errors.value.push('Existing family number is required');
  }
  
  if (familyType.value === 'existing' && existingFamilyNo.value.trim()) {
    // Validate family number format
    const churchCode = page.props.church_code;
    const familyNoPattern = new RegExp(`^${churchCode}-\\d{3,4}$`);
    if (!familyNoPattern.test(existingFamilyNo.value)) {
      errors.value.push(`Family number must be in format: ${churchCode}-XXX or ${churchCode}-XXXX`);
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
    community_id: '',
    community_cluster_id: '',
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
  isPrePopulated.value = false;
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
    // Reset pre-populated flag
    isPrePopulated.value = false;
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