<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-[#ffffff] overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="mb-6">
            <h1 class="text-2xl font-semibold">Book New Mass Intention</h1>
            <p class="text-gray-600 mt-2">Schedule a new mass intention for a member or non-member</p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
              <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Member Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Member Information</h3>
                  
                  <!-- Member Type Selection -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Member Type</label>
                    <div class="flex space-x-4">
                      <label class="flex items-center">
                        <input 
                          v-model="form.member_type" 
                          type="radio" 
                          value="member"
                          class="mr-2 text-blue-600 focus:ring-[#3b82f6]"
                        />
                        <span class="text-sm text-gray-700">Parish Member</span>
                      </label>
                      <label class="flex items-center">
                        <input 
                          v-model="form.member_type" 
                          type="radio" 
                          value="non_member"
                          class="mr-2 text-blue-600 focus:ring-[#3b82f6]"
                        />
                        <span class="text-sm text-gray-700">Non-Member</span>
                      </label>
                    </div>
                  </div>

                  <!-- Member Search (for parish members) -->
                  <div v-if="form.member_type === 'member'" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Member</label>
                    <div class="relative">
                      <input 
                        v-model="memberSearchQuery"
                        type="text" 
                        placeholder="Search by name, family number, or phone..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                        @input="searchMembers"
                      />
                      
                      <!-- Search Results Dropdown -->
                      <div v-if="memberSearchResults.length > 0 && memberSearchQuery" class="absolute z-10 w-full mt-1 bg-[#ffffff] border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                        <div 
                          v-for="member in memberSearchResults" 
                          :key="member.id"
                          @click="selectMember(member)"
                          class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
                        >
                          <div class="font-medium text-gray-900">{{ member.name }}</div>
                          <div class="text-sm text-gray-600">
                            Family: {{ member.family_no }} | Phone: {{ member.phone }}
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Selected Member Display -->
                    <div v-if="selectedMember" class="mt-3 p-3 bg-blue-50 rounded-md">
                      <div class="flex justify-between items-start">
                        <div>
                          <div class="font-medium text-blue-900">{{ selectedMember.name }}</div>
                          <div class="text-sm text-blue-700">
                            Family: {{ selectedMember.family_no }} | Phone: {{ selectedMember.phone }}
                          </div>
                        </div>
                        <button 
                          @click="clearSelectedMember"
                          type="button"
                          class="text-blue-600 hover:text-blue-800"
                        >
                          <X class="w-[1rem] h-[1rem]" />
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Non-Member Input -->
                  <div v-if="form.member_type === 'non_member'" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input 
                      v-model="form.non_member_name"
                      type="text" 
                      placeholder="Enter full name"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                    />
                  </div>

                  <!-- Phone Number -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input 
                      v-model="form.phone"
                      type="tel" 
                      placeholder="Enter phone number"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                    />
                  </div>
                </div>

                <!-- Scheduling Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Scheduling</h3>
                  
                  <!-- Date Selection -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mass Date</label>
                    <input 
                      v-model="form.mass_date"
                      type="date" 
                      :min="minDate"
                      :max="maxDate"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                      @change="onDateChange"
                    />
                    <p class="text-xs text-gray-500 mt-1">
                      Available dates: {{ formatDate(minDate) }} to {{ formatDate(maxDate) }}
                    </p>
                  </div>

                  <!-- Mass Type Selection -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mass Type</label>
                    <select 
                      v-model="form.mass_type_id"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                      @change="onMassTypeChange"
                    >
                      <option value="">Select Mass Type</option>
                      <option 
                        v-for="massType in massTypes" 
                        :key="massType.id" 
                        :value="massType.id"
                      >
                        {{ massType.name }} - {{ formatTime(massType.default_time) }}
                      </option>
                    </select>
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
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                    >
                      <option value="">Select Intention Type</option>
                      <option 
                        v-for="intentionType in intentionTypes" 
                        :key="intentionType.id" 
                        :value="intentionType.id"
                      >
                        {{ intentionType.name }} - ₹{{ intentionType.default_amount }}
                      </option>
                    </select>
                  </div>

                  <!-- Special Instructions -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                    <textarea 
                      v-model="form.special_instructions"
                      rows="3"
                      placeholder="Any special instructions or notes..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                    ></textarea>
                  </div>

                  <!-- Payment Method -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select 
                      v-model="form.payment_method_id"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]"
                    >
                      <option value="">Select Payment Method</option>
                      <option 
                        v-for="paymentMethod in paymentMethods" 
                        :key="paymentMethod.id" 
                        :value="paymentMethod.id"
                      >
                        {{ paymentMethod.name }}
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                  <button 
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span v-if="isSubmitting">Booking...</span>
                    <span v-else>Book Mass Intention</span>
                  </button>
                </div>
              </form>
            </div>

            <!-- Right Sidebar - Already Booked Masses -->
            <div class="lg:col-span-1">
              <div class="bg-gray-50 p-6 rounded-lg sticky top-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Already Booked Masses</h3>
                
                <div v-if="!form.mass_date || !form.mass_type_id" class="text-center py-8">
                  <Calendar class="mx-auto h-12 w-12 text-gray-400 mb-3" />
                  <p class="text-sm text-gray-500">
                    Select a date and mass type to see booked masses
                  </p>
                </div>

                <div v-else>
                  <div class="mb-4">
                    <div class="text-sm text-gray-600">
                      <strong>{{ formatDate(form.mass_date) }}</strong><br>
                      <span v-if="selectedMassType">{{ selectedMassType.name }} - {{ formatTime(selectedMassType.default_time) }}</span>
                    </div>
                  </div>

                  <div v-if="bookedMasses.length === 0" class="text-center py-8">
                    <CheckCircle class="mx-auto h-12 w-12 text-green-400 mb-3" />
                    <p class="text-sm text-green-600 font-medium">No masses booked yet</p>
                    <p class="text-xs text-gray-500 mt-1">This time slot is available</p>
                  </div>

                  <div v-else class="space-y-3">
                    <div class="text-sm text-gray-600 mb-3">
                      <strong>{{ bookedMasses.length }} mass(es) already booked</strong>
                    </div>
                    
                    <div 
                      v-for="mass in bookedMasses" 
                      :key="mass.id"
                      class="bg-[#ffffff] p-3 rounded-md border border-gray-200"
                    >
                                             <div class="flex justify-between items-start">
                         <div class="flex-1">
                           <div class="font-medium text-gray-900 text-sm">
                             {{ mass.display_name }}
                           </div>
                           <div class="text-xs text-gray-600 mt-1">
                             {{ mass.intention_type_name }}
                           </div>
                         </div>
                         <!-- Status badge commented out - status workflow not implemented yet
                         <span 
                           :class="getStatusBadgeClass(mass.status)"
                           class="px-2 py-1 text-xs font-medium rounded-full"
                         >
                           {{ mass.status }}
                         </span>
                         -->
                       </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { Calendar, CheckCircle, X } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({
  layout: AppLayout
});

