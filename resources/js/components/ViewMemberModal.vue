<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps<{
  modelValue: boolean;
  member: Record<string, any> | null;
  incomeRange: Record<string, any> | null;
}>();

const emit = defineEmits(['update:modelValue']);

const page = usePage();
const modalRef = ref<HTMLElement | null>(null);
const currentTab = ref('personal');
const familyMembers = ref<any[]>([]);
const loadingFamilyMembers = ref(false);
const memberDetails = ref<any>(null);
const loadingMemberDetails = ref(false);

// Tab definitions with categories and fields
const tabs = [
  {
    key: 'personal',
    label: 'Personal Info',
    fields: [
      { key: 'first_name', label: 'First Name' },
      { key: 'middle_name', label: 'Middle Name' },
      { key: 'last_name', label: 'Last Name' },
      { key: 'date_of_birth', label: 'Date of Birth' },
      { key: 'age', label: 'Age' },
      { key: 'aadhar', label: 'Aadhar' },
      { key: 'blood_group_id', label: 'Blood Group' },
    ],
  },
  {
    key: 'religious',
    label: 'Religious Details',
    fields: [
      { key: 'baptism_date', label: 'Baptism Date' },
      { key: 'baptism_reg_no', label: 'Baptism Reg. No.' },
      { key: 'baptism_parish', label: 'Baptism Parish' },
      { key: 'confirmation_date', label: 'Confirmation Date' },
      { key: 'confirmation_reg_no', label: 'Confirmation Reg. No.' },
      { key: 'confirmation_parish', label: 'Confirmation Parish' },
      { key: 'marriage_date', label: 'Marriage Date' },
      { key: 'marriage_reg_no', label: 'Marriage Reg. No.' },
      { key: 'marriage_parish', label: 'Marriage Parish' },
      { key: 'death_date', label: 'Death Date' },
      { key: 'deaths_reg_no', label: 'Death Reg. No.' },
      { key: 'death_parish', label: 'Death Parish' },
    ],
  },
  {
    key: 'contact',
    label: 'Contact Info',
    fields: [
      { key: 'contact_no_1', label: 'Contact No 1' },
      { key: 'contact_no_2', label: 'Contact No 2' },
      { key: 'email', label: 'Email' },
    ],
  },
  {
    key: 'permanent_address',
    label: 'Permanent Address',
    fields: [
      { key: 'permanent_add1', label: 'Address 1' },
      { key: 'permanent_add2', label: 'Address 2' },
      { key: 'permanent_add3', label: 'Address 3' },
      { key: 'permanent_town_id', label: 'Town' },
      { key: 'permanent_city_id', label: 'City' },
      { key: 'permanent_pincode', label: 'Pincode' },
      { key: 'permanent_state_id', label: 'State' },
      { key: 'permanent_country_id', label: 'Country' },
    ],
  },
  {
    key: 'current_address',
    label: 'Current Address',
    fields: [
      { key: 'current_add1', label: 'Address 1' },
      { key: 'current_add2', label: 'Address 2' },
      { key: 'current_add3', label: 'Address 3' },
      { key: 'current_town_id', label: 'Town' },
      { key: 'current_city_id', label: 'City' },
      { key: 'current_pincode', label: 'Pincode' },
      { key: 'current_state_id', label: 'State' },
      { key: 'current_country_id', label: 'Country' },
    ],
  },
  {
    key: 'community',
    label: 'Community & Family',
    fields: [
      { key: 'community_id', label: 'Community' },
      { key: 'community_cluster_id', label: 'Cluster' },
      { key: 'family_no', label: 'Family No' },
      { key: 'member_no', label: 'Member No' },
      { key: 'registration_year', label: 'Registration Year' },
      { key: 'church_code', label: 'Church Code' },
      { key: 'marital_status', label: 'Marital Status' },
      { key: 'current_family_no', label: 'Current Family No' },
    ],
  },
  {
    key: 'education',
    label: 'Education & Work',
    fields: [
      { key: 'school_name', label: 'School Name' },
      { key: 'college_name', label: 'College Name' },
      { key: 'latest_qualifications', label: 'Latest Qualifications' },
      { key: 'company_name', label: 'Company Name' },
      { key: 'designation', label: 'Designation' },
      { key: 'income_range', label: 'Income Range' },
    ],
  },
  {
    key: 'leadership',
    label: 'Leadership Roles',
    fields: [
      { key: 'scc_heads', label: 'SCC Head' },
      { key: 'ppc_heads', label: 'PPC Head' },
      { key: 'cluster_heads', label: 'Cluster Head' },
      { key: 'cells_and_associations', label: 'Cells & Associations' },
    ],
  },
];

