<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-[#ffffff] overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex items-center mb-6">
            <Link 
              :href="route('fund.annual-contributions.index')"
              class="mr-4 text-gray-500 hover:text-gray-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </Link>
            <h1 class="text-2xl font-semibold">Edit Annual Contribution</h1>
          </div>
          
          <!-- Form Layout -->
                                                          <div class="grid grid-cols-1 gap-6">
              <!-- Main Form (Full Width) -->
              <div>
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
                      <input 
                        id="family_no"
                        v-model="form.family_no"
                        type="text"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.family_no ? 'border-red-300' : 'border-gray-300'
                        ]"
                        placeholder="Enter family number"
                        required
                      />
                      <p v-if="form.errors.family_no" class="mt-1 text-sm text-red-600">
                        {{ form.errors.family_no }}
                      </p>
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
                      <DateInput
                        v-model="form.start_date"
                        class="w-full"
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
                      <DateInput
                        v-model="form.end_date"
                        class="w-full"
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

                    <!-- Transaction Reference -->
                    <div>
                      <label for="transaction_reference" class="block text-sm font-medium text-gray-700 mb-2">
                        Transaction Reference
                      </label>
                      <input
                        id="transaction_reference"
                        v-model="form.transaction_reference"
                        type="text"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.transaction_reference ? 'border-red-300' : 'border-gray-300'
                        ]"
                        placeholder="Check No, UPI ID, etc."
                      />
                      <p v-if="form.errors.transaction_reference" class="mt-1 text-sm text-red-600">
                        {{ form.errors.transaction_reference }}
                      </p>
                      <p class="mt-1 text-sm text-gray-500">
                        Enter check number, UPI transaction ID, or other payment reference
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Member Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Member Information</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Member -->
                    <div>
                      <label for="member_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Family Member
                      </label>
                      <select 
                        id="member_id"
                        v-model="form.member_id"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.member_id ? 'border-red-300' : 'border-gray-300'
                        ]"
                        @change="handleMemberChange"
                      >
                        <option value="">Select a family member</option>
                        <option v-for="member in members" :key="member.id" :value="member.id">
                          {{ member.first_name }} {{ member.last_name }}
                        </option>
                      </select>
                      <p v-if="form.errors.member_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.member_id }}
                      </p>
                    </div>

                                         <!-- Paid By Name -->
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
                  
          <!-- Contribution History (bottom) -->
          <div class="bg-[#ffffff] border border-gray-200 rounded-lg mt-6">
            <div class="px-4 py-3 border-b border-gray-200">
              <h4 class="text-lg font-medium text-gray-900">Contribution History</h4>
              <p class="text-sm text-gray-600">Previous contributions for this family</p>
                  </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid By</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                  </tr>
                </thead>
                                <tbody class="bg-[#ffffff] divide-y divide-gray-200">
                  <tr v-if="contributionHistory.length === 0" class="hover:bg-gray-50">
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                      No contribution history found for this family
                    </td>
                  </tr>
                  <tr v-for="c in contributionHistory" :key="c.id" class="hover:bg-gray-50">
                                         <td class="px-4 py-3 text-sm text-gray-900">{{ c.start_date ? new Date(c.start_date).toLocaleDateString() : '-' }} - {{ c.end_date ? new Date(c.end_date).toLocaleDateString() : '-' }}</td>
                     <td class="px-4 py-3 text-sm text-gray-900">{{ c.category_name || '-' }}</td>
                     <td class="px-4 py-3 text-sm text-gray-900">₹{{ c.amount || '0.00' }}</td>
                     <td class="px-4 py-3">
                       <span :class="getStatusTextColor(c.status)">{{ c.status || '-' }}</span>
                     </td>
                     <td class="px-4 py-3 text-sm text-gray-900">{{ c.paid_by || '-' }}</td>
                     <td class="px-4 py-3 text-sm text-gray-900">{{ c.date_of_payment ? new Date(c.date_of_payment).toLocaleDateString() : '-' }}</td>
                     <td class="px-4 py-3 text-sm text-gray-900">{{ c.payment_method || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Sticky Form Actions -->
          <div class="fixed bottom-0 left-0 right-0 bg-[#ffffff] border-t border-gray-200 py-4 z-50">
            <div class="max-w-7xl mx-auto px-6">
              <div class="flex justify-start space-x-3" style="margin-left: 200px; margin-right: 0;">
                <Link 
                  :href="route('fund.annual-contributions.index')"
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
                  <span v-if="form.processing">Updating...</span>
                  <span v-else>Update Contribution</span>
                </button>
              </div>
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
import { onMounted } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { DateInput } from '@/components/ui/date-input';
import { formatDateForDisplay } from '@/lib/utils';
import { ref } from 'vue';

defineOptions({
    layout: AppLayout
});

interface Props {
  contribution: {
    id: number;
    family_no: string;
    amount: string;
    payment_method_id: number | null;
    fund_category_id: number | null;
    start_date: string;
    end_date: string;
    status: string;
    member_id: number | null;
    paid_by_name: string | null;
    contact_no: string | null;
    notes: string | null;
  };
  filterOptions: {
    fund_categories: Array<{ id: number; name: string }>;
    payment_methods: Array<{ id: number; name: string }>;
  };
  members: Array<{
    id: number;
    first_name: string;
    last_name: string;
  }>;
}

const props = defineProps<Props>();

// Form handling
const form = useForm({
  family_no: props.contribution.family_no,
  amount: props.contribution.amount,
  payment_method_id: props.contribution.payment_method_id || '',
  transaction_reference: props.contribution.transaction_reference || '',
  fund_category_id: props.contribution.fund_category_id || '',
  start_date: props.contribution.start_date,
  end_date: props.contribution.end_date,
  status: props.contribution.status,
  member_id: props.contribution.member_id || '',
  paid_by_name: props.contribution.paid_by_name || '',
  contact_no: props.contribution.contact_no || '',
  notes: props.contribution.notes || ''
});

// Contribution history for this family (reusing bottom list like in create)
const contributionHistory = ref<any[]>([]);

const statusOptions = [
  { value: 'pending', label: 'Pending' },
  { value: 'partial', label: 'Partial' },
  { value: 'paid', label: 'Paid' },
  { value: 'cancelled', label: 'Cancelled' },
  { value: 'refunded', label: 'Refunded' }
];

// Methods
const handleMemberChange = () => {
  if (form.member_id) {
    const selectedMember = props.members.find(member => member.id === Number(form.member_id));
    if (selectedMember) {
      form.paid_by_name = `${selectedMember.first_name} ${selectedMember.last_name}`;
    }
  } else {
    form.paid_by_name = '';
  }
};

const submitForm = () => {
  // Submit the form
  form.put(route('fund.annual-contributions.update', props.contribution.id), {
    onSuccess: () => {
      // Form will redirect on success
    },
    onError: (errors) => {
      console.error('Form validation errors:', errors);
    }
  });
};

// Utility methods
const getStatusLabel = (status: string) => {
  const statusOption = statusOptions.find(option => option.value === status);
  return statusOption ? statusOption.label : status;
};

const getStatusTextColor = (status: string) => {
  const colors = {
    'paid': 'text-green-600',
    'pending': 'text-yellow-600',
    'partial': 'text-blue-600',
    'cancelled': 'text-red-600',
    'refunded': 'text-gray-600'
  };
  return colors[status as keyof typeof colors] || 'text-gray-600';
};

// Format dates to YYYY-MM-DD for date inputs
onMounted(() => {
  if (form.start_date) {
    const s = new Date(form.start_date);
    form.start_date = s.toISOString().split('T')[0];
  }
  if (form.end_date) {
    const e = new Date(form.end_date);
    form.end_date = e.toISOString().split('T')[0];
  }

  // Load contribution history for this family
  if (form.family_no) {
    fetch(`/fund/family-contributions/${encodeURIComponent(form.family_no)}`)
      .then(r => r.json())
             .then(data => {
         contributionHistory.value = data?.contributions || [];
         
       })
      .catch((error) => {
        console.error('Error fetching contribution history:', error);
        contributionHistory.value = [];
      });
  }
});
</script>
