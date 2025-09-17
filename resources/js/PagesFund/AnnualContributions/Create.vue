<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-[#ffffff] overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex items-center mb-6">
            <Link 
              href="/fund/annual-contributions"
              class="mr-4 text-gray-500 hover:text-gray-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </Link>
            <h1 class="text-2xl font-semibold">Add New Contribution</h1>
          </div>
          
          <!-- Family Summary Section -->
          <!-- <div v-if="form.family_no" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-semibold text-blue-900">Family: {{ form.family_no }}</h3>
              <span class="text-sm text-blue-600">{{ selectedFamilyMembers?.length || 0 }} members</span>
            </div> -->
            
            <!-- Quick Stats -->
            <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
              <div class="bg-[#ffffff] rounded-lg p-3 border border-blue-100">
                <div class="text-2xl font-bold text-blue-600">{{ totalContributions || 0 }}</div>
                <div class="text-sm text-gray-600">Total Contributions</div>
              </div>
              <div class="bg-[#ffffff] rounded-lg p-3 border border-blue-100">
                <div class="text-2xl font-bold text-green-600">₹{{ totalPaid || 0 }}</div>
                <div class="text-sm text-gray-600">Total Paid</div>
              </div>
              <div class="bg-[#ffffff] rounded-lg p-3 border border-blue-100">
                <div class="text-2xl font-bold text-orange-600">₹{{ totalPending || 0 }}</div>
                <div class="text-sm text-gray-600">Total Pending</div>
              </div>
                                            <div class="bg-[#ffffff] rounded-lg p-3 border border-blue-100">
                 <div class="text-2xl font-bold text-purple-600">
                   {{ form.years.length > 0 ? form.years.join(', ') : 'No years selected' }}
                 </div>
                 <div class="text-sm text-gray-600">Selected Years</div>
               </div>
             </div>
           </div>
          

          
          <!-- Form Layout -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form (full width when sidebar removed) -->
            <div class="lg:col-span-3">
              <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Family Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Family Information</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Family Number -->
                    <div>
                      <label for="family_no" class="block text-sm font-medium text-gray-700 mb-2">
                        Family Number <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <input 
                          id="family_no"
                          v-model="form.family_no"
                          type="text"
                          :class="[
                            'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                            form.errors.family_no ? 'border-red-300' : 'border-gray-300'
                          ]"
                          placeholder="Enter family number or search for member"
                          @input="searchMembersForFamily"
                          @focus="showFamilySearchResults = true"
                          @click.stop="showFamilySearchResults = true"
                          required
                        />
                        
                        <!-- Family Number Search Results -->
                        <div v-if="familySearchResults.length > 0 && showFamilySearchResults" 
                             class="absolute z-10 w-full mt-1 bg-[#ffffff] border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                          <div v-for="member in familySearchResults" 
                               :key="member.id"
                               @click="selectMemberForFamily(member)"
                               class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0">
                            <div class="font-medium">{{ member.full_name }}</div>
                            <div class="text-sm text-gray-500">Community No: {{ member.community.split('-')[0].trim() }} | Family: {{ member.family_no || 'N/A' }}</div>
                            <div class="text-sm text-gray-500">Address: {{ member.current_add1 }} | {{ member.contact_no_1 }}</div>
                          </div>
                        </div>
                        
                        <!-- No Results Message for Family Search -->
                        <div v-if="showFamilySearchResults && familySearchResults.length === 0 && form.family_no.length >= 2" 
                             class="absolute z-10 w-full mt-1 bg-[#ffffff] border border-gray-300 rounded-md shadow-lg p-3 text-center text-gray-500">
                          No members found
                        </div>
                      </div>
                      <p v-if="form.errors.family_no" class="mt-1 text-sm text-red-600">
                        {{ form.errors.family_no }}
                      </p>
                      <p class="mt-1 text-sm text-gray-500">
                        Type family number directly or search for a member to auto-fill
                      </p>
                      <div v-if="selectedFamilyMember" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="font-medium">{{ selectedFamilyMember.full_name }}</div>
                        <div class="text-sm text-gray-600">Community No: {{ selectedFamilyMember.community.split('-')[0].trim() }} | Family: {{ selectedFamilyMember.family_no || 'N/A' }}</div>
                        <div class="text-sm text-gray-600">Address: {{ selectedFamilyMember.current_add1 }} | {{ selectedFamilyMember.contact_no_1 }}</div>
                      </div>
                    </div>

                    
                  </div>
                </div>

                <!-- Payment Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Amount -->
                    <div>
                      <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount (₹) <span class="text-red-500">*</span>
                      </label>
                      <input 
                        id="amount"
                        v-model="form.amount"
                        type="number"
                        step="0.01"
                        min="0.01"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.amount ? 'border-red-300' : 'border-gray-300'
                        ]"
                        placeholder="0.00"

                      />
                      <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">
                        {{ form.errors.amount }}
                      </p>
                      
                      
                    </div>

                    <!-- Start Date -->
                    <div>
                      <label for="start_date" class="block text-xs font-medium text-gray-700 mb-3">
                        Start Date <span class="text-red-500">*</span>
                      </label>
                      <input 
                        id="start_date"
                        v-model="form.start_date"
                        type="date"
                        :class="[
                          'w-full px-2 py-1.5 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.start_date ? 'border-red-300' : 'border-gray-300'
                        ]"
                        required
                      />
                      <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">
                        {{ form.errors.start_date }}
                      </p>
                    </div>

                    <!-- End Date -->
                    <div>
                      <label for="end_date" class="block text-xs font-medium text-gray-700 mb-3">
                        End Date <span class="text-red-500">*</span>
                      </label>
                      <input 
                        id="end_date"
                        v-model="form.end_date"
                        type="date"
                        :class="[
                          'w-full px-2 py-1.5 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.end_date ? 'border-red-300' : 'border-gray-300'
                        ]"
                        required
                      />
                      <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">
                        {{ form.errors.end_date }}
                      </p>
                    </div>

                    <!-- Status -->
                    <div>
                      <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                      </label>
                      <select 
                        id="status"
                        v-model="form.status"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.status ? 'border-red-300' : 'border-gray-300'
                        ]"
                        required
                      >
                        <option value="">Select Status</option>
                        <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                          {{ status.label }}
                        </option>
                      </select>
                      <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                        {{ form.errors.status }}
                      </p>
                    </div>

                    <!-- Fund Category -->
                    <div>
                      <label for="fund_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Fund Category
                      </label>
                      <select 
                        id="fund_category_id"
                        v-model="form.fund_category_id"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.fund_category_id ? 'border-red-300' : 'border-gray-300'
                        ]"
                      >
                        <option value="">Select Category</option>
                        <option v-for="category in filterOptions?.fund_categories || []" :key="category.id" :value="category.id">
                          {{ category.name }}
                        </option>
                      </select>
                      <p v-if="form.errors.fund_category_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.fund_category_id }}
                      </p>
                    </div>

                    <!-- Payment Method -->
                    <div>
                      <label for="payment_method_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Method
                      </label>
                      <select 
                        id="payment_method_id"
                        v-model="form.payment_method_id"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.payment_method_id ? 'border-red-300' : 'border-gray-300'
                        ]"
                      >
                        <option value="">Select Method</option>
                        <option v-for="method in filterOptions?.payment_methods || []" :key="method.id" :value="method.id">
                          {{ method.name }}
                        </option>
                      </select>
                      <p v-if="form.errors.payment_method_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.payment_method_id }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Member Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Member Information</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Member Search -->
                    <div>
                      <label for="member_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Family Member
                      </label>
                      <div class="relative">
                        <select 
                          id="member"
                          v-model="form.member_id"
                          :class="[
                            'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                            form.errors.member_id ? 'border-red-300' : 'border-gray-300'
                          ]"
                          :disabled="!form.family_no"
                        >
                          <option value="">Select a family member</option>
                          <option v-for="member in familyMembers" :key="member.id" :value="member.id">
                            {{ member.full_name }} 
                          </option>
                        </select>
                        <p v-if="!form.family_no" class="mt-1 text-sm text-gray-500">
                          Please select a family number first
                        </p>
                        <p v-else-if="familyMembers.length === 0" class="mt-1 text-sm text-gray-500">
                          No family members found
                        </p>
                      </div>
                      <p class="mt-1 text-sm text-gray-500">
                        Select a family member from the dropdown to link this contribution
                      </p>
                    </div>

                    <!-- Paid By Name (fallback) -->
                    <div>
                      <label for="paid_by_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Paid By Name
                      </label>
                      <input 
                        id="paid_by_name"
                        v-model="form.paid_by_name"
                        type="text"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.paid_by_name ? 'border-red-300' : 'border-gray-300'
                        ]"
                        placeholder="Enter payer's name"
                        :disabled="!!form.member_id"
                      />
                      <p v-if="form.errors.paid_by_name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.paid_by_name }}
                      </p>
                      <p class="mt-1 text-sm text-gray-500">
                        {{ form.member_id ? 'Auto-filled from member' : 'Required if no member selected' }}
                      </p>
                    </div>

                    <!-- Contact Number -->
                    <div>
                      <label for="contact_no" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Number
                      </label>
                      <input 
                        id="contact_no"
                        v-model="form.contact_no"
                        type="tel"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.contact_no ? 'border-red-300' : 'border-gray-300'
                        ]"
                        placeholder="Enter contact number"
                      />
                      <p v-if="form.errors.contact_no" class="mt-1 text-sm text-red-600">
                        {{ form.errors.contact_no }}
                      </p>
                    </div>
                  </div>

                  <!-- Selected Member Display -->
                  <div v-if="form.member_id && selectedMember" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <div class="flex items-center justify-between">
                      <div>
                        <h4 class="font-medium text-blue-900">Selected Member</h4>
                        <p class="text-sm text-blue-700">{{ selectedMember.full_name }}</p>
                        <p class="text-sm text-blue-600">Member No: {{ selectedMember.member_no }} | Family: {{ selectedMember.family_no || 'N/A' }}</p>
                      </div>
                      <button 
                        type="button"
                        @click="clearMemberSelection"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Additional Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                  
                  <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                      Notes
                    </label>
                    <textarea 
                      id="notes"
                      v-model="form.notes"
                      rows="3"
                      :class="[
                        'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                        form.errors.notes ? 'border-red-300' : 'border-gray-300'
                      ]"
                      placeholder="Any additional notes about this contribution..."
                    ></textarea>
                    <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                      {{ form.errors.notes }}
                    </p>
                  </div>
                </div>
              </form>
            </div>
            
            
          </div>

                     <!-- Sticky Form Actions -->
           <div class="fixed bottom-0 left-0 right-0 bg-[#ffffff] border-t border-gray-200 py-4 z-50">
             <div class="max-w-7xl mx-auto px-6">
               <div class="flex justify-start space-x-3" style="margin-left: 200px; margin-right: 0;">
                 <Link 
                   href="/fund/annual-contributions"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-[#ffffff] hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3b82f6]"
                 >
                   Cancel
                 </Link>
                 <button 
                   type="submit"
                   :disabled="form.processing"
                   @click="submitForm"
                   class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3b82f6] disabled:opacity-50 disabled:cursor-not-allowed"
                 >
                   <span v-if="form.processing">Creating...</span>
                   <span v-else>Create Contribution</span>
                 </button>
               </div>
             </div>
           </div>

          <!-- Contribution History Section (Moved to bottom) -->
          <div v-if="form.family_no && contributionHistory.length > 0" class="bg-[#ffffff] border border-gray-200 rounded-lg mt-6">
            <div class="px-4 py-3 border-b border-gray-200">
              <h4 class="text-lg font-medium text-gray-900">Contribution History</h4>
              <p class="text-sm text-gray-600">Previous contributions for this family</p>
            </div>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th @click="toggleCategorySort" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none">
                      Category <span class="ml-1 text-[10px]">{{ categorySortAsc ? '▲' : '▼' }}</span>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid By</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                  </tr>
                </thead>
                <tbody class="bg-[#ffffff] divide-y divide-gray-200">
                  <tr v-for="contribution in sortedContributionHistory" :key="contribution.id" class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ formatDate(contribution.start_date) }} - {{ formatDate(contribution.end_date) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ contribution.category_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">₹{{ contribution.amount }}</td>
                    <td class="px-4 py-3">
                      <span :class="getStatusBadgeClass(contribution.status)">
                        {{ contribution.status }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ contribution.paid_by || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ formatDate(contribution.date_of_payment) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ contribution.payment_method || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Bottom spacing for sticky buttons -->
          <div class="h-20"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { SelectInput } from '@/components/ui/select';
import { useToast } from '@/composables/useToast';

defineOptions({
    layout: AppLayout
});

const props = withDefaults(defineProps<{
  filterOptions?: any;
}>(), {
  filterOptions: () => ({
    fund_categories: [],
    payment_methods: []
  })
});

const { success, error } = useToast();

// Form handling
const form = useForm({
  family_no: '',
  amount: '',
  payment_method_id: '',
  fund_category_id: '',
  start_date: '',
  end_date: '',
  status: '',
  member_id: '',
  paid_by_name: '',
  contact_no: '',
  notes: ''
});

// Member search
const memberSearch = ref('');
const memberSearchResults = ref<any[]>([]);
const selectedMember = ref<any>(null);
const showMemberResults = ref(false);

// Family number search
const familySearchResults = ref<any[]>([]);
const showFamilySearchResults = ref(false);
const selectedFamilyMember = ref<any>(null);

// Family members for the selected family
const familyMembers = ref<any[]>([]);

// Contribution history and pending amounts
const contributionHistory = ref<any[]>([]);
const categorySortAsc = ref(true);
const sortedContributionHistory = computed(() => {
  const list = [...contributionHistory.value];
  return list.sort((a, b) => {
    const an = (a.category_name || '').toString().toLowerCase();
    const bn = (b.category_name || '').toString().toLowerCase();
    if (an < bn) return categorySortAsc.value ? -1 : 1;
    if (an > bn) return categorySortAsc.value ? 1 : -1;
    return 0;
  });
});

const toggleCategorySort = () => {
  categorySortAsc.value = !categorySortAsc.value;
};
const pendingAmountsByCategory = ref<any[]>([]);
const totalContributions = ref(0);
const totalPaid = ref(0);
const totalPending = ref(0);
const totalPendingAmount = ref(0);
const selectedFamilyMembers = ref<any[]>([]);
const pendingYears = ref<number[]>([]);

// Computed properties
const currentYear = computed(() => new Date().getFullYear());

// Year selection removed; using date range now

const statusOptions = [
  { value: 'pending', label: 'Pending' },
  { value: 'partial', label: 'Partial' },
  { value: 'paid', label: 'Paid' },
  { value: 'cancelled', label: 'Cancelled' },
  { value: 'refunded', label: 'Refunded' }
];

// Methods

const searchMembersForFamily = async () => {
  
  if (form.family_no.length < 2) {
    familySearchResults.value = [];
    showFamilySearchResults.value = false;
    return;
  }

  try {
    const url = `/member/search-members?query=${encodeURIComponent(form.family_no)}`;
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    });
    
    if (response.ok) {
      const data = await response.json();
      familySearchResults.value = data || [];
      familyMembers.value = data || [];
      showFamilySearchResults.value = data && data.length > 0;
    } else {
      console.error('Response not ok:', response.status, response.statusText);
      showFamilySearchResults.value = false;
    }
  } catch (error) {
    console.error('Error searching members for family:', error);
    familySearchResults.value = [];
    showFamilySearchResults.value = false;
  }
};

// Fetch family members by family number
const fetchFamilyMembers = async (familyNo: string) => {
  if (!familyNo) {
    familyMembers.value = [];
    return;
  }

  try {
    const url = `/member/family-members/${encodeURIComponent(familyNo)}`;
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    });
    if (response.ok) {
      const data = await response.json();
      // Map to the format used in the dropdown
      familyMembers.value = (Array.isArray(data) ? data : []).map((m: any) => ({
        id: m.id,
        full_name: m.first_name && m.last_name ? `${m.first_name} ${m.last_name}` : (m.full_name || ''),
        member_no: m.member_no,
        family_no: familyNo,
      }));
    } else {
      console.error('Response not ok:', response.status, response.statusText);
      familyMembers.value = [];
    }
  } catch (error) {
    console.error('Error fetching family members:', error);
    familyMembers.value = [];
  }
};
watch(() => showFamilySearchResults, () => {
  // No-op; don't sync dropdown with search list
});