const enhancedIncomeRanges = computed(() => {
  const c = props.incomeRange || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
  };
});

function calculateAge(dateStr: string) {
  if (!dateStr) return '';
  const birthDate = new Date(dateStr);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  return age.toString();
}

function formatDate(dateStr: string) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const day = date.getDate().toString().padStart(2, '0');
  const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const month = monthNames[date.getMonth()];
  const year = date.getFullYear();
  return `${day}-${month}-${year}`;
}

function formatFieldValue(fieldKey: string, value: any) {
  // Handle age calculation first (before early return)
  if (fieldKey === 'age') {
    const age = calculateAge(props.member?.date_of_birth || '');
    return age || '—';
  }
  
  if (value === null || value === undefined) return '—';
  
  // Handle relationship objects
  if (fieldKey === 'relationship_id' && typeof value === 'object' && value.name) {
    return value.name;
  }
  
  // Handle designation objects
  if (fieldKey === 'designation' && typeof value === 'object' && value.name) {
    return value.name;
  }
  
  // Handle Income Range objects
  if (fieldKey === 'income_range' && typeof value === 'object' && value.name) {
    return value.name;
  }
  
  // Handle blood group objects
  if (fieldKey === 'blood_group_id' && typeof value === 'object' && value.name) {
    return value.name;
  }
  
  // Handle cells and associations relationship
  if (fieldKey === 'cells_and_associations') {
    if (Array.isArray(value) && value.length > 0) {
      return value.map((item, index) => `${index + 1}. ${item.name}`).join('\n');
    }
    return '—';
  }

  // Handle leadership roles
  if (fieldKey === 'scc_heads') {
    if (memberDetails.value?.scc_heads && Array.isArray(memberDetails.value.scc_heads) && memberDetails.value.scc_heads.length > 0) {
      return memberDetails.value.scc_heads.map((item: any, index: number) => `${index + 1}. ${item.community?.name || 'Unknown Community'}`).join('\n');
    }
    return '—';
  }

  if (fieldKey === 'ppc_heads') {
    if (memberDetails.value?.ppc_heads && Array.isArray(memberDetails.value.ppc_heads) && memberDetails.value.ppc_heads.length > 0) {
      return memberDetails.value.ppc_heads.map((item: any, index: number) => `${index + 1}. ${item.community?.name || 'Unknown Community'}`).join('\n');
    }
    return '—';
  }

  if (fieldKey === 'cluster_heads') {
    if (memberDetails.value?.cluster_heads && Array.isArray(memberDetails.value.cluster_heads) && memberDetails.value.cluster_heads.length > 0) {
      return memberDetails.value.cluster_heads.map((item: any, index: number) => `${index + 1}. ${item.cluster?.name || 'Unknown Cluster'} - ${item.community?.name || 'Unknown Community'}`).join('\n');
    }
    return '—';
  }
  
  // Handle community objects
  if (fieldKey === 'community_id' && typeof value === 'object' && value.name) {
    return value.name;
  }
  
  // Handle community cluster objects
  if (fieldKey === 'community_cluster_id' && typeof value === 'object' && value.name) {
    return value.name;
  }
  

  
  // Handle parish relationships
  if (fieldKey === 'baptism_parish') {
    // Check if value is a JSON object with name property
    if (typeof value === 'object' && value !== null && value.name) {
      return value.name;
    }
    // Check if relationship is loaded
    if (props.member?.baptism_parish_id && props.member?.baptismParish) {
      return props.member.baptismParish.name;
    }
    // Return string value or fallback
    return (typeof value === 'string' ? value : '') || '—';
  }
  if (fieldKey === 'confirmation_parish') {
    // Check if value is a JSON object with name property
    if (typeof value === 'object' && value !== null && value.name) {
      return value.name;
    }
    // Check if relationship is loaded
    if (props.member?.confirmation_parish_id && props.member?.confirmationParish) {
      return props.member.confirmationParish.name;
    }
    // Return string value or fallback
    return (typeof value === 'string' ? value : '') || '—';
  }
  if (fieldKey === 'marriage_parish') {
    // Check if value is a JSON object with name property
    if (typeof value === 'object' && value !== null && value.name) {
      return value.name;
    }
    // Check if relationship is loaded
    if (props.member?.marriage_parish_id && props.member?.marriageParish) {
      return props.member.marriageParish.name;
    }
    // Return string value or fallback
    return (typeof value === 'string' ? value : '') || '—';
  }
  if (fieldKey === 'death_parish') {
    // Check if value is a JSON object with name property
    if (typeof value === 'object' && value !== null && value.name) {
      return value.name;
    }
    // Check if relationship is loaded
    if (props.member?.death_parish_id && props.member?.deathParish) {
      return props.member.deathParish.name;
    }
    // Return string value or fallback
    return (typeof value === 'string' ? value : '') || '—';
  }
  
  // Handle address fields (town, city, state, country)
  if (fieldKey.includes('_town_id') || fieldKey.includes('_city_id') || fieldKey.includes('_state_id') || fieldKey.includes('_country_id')) {
    // The value should be the name string from the transformed data
    if (typeof value === 'string' && value.trim()) {
      return value;
    }
    // Return fallback
    return '—';
  }
  

  // Handle church code with badge styling
  if (fieldKey === 'church_code') {
            return value || '—';
  }
  
  // Handle marital status with proper formatting
  if (fieldKey === 'marital_status') {
    if (!value) return '—';
    return value.charAt(0).toUpperCase() + value.slice(1);
  }
  
  // Handle date formatting
  if (fieldKey.includes('date') && value) {
    const date = new Date(value);
    const day = date.getDate().toString().padStart(2, '0');
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = monthNames[date.getMonth()];
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
  }
  

  
  return value;
}

