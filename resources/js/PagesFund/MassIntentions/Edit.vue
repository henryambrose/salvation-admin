<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Edit Mass Intention</h1>
            <Link 
              :href="route('fund.mass-intentions.index')"
              class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Back to List
            </Link>
          </div>

          <div class="max-w-4xl mx-auto">
            <form @submit.prevent="updateIntention" class="space-y-6">
              <!-- Member Information Section -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Member Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Member</label>
                    <select 
                      v-model="form.member_id" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="">Select Member</option>
                      <option v-for="member in members" :key="member.id" :value="member.id">
                        {{ member.first_name }} {{ member.last_name }} - {{ member.family_no }}
                      </option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Non-Member Name</label>
                    <input 
                      v-model="form.non_member_name" 
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Enter name if not a member"
                    />
                  </div>
                </div>
              </div>

              <!-- Scheduling Section -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Scheduling</h3>
                
                <!-- Mass Date -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Mass Date</label>
                  <input 
                    v-model="form.mass_date" 
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
              </div>

              <!-- Intention Details Section -->
              <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Intention Details</h3>
                
                <!-- Intention Type -->
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Intention Type</label>
                  <select 
                    v-model="form.mass_intention_type_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                    <option value="">Select Intention Type</option>
                    <option v-for="type in massIntentionTypes" :key="type.id" :value="type.id">
                      {{ type.name }} - ₹{{ type.default_amount }}
                    </option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">Current: {{ form.mass_intention_type_id }}</p>
                </div>

                <!-- Intention For -->
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Intention For</label>
                  <input 
                    v-model="form.intention_for" 
                    type="text" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter what the intention is for"
                    required
                  />
                </div>

                <!-- Amount -->
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                  <input 
                    v-model="form.amount" 
                    type="number" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>

                <!-- Status -->
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                  <select 
                    v-model="form.status" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                    <option value="">Select Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>

                <!-- Special Instructions -->
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                  <textarea 
                    v-model="form.special_instructions" 
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Any special instructions or notes"
                  ></textarea>
                </div>

                <!-- Payment Method -->
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                  <select 
                    v-model="form.payment_method_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                    <option value="">Select Payment Method</option>
                    <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                      {{ method.name }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-3">
                <Link 
                  :href="route('fund.mass-intentions.index')"
                  class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  Cancel
                </Link>
                <button 
                  type="submit" 
                  :disabled="updating"
                  class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ updating ? 'Updating...' : 'Update Intention' }}
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
import { Link, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout
});

interface Props {
  massIntention: any;
  massIntentionTypes: any[];
  members: any[];
  paymentMethods: any[];
}

const props = defineProps<Props>();

const updating = ref(false);

// Form data - initialize with empty values first
const form = ref({
  member_id: '',
  non_member_name: '',
  mass_date: '',
  mass_intention_type_id: '',
  intention_for: '',
  amount: '',
  payment_method_id: '',
  special_instructions: '',
  status: '',
});

// Initialize form data when props are available
onMounted(() => {
  initializeForm();
});

// Watch for changes in massIntention prop
watch(() => props.massIntention, (newValue) => {
  if (newValue) {
    initializeForm();
  }
}, { immediate: true });

// Initialize form with existing data
function initializeForm() {
  if (props.massIntention) {
    const intention = props.massIntention;
    
    // Format the date for the HTML date input (YYYY-MM-DD format)
    let formattedDate = '';
    if (intention.mass_date) {
      try {
        const date = new Date(intention.mass_date);
        if (!isNaN(date.getTime())) {
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          formattedDate = `${year}-${month}-${day}`;
        }
      } catch (error) {
        console.error('Date parsing error:', error);
      }
    }
    
    form.value = {
      member_id: intention.member_id || '',
      non_member_name: intention.non_member_name || '',
      mass_date: formattedDate,
      mass_intention_type_id: intention.mass_intention_type_id || intention.massIntentionType?.id || '',
      intention_for: intention.intention_for || '',
      amount: intention.amount || '',
      payment_method_id: intention.payment_method_id || '',
      special_instructions: intention.special_instructions || '',
      status: intention.status || 'pending',
    };
  }
}

// Update intention
function updateIntention() {
  updating.value = true;
  
  console.log('Submitting form data:', form.value);
  
  router.put(route('fund.mass-intentions.update', props.massIntention.id), form.value, {
    onSuccess: (response) => {
      console.log('Update successful:', response);
      updating.value = false;
      // Redirect to index page after successful update
      router.visit(route('fund.mass-intentions.index'));
      
      // Fallback redirect in case router.visit doesn't work
      setTimeout(() => {
        window.location.href = route('fund.mass-intentions.index');
      }, 1000);
    },
    onError: (errors) => {
      console.log('Update failed with errors:', errors);
      updating.value = false;
    }
  });
}

// Utility function
function formatDate(dateString: string) {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-IN');
}
</script>