// Fetch family contribution history
const fetchFamilyContributionHistory = async (familyNo: string) => {
  try {
    const response = await fetch(`/fund/family-contributions/${familyNo}`);
    const data = await response.json();
    
    contributionHistory.value = data.contributions || [];
    totalContributions.value = data.total_contributions || 0;
    totalPaid.value = data.total_paid || 0;
    totalPending.value = data.total_pending || 0;
    
    // Pending years removed with year logic
  } catch (error) {
    console.error('Error fetching contribution history:', error);
    contributionHistory.value = [];
    totalContributions.value = 0;
    totalPaid.value = 0;
    totalPending.value = 0;
    pendingYears.value = [];
  }
};

// Year pending calculation removed

// Fetch pending amounts by category (simplified - just calls calculatePendingYears)
const fetchPendingAmountsByCategory = async (familyNo: string) => {
  try {
    // Year-based pending logic removed
    // Don't fetch again if we already have the data
    
  } catch (error) {
    console.error('Error fetching pending years:', error);
    pendingYears.value = [];
  }
};

const selectMember = (member: any) => {
  selectedMember.value = member;
  form.member_id = member.id;
  form.paid_by_name = member.full_name;
  form.family_no = member.family_no || ''; // Auto-fill family_no
  memberSearch.value = member.full_name;
  showMemberResults.value = false;
  memberSearchResults.value = [];
  
  // Fetch family members for the selected family
  fetchFamilyMembers(member.family_no || '');
  
  // Fetch contribution history and pending amounts when member is selected
  if (member.family_no) {
    fetchFamilyContributionHistory(member.family_no);
    fetchPendingAmountsByCategory(member.family_no);
  }
};