async function fetchFamilyMembers() {
  if (!props.member?.family_no) return;
  
  loadingFamilyMembers.value = true;
  try {
    // Pass the current member ID to exclude them from the list
    const response = await axios.get(`/member/family-details/${props.member.family_no}`, {
      params: {
        exclude_member_id: props.member.id
      }
    });
    
    if (response.data && response.data.members) {
      familyMembers.value = response.data.members;
    } else {
      familyMembers.value = [];
    }
  } catch (error) {
    console.error('Error fetching family members:', error);
    familyMembers.value = [];
  } finally {
    loadingFamilyMembers.value = false;
  }
}

async function fetchMemberDetails() {
  if (!props.member?.id) return;
  
  loadingMemberDetails.value = true;
  try {
    console.log('Fetching member details for ID:', props.member.id);
    const response = await axios.get(`/member/${props.member.id}/details`);
    console.log('Member details response:', response.data);
    
    if (response.data) {
      memberDetails.value = response.data;
    }
  } catch (error) {
    console.error('Error fetching member details:', error);
    memberDetails.value = null;
  } finally {
    loadingMemberDetails.value = false;
  }
}

function closeModal() {
  emit('update:modelValue', false);
}

function handleTab(e: KeyboardEvent) {
  if (!modalRef.value || !props.modelValue) return;

  const focusable = modalRef.value.querySelectorAll<HTMLElement>('a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])');
  const first = focusable[0];
  const last = focusable[focusable.length - 1];

  if (e.key === 'Tab') {
    if (e.shiftKey) {
      if (document.activeElement === first) {
        e.preventDefault();
        last.focus();
      }
    } else {
      if (document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }

  if (e.key === 'Escape') {
    closeModal();
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleTab);
});

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleTab);
});

// Watch for member changes to fetch family members
import { watch } from 'vue';
watch(() => props.member, (newMember) => {
  if (newMember) {
    fetchMemberDetails();
    if (currentTab.value === 'community') {
      fetchFamilyMembers();
    }
  }
}, { immediate: true });

// Watch for tab changes to fetch family members when community tab is selected
watch(() => currentTab.value, (newTab) => {
  if (newTab === 'community' && props.member) {
    fetchFamilyMembers();
  }
});
</script>