interface Props {
  massTypes: any[];
  intentionTypes: any[];
  paymentMethods: any[];
}

const props = defineProps<Props>();

// Form state
const form = useForm({
  member_type: 'member',
  member_id: null as number | null,
  non_member_name: '',
  phone: '',
  mass_date: '',
  mass_type_id: '',
  mass_intention_type_id: '',
  special_instructions: '',
  payment_method_id: '',
  amount: 0,
});

// Local state
const memberSearchQuery = ref('');
const memberSearchResults = ref<any[]>([]);
const selectedMember = ref<any>(null);
const bookedMasses = ref<any[]>([]);
const isSubmitting = ref(false);

// Computed properties
const minDate = computed(() => {
  const today = new Date();
  return today.toISOString().split('T')[0];
});

const maxDate = computed(() => {
  const today = new Date();
  const threeMonthsLater = new Date(today.getFullYear(), today.getMonth() + 3, today.getDate());
  return threeMonthsLater.toISOString().split('T')[0];
});

const selectedMassType = computed(() => {
  return props.massTypes.find(type => type.id == form.mass_type_id);
});

// Methods
async function searchMembers() {
  if (memberSearchQuery.value.length < 2) {
    memberSearchResults.value = [];
    return;
  }

  try {
    const response = await fetch(route('fund.mass-intentions.search-members') + '?query=' + encodeURIComponent(memberSearchQuery.value));
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
  form.phone = member.phone || '';
  memberSearchQuery.value = '';
  memberSearchResults.value = [];
}

function clearSelectedMember() {
  selectedMember.value = null;
  form.member_id = null;
  form.phone = '';
}

function onDateChange() {
  if (form.mass_date && form.mass_type_id) {
    fetchBookedMasses();
  }
}

function onMassTypeChange() {
  if (form.mass_date && form.mass_type_id) {
    fetchBookedMasses();
  }
}

async function fetchBookedMasses() {
  if (!form.mass_date || !form.mass_type_id) return;

  try {
    const response = await fetch(route('fund.mass-intentions.index') + '?mass_date=' + encodeURIComponent(form.mass_date) + '&mass_type_id=' + form.mass_type_id + '&status=all');
    const data = await response.json();
    bookedMasses.value = data.booked_masses || [];
  } catch (error) {
    console.error('Error fetching booked masses:', error);
    bookedMasses.value = [];
  }
}

function submitForm() {
  // Validate form
  if (!form.mass_date || !form.mass_type_id || !form.mass_intention_type_id || !form.payment_method_id) {
    alert('Please fill in all required fields');
    return;
  }

  if (form.member_type === 'member' && !form.member_id) {
    alert('Please select a member');
    return;
  }

  if (form.member_type === 'non_member' && !form.non_member_name) {
    alert('Please enter the name for non-member');
    return;
  }

  if (!form.phone) {
    alert('Please enter phone number');
    return;
  }

  // Set amount based on selected intention type
  const selectedIntentionType = props.intentionTypes.find(type => type.id == form.mass_intention_type_id);
  if (selectedIntentionType) {
    form.amount = selectedIntentionType.default_amount;
  }

  isSubmitting.value = true;

  // Submit the form
  form.post(route('fund.mass-intentions.store'), {
    onSuccess: () => {
      // Reset form and show success message
      form.reset();
      selectedMember.value = null;
      bookedMasses.value = [];
      isSubmitting.value = false;
      alert('Mass intention booked successfully!');
    },
    onError: () => {
      isSubmitting.value = false;
    }
  });
}

function formatDate(dateString: string) {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-IN', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function formatTime(timeString: string) {
  if (!timeString) return '';
  const [hours, minutes] = timeString.split(':');
  const hour = parseInt(hours);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  const displayHour = hour % 12 || 12;
  return `${displayHour}:${minutes} ${ampm}`;
}

// Status badge function commented out - status workflow not implemented yet
/*
function getStatusBadgeClass(status: string) {
  switch (status?.toLowerCase()) {
    case 'confirmed':
      return 'bg-green-100 text-green-800';
    case 'pending':
      return 'bg-yellow-100 text-yellow-800';
    case 'cancelled':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
}
*/

// Initialize form with today's date
onMounted(() => {
  form.mass_date = minDate.value;
});
</script>
