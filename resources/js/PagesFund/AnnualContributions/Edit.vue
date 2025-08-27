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
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form (2 columns) -->
            <div class="lg:col-span-2">
              <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Family Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Family Information</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                    <!-- Year -->
                    <div>
                      <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        Year <span class="text-red-500">*</span>
                      </label>
                      <input 
                        id="year"
                        v-model="form.year"
                        type="number"
                        min="2020"
                        max="2030"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.year ? 'border-red-300' : 'border-gray-300'
                        ]"
                        required
                      />
                      <p v-if="form.errors.year" class="mt-1 text-sm text-red-600">
                        {{ form.errors.year }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Payment Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        required
                      />
                      <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">
                        {{ form.errors.amount }}
                      </p>
                    </div>

                    <!-- Payment Date -->
                    <div>
                      <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Date <span class="text-red-500">*</span>
                      </label>
                      <input 
                        id="payment_date"
                        v-model="form.payment_date"
                        type="date"
                        :class="[
                          'w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]',
                          form.errors.payment_date ? 'border-red-300' : 'border-gray-300'
                        ]"
                        required
                      />
                      <p v-if="form.errors.payment_date" class="mt-1 text-sm text-red-600">
                        {{ form.errors.payment_date }}
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
            
            <!-- Contribution Info Sidebar -->
            <div class="lg:col-span-1">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 sticky top-6">
                <h4 class="text-lg font-medium text-blue-900 mb-3">Contribution Details</h4>
                
                <div class="space-y-3">
                  <div class="bg-[#ffffff] rounded-lg p-3 border border-blue-100">
                    <div class="text-sm text-gray-600">Family Number</div>
                    <div class="text-lg font-bold text-blue-600">{{ form.family_no || '-' }}</div>
                  </div>
                  
                  <div class="bg-[#ffffff] rounded-lg p-3 border border-blue-100">
                    <div class="text-sm text-gray-600">Year</div>
                    <div class="text-lg font-bold text-blue-600">{{ form.year || '-' }}</div>
                  </div>
                  
                  <div class="bg-[#ffffff] rounded-lg p-3 border border-blue-100">
                    <div class="text-sm text-gray-600">Amount</div>
                    <div class="text-lg font-bold text-green-600">₹{{ form.amount || '0.00' }}</div>
                  </div>
                  
                  <div class="bg-[#ffffff] rounded-lg p-3 border border-blue-100">
                    <div class="text-sm text-gray-600">Status</div>
                    <div class="text-lg font-bold" :class="getStatusTextColor(form.status)">
                      {{ getStatusLabel(form.status) || '-' }}
                    </div>
                  </div>
                </div>
              </div>
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

defineOptions({
    layout: AppLayout
});

interface Props {
  contribution: {
    id: number;
    family_no: string;
    year: number;
    amount: string;
    payment_method_id: number | null;
    fund_category_id: number | null;
    payment_date: string;
    status: string;
    member_id: number | null;
    paid_by_name: string | null;
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
  year: props.contribution.year,
  amount: props.contribution.amount,
  payment_method_id: props.contribution.payment_method_id || '',
  fund_category_id: props.contribution.fund_category_id || '',
  payment_date: props.contribution.payment_date,
  status: props.contribution.status,
  member_id: props.contribution.member_id || '',
  paid_by_name: props.contribution.paid_by_name || '',
  notes: props.contribution.notes || ''
});

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

// Format date to YYYY-MM-DD for date input
onMounted(() => {
  if (form.payment_date) {
    // Ensure date is in correct format for date input
    const date = new Date(form.payment_date);
    form.payment_date = date.toISOString().split('T')[0];
  }
});
</script>