const selectMemberForFamily = (member: any) => {
  form.family_no = member.family_no || ''; // Auto-fill family_no
  familySearchResults.value = []; // Clear results
  showFamilySearchResults.value = false;
  selectedFamilyMember.value = member;
  
  // Fetch family members for the selected family
  fetchFamilyMembers(member.family_no || '');
  
  // Fetch contribution history and pending amounts when family is selected
  if (member.family_no) {
    fetchFamilyContributionHistory(member.family_no);
    fetchPendingAmountsByCategory(member.family_no);
  }
  
  // Also clear member search if it was previously selected
  if (selectedMember.value && selectedMember.value.family_no === member.family_no) {
    // This is the same family, keep the member selected
  } else {
    // Different family, clear member selection
    selectedMember.value = null;
    form.member_id = '';
    form.paid_by_name = '';
    memberSearch.value = '';
    showMemberResults.value = false;
    memberSearchResults.value = [];
  }
};

const clearMemberSelection = () => {
  selectedMember.value = null;
  form.member_id = '';
  form.paid_by_name = '';
  form.family_no = ''; // Clear family_no
  selectedFamilyMember.value = null;
  memberSearch.value = '';
  showMemberResults.value = false;
  memberSearchResults.value = [];
  
  // Also clear family search results
  familySearchResults.value = [];
  showFamilySearchResults.value = false;
  
  // Clear family members
  familyMembers.value = [];
  
  // Clear contribution history and pending amounts
  contributionHistory.value = [];
  pendingAmountsByCategory.value = [];
  totalContributions.value = 0;
  totalPaid.value = 0;
  totalPending.value = 0;
  totalPendingAmount.value = 0;
  pendingYears.value = [];
};