<template>
  <transition name="fade-scale">
    <div v-if="modelValue" class="bg-opacity-60 fixed inset-0 z-50 flex items-center justify-center bg-black p-4" role="dialog" aria-modal="true">
      <div
        ref="modalRef"
        class="w-full max-w-6xl h-[90vh] flex flex-col rounded-2xl border border-gray-200 bg-gray-50 shadow-2xl transition-all duration-200"
        style="overflow-x:hidden;"
      >
        <!-- Header -->
        <div class="flex items-center justify-between rounded-t-2xl bg-blue-600 px-6 py-4">
          <h3 class="text-2xl font-bold text-white">Member Details</h3>
          <button class="rounded-full bg-white/20 p-2 text-white hover:bg-white/40" @click="closeModal" aria-label="Close Modal">
            <span class="text-2xl leading-none">×</span>
          </button>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 border-b border-gray-200 bg-white px-6 pt-4">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            class="rounded-full px-4 py-2 text-sm font-medium transition-all duration-150"
            :class="currentTab === tab.key
              ? 'bg-blue-600 text-white shadow'
              : 'bg-gray-100 text-gray-700 hover:bg-blue-100'"
            @click="currentTab = tab.key"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Loading indicator for member details -->
        <div v-if="loadingMemberDetails" class="bg-white px-6 py-4">
          <div class="flex items-center justify-center py-8">
            <div class="text-gray-500">Loading member details...</div>
          </div>
        </div>

        <!-- Tab Content -->
        <div v-if="member" class="bg-white px-6 py-6 flex-1 overflow-y-auto">
          <div
            v-for="tab in tabs"
            :key="tab.key"
            v-show="currentTab === tab.key"
            class="space-y-6"
          >
            <!-- Regular fields grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
              <!-- Loading indicator for leadership tab -->
              <div v-if="tab.key === 'leadership' && loadingMemberDetails" class="col-span-full flex justify-center py-8">
                <div class="text-gray-500">Loading leadership roles...</div>
              </div>
              
              <div
                v-for="field in tab.fields"
                :key="field.key"
                v-show="tab.key !== 'leadership' || !loadingMemberDetails"
                class="rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 shadow-sm min-h-[80px]"
                :class="{ 
                  'bg-blue-50 border-blue-200': ['cells_and_associations', 'scc_heads', 'ppc_heads', 'cluster_heads'].includes(field.key),
                  'min-h-[120px]': ['cells_and_associations', 'scc_heads', 'ppc_heads', 'cluster_heads'].includes(field.key)
                }"
              >
                <div class="text-xs font-semibold text-gray-500 mb-2">
                  {{ field.label }}
                  <span v-if="['cells_and_associations', 'scc_heads', 'ppc_heads', 'cluster_heads'].includes(field.key) && memberDetails?.[field.key]?.length" 
                        class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                    {{ memberDetails[field.key].length }} {{ memberDetails[field.key].length === 1 ? 'item' : 'items' }}
                  </span>
                </div>
                <div 
                  class="text-base font-medium text-gray-800 break-words"
                  :class="{ 
                    'line-clamp-3': !['cells_and_associations', 'scc_heads', 'ppc_heads', 'cluster_heads'].includes(field.key),
                    'whitespace-pre-line': field.key === 'cells_and_associations' || field.key === 'scc_heads' || field.key === 'ppc_heads' || field.key === 'cluster_heads',
                    'max-h-32 overflow-y-auto': ['cells_and_associations', 'scc_heads', 'ppc_heads', 'cluster_heads'].includes(field.key)
                  }"
                >
                  <template v-if="field.key === 'church_code'">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                      {{ formatFieldValue(field.key, page.props.church_code) }}
                    </span>
                  </template>
                  <template v-else>
                    {{ formatFieldValue(field.key, 
                      field.key === 'scc_heads' || field.key === 'ppc_heads' || field.key === 'cluster_heads' || field.key === 'cells_and_associations' 
                        ? memberDetails?.[field.key] 
                        : member[field.key]
                    ) }}
                  </template>
                </div>
              </div>
            </div>

            <!-- Debug section for leadership tab (development only) -->
            <!-- <div v-if="tab.key === 'leadership' && memberDetails && import.meta.env.DEV" class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
              <h5 class="text-sm font-semibold text-yellow-800 mb-2">🔍 Debug Info (Development Only)</h5>
              <div class="text-xs text-yellow-700 space-y-1">
                <div><strong>memberDetails loaded:</strong> {{ !!memberDetails }}</div>
                <div><strong>SCC Heads:</strong> {{ memberDetails.scc_heads?.length || 0 }} items</div>
                <div><strong>PPC Heads:</strong> {{ memberDetails.ppc_heads?.length || 0 }} items</div>
                <div><strong>Cluster Heads:</strong> {{ memberDetails.cluster_heads?.length || 0 }} items</div>
                <div><strong>Cells:</strong> {{ memberDetails.cells_and_associations?.length || 0 }} items</div>
                <div><strong>Raw SCC Heads:</strong> {{ JSON.stringify(memberDetails.scc_heads) }}</div>
              </div>
            </div> -->

            <!-- Family Members Table for Community tab -->
            <div v-if="tab.key === 'community' && member.family_no" class="mt-8">
              <div class="border-t border-gray-200 pt-6">
                <h4 class="mb-4 text-lg font-semibold text-gray-800">Family Members</h4>
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                  <p class="text-sm text-blue-800">
                    <strong>💡 Family Tree Help:</strong> This table shows family members ordered by generation (oldest first). 
                    Father, Mother, and Spouse columns help identify missing relationships. 
                    If the family tree isn't showing correctly, check these columns for missing data.
                  </p>
                </div>
                
                <!-- Relationship Summary -->
                <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                  <div class="grid grid-cols-5 gap-4 text-sm">
                    <div>
                      <span class="font-semibold text-gray-700">Total Members:</span>
                      <span class="ml-2 text-gray-600">{{ familyMembers.length }}</span>
                    </div>
                    <div>
                      <span class="font-semibold text-gray-700">Internal:</span>
                      <span class="ml-2 text-blue-600">{{ familyMembers.filter(m => m.source === 'Member').length }}</span>
                    </div>
                    <div>
                      <span class="font-semibold text-gray-700">External:</span>
                      <span class="ml-2 text-orange-600">{{ familyMembers.filter(m => m.source === 'External').length }}</span>
                    </div>
                    <!-- <div>
                      <span class="font-semibold text-gray-700">With Fathers:</span>
                      <span class="ml-2 text-green-600">{{ familyMembers.filter(m => m.father).length }}</span>
                    </div>
                    <div>
                      <span class="font-semibold text-gray-700">With Mothers:</span>
                      <span class="ml-2 text-green-600">{{ familyMembers.filter(m => m.mother).length }}</span>
                    </div> -->
                  </div>
                </div>
                <div class="max-h-96 overflow-y-auto">
                
                <div v-if="loadingFamilyMembers" class="flex justify-center py-8">
                  <div class="text-gray-500">Loading family members...</div>
                </div>
                
                <div v-else-if="familyMembers.length === 0" class="text-center py-8 text-gray-500">
                  No family members found
                </div>
                
                <div v-else class="overflow-x-auto rounded-lg border border-gray-200">
                  <table class="w-full">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date of Birth</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Age</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Father</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mother</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Spouse</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                      <tr v-for="familyMember in familyMembers" :key="familyMember.id" class="bg-white hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-800">
                          <div class="font-medium">{{ familyMember.first_name }} {{ familyMember.last_name }}</div>
                          <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">Gen {{ familyMember.generation || 0 }}</span>
                            <span v-if="familyMember.source === 'External'" 
                                  class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                              External
                            </span>
                          </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                          <span v-if="familyMember.source === 'External' && !familyMember.date_of_birth" class="text-gray-400">N/A</span>
                          <span v-else>{{ familyMember.date_of_birth ? formatDate(familyMember.date_of_birth) : '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                          <span v-if="familyMember.source === 'External' && !familyMember.date_of_birth" class="text-gray-400">N/A</span>
                          <span v-else>{{ calculateAge(familyMember.date_of_birth) || '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                          <div v-if="familyMember.father" class="text-blue-600">
                            {{ familyMember.father.name }}
                          </div>
                          <div v-else class="text-gray-400">—</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                          <div v-if="familyMember.mother" class="text-green-600">
                            {{ familyMember.mother.name }}
                          </div>
                          <div v-else class="text-gray-400">—</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                          <div v-if="familyMember.spouse" class="text-purple-600">
                            {{ familyMember.spouse.name }}
                          </div>
                          <div v-else class="text-gray-400">—</div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

        <!-- Footer -->
        <div class="sticky bottom-0 left-0 right-0 z-10 flex justify-end rounded-b-2xl bg-gray-100 px-6 py-4">
          <button @click="closeModal" class="rounded bg-blue-600 px-6 py-2 text-lg font-semibold text-white shadow hover:bg-blue-700 transition">
            Close
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

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

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