const submitForm = () => {
  // Auto-fill paid_by_name if member is selected but paid_by_name is empty
  if (form.member_id && !form.paid_by_name) {
    form.paid_by_name = selectedMember.value?.full_name || '';
  }

  // Validate that either member_id or paid_by_name is provided
  if (!form.member_id && !form.paid_by_name) {
    error('Please either select a member or enter the payer\'s name.');
    return;
  }

  // Validate amount
  if (!form.amount || parseFloat(form.amount) <= 0) {
    error('Please enter a valid amount for the contribution.');
    return;
  }

  if (!form.start_date || !form.end_date) {
    error('Please select start and end dates.');
    return;
  }

  form.post('/fund/annual-contributions', {
    onSuccess: () => {
      success('Annual contribution created successfully!');
      // Form will redirect on success
    },
    onError: (errors) => {
      console.error('Form validation errors:', errors);
      error('Please check the form for errors and try again.');
    }
  });
};

// Utility methods
const getStatusBadgeClass = (status: string) => {
  const classes = {
    'paid': 'px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full',
    'pending': 'px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full',
    'partial': 'px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full',
    'cancelled': 'px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full',
    'refunded': 'px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full'
  };
  return classes[status as keyof typeof classes] || classes.pending;
};

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-IN');
};

// Removed split amount logic (no years)

// Close member search results when clicking outside
const closeMemberSearch = (event: Event) => {
  const target = event.target as HTMLElement;
  const searchContainer = document.getElementById('member_search')?.closest('.relative');
  
  if (searchContainer && !searchContainer.contains(target)) {
    showMemberResults.value = false;
  }
};

// Close family search results when clicking outside
const closeFamilySearch = (event: Event) => {
  const target = event.target as HTMLElement;
  const searchContainer = document.getElementById('family_no')?.closest('.relative');
  
  if (searchContainer && !searchContainer.contains(target)) {
    showFamilySearchResults.value = false;
  }
};

// Watch for member search changes
watch(memberSearch, () => {
  if (!memberSearch.value) {
    memberSearchResults.value = [];
    showMemberResults.value = false;
  }
});

// Watch for family search changes
watch(() => form.family_no, (newValue) => {
  if (!newValue) {
    familySearchResults.value = [];
    showFamilySearchResults.value = false;
    
    // Clear family members
    familyMembers.value = [];
    
    // Clear contribution history and pending amounts
    contributionHistory.value = [];
    pendingAmountsByCategory.value = [];
    totalContributions.value = 0;
    totalPaid.value = 0;
    totalPending.value = 0;
    totalPendingAmount.value = 0;
    pendingYears.value = [];
  } else {
    // Fetch members for selected family
    fetchFamilyMembers(newValue);
  }
});

// Removed year watcher

// Watch for fund category changes to fetch pending amounts
watch(() => form.fund_category_id, (newCategoryId) => {
  // No-op: year-based pending logic removed
});

// Initialize form with current dates
onMounted(() => {
  const today = new Date().toISOString().split('T')[0];
  form.start_date = today;
  form.end_date = today;
  
  // Set default fund category to "Annual Contributions" if available
  if (props.filterOptions?.fund_categories) {
    const annualContributionsCategory = props.filterOptions.fund_categories.find(
      (cat: any) => cat.name.toLowerCase().includes('annual') || cat.name.toLowerCase().includes('contributions')
    );
    if (annualContributionsCategory) {
      form.fund_category_id = annualContributionsCategory.id;
    }
  }
  
  // Add click outside handlers
  document.addEventListener('click', closeMemberSearch);
  document.addEventListener('click', closeFamilySearch);
});
</script>
