<template>
  <div class="py-6">
    <div class="mx-auto max-w-7xl">
      <div class="overflow-hidden bg-[#ffffff] shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="mb-6">
            <h1 class="text-2xl font-semibold">Book New Mass Intention</h1>
            <p class="mt-2 text-gray-600">Schedule a new mass intention for a member or non-member</p>
          </div>

          <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Main Form -->
            <div class="lg:col-span-2">
              <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Member Information Section -->
                <div class="rounded-lg bg-gray-50 p-6">
                  <h3 class="mb-4 text-lg font-medium text-gray-900">Member Information</h3>

                  <!-- Member Type Selection -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Member Type</label>
                    <div class="flex space-x-4">
                      <label class="flex items-center">
                        <input v-model="form.member_type" type="radio" value="member" class="mr-2 text-blue-600 focus:ring-[#3b82f6]" />
                        <span class="text-sm text-gray-700">Parish Member</span>
                      </label>
                      <label class="flex items-center">
                        <input v-model="form.member_type" type="radio" value="external" class="mr-2 text-blue-600 focus:ring-[#3b82f6]" />
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
                        placeholder="Search by name or phone..."
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
                          <div class="font-medium">{{ member.name }}</div>
                          <div class="text-sm text-gray-500">
                            Community No: {{ member.community.name.split('-')[0].trim() }} | Family: {{ member.family_no || 'N/A' }}
                          </div>
                          <div class="text-sm text-gray-500">Address: {{ member.current_add1 }} | {{ member.contact_no_1 }}</div>
                        </div>
                      </div>
                    </div>

                    <!-- Selected Member Display -->
                    <div v-if="selectedMember" class="mt-3 rounded-md bg-blue-50 p-3">
                      <div class="flex items-start justify-between">
                        <div>
                          <div class="font-medium text-blue-900">{{ selectedMember.name }}</div>
                          <div class="text-sm text-blue-700">
                            <div class="font-medium">{{ selectedMember.full_name }}</div>
                            <div class="text-sm text-gray-500">
                              Community No: {{ selectedMember.community.name.split('-')[0].trim() }} | Family: {{ selectedMember.family_no || 'N/A' }}
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
                      v-model="form.external_name"
                      type="text"
                      placeholder="Enter full name"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                    />
                  </div>

                  <!-- Phone Number -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Phone Number</label>
                    <input
                      v-model="form.phone"
                      type="tel"
                      placeholder="Enter phone number"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                    />
                  </div>
                </div>

                <!-- Scheduling Section -->
                <div class="rounded-lg bg-gray-50 p-6">
                  <h3 class="mb-4 text-lg font-medium text-gray-900">Scheduling</h3>

                  <!-- Date Selection -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Mass Date</label>
                    <DateInput
                      v-model="form.mass_date"
                      class="w-full"
                      @change="onDateChange"
                    />
                    <p class="mt-1 text-xs text-gray-500">Available dates: {{ formatDateForDisplay(minDate) }} to {{ formatDateForDisplay(maxDate) }}</p>
                  </div>

                  <!-- Mass Type Selection -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Mass Type</label>
                    <select
                      v-model="form.mass_type_id"
                      required
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                      @change="onMassTypeChange"
                    >
                      <option value="">Select Mass Type</option>
                      <option v-for="massType in massTypes" :key="massType.id" :value="massType.id">
                        {{ massType.name }} - {{ formatTime(massType.default_time) }}
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Intention Details Section -->
                <div class="rounded-lg bg-gray-50 p-6">
                  <h3 class="mb-4 text-lg font-medium text-gray-900">Intention Details</h3>

                  <!-- Intention Type -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Intention Type</label>
                    <select
                      v-model="form.mass_intention_type_id"
                      required
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                      @change="onIntentionTypeChange"
                    >
                      <option value="">Select Intention Type</option>
                      <option v-for="intentionType in intentionTypes" :key="intentionType.id" :value="intentionType.id">
                        {{ intentionType.name }} - ₹{{ intentionType.default_amount }}
                      </option>
                    </select>
                  </div>

                  <!-- Intention For -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Intention For</label>
                    <input
                      v-model="form.intention_for"
                      type="text"
                      placeholder="Enter intention for (e.g., For the soul of John Doe)"
                      required
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                    />
                  </div>

                  <!-- Amount -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Amount</label>
                    <input
                      v-model="form.amount"
                      type="number"
                      placeholder="Enter amount"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                    />
                    <p class="mt-1 text-xs text-gray-500">Amount will be auto-filled based on intention type</p>
                  </div>

                  <!-- Status -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                    <select
                      v-model="form.status"
                      required
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
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
                    <label class="mb-2 block text-sm font-medium text-gray-700">Special Instructions</label>
                    <textarea
                      v-model="form.special_instructions"
                      rows="3"
                      placeholder="Any special instructions or notes..."
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                    ></textarea>
                  </div>

                  <!-- Payment Method -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Payment Method</label>
                    <select
                      v-model="form.payment_method_id"
                      required
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                    >
                      <option value="">Select Payment Method</option>
                      <option v-for="paymentMethod in paymentMethods" :key="paymentMethod.id" :value="paymentMethod.id">
                        {{ paymentMethod.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Transaction Reference -->
                  <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Transaction Reference</label>
                    <input
                      v-model="form.transaction_reference"
                      type="text"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#3b82f6] focus:outline-none"
                      placeholder="Check No, UPI ID, etc."
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      Enter check number, UPI transaction ID, or other payment reference
                    </p>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="rounded-md bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-[#3b82f6] focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    <span v-if="isSubmitting">Booking...</span>
                    <span v-else>Book Mass Intention</span>
                  </button>
                </div>
              </form>
            </div>

            <!-- Right Sidebar - Already Booked Masses -->
            <div class="lg:col-span-1">
              <div class="sticky top-6 rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Already Booked Masses</h3>

                <div v-if="!form.mass_date || !form.mass_type_id" class="py-8 text-center">
                  <Calendar class="mx-auto mb-3 h-12 w-12 text-gray-400" />
                  <p class="text-sm text-gray-500">Select a date and mass type to see booked masses</p>
                </div>

                <div v-else>
                  <div class="mb-4">
                    <div class="text-sm text-gray-600">
                      <strong>{{ formatDateForDisplay(form.mass_date) }}</strong
                      ><br />
                      <span v-if="selectedMassType">{{ selectedMassType.name }} - {{ formatTime(selectedMassType.default_time) }}</span>
                    </div>
                  </div>

                  <div v-if="bookedMasses.length === 0" class="py-8 text-center">
                    <CheckCircle class="mx-auto mb-3 h-12 w-12 text-green-400" />
                    <p class="text-sm font-medium text-green-600">No masses booked yet</p>
                    <p class="mt-1 text-xs text-gray-500">This time slot is available</p>
                  </div>

                  <div v-else class="space-y-3">
                    <div class="mb-3 text-sm text-gray-600">
                      <strong>{{ bookedMasses.length }} mass(es) already booked</strong>
                    </div>

                    <div v-for="mass in bookedMasses" :key="mass.id" class="rounded-md border border-gray-200 bg-[#ffffff] p-3">
                      <div class="flex items-start justify-between">
                        <div class="flex-1">
                          <div class="text-sm font-medium text-gray-900">
                            {{ mass.display_name }}
                          </div>
                          <div class="mt-1 text-xs text-gray-600">
                            {{ mass.intention_type_name }}
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
    </div>
  </div>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { Calendar, CheckCircle, X } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { useToast } from '@/composables/useToast';
import { DateInput } from '@/components/ui/date-input';
import { formatDateForDisplay } from '@/lib/utils';

defineOptions({
  layout: AppLayout,
});

interface Props {
  massTypes: any[];
  intentionTypes: any[];
  paymentMethods: any[];
}

const props = defineProps<Props>();

const { success, error } = useToast();

// Form state
const form = useForm({
  member_type: 'member',
  member_id: null as number | null,
  external_name: '',
  phone: '',
  mass_date: '',
  mass_type_id: '',
  mass_intention_type_id: '',
  intention_for: '',
  amount: 0,
  status: 'pending',
  special_instructions: '',
  payment_method_id: '',
  transaction_reference: '',
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
  return props.massTypes.find((type) => type.id == form.mass_type_id);
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

function onIntentionTypeChange() {
  // Auto-fill amount based on selected intention type
  const selectedIntentionType = props.intentionTypes.find((type) => type.id == form.mass_intention_type_id);
  if (selectedIntentionType) {
    form.amount = selectedIntentionType.default_amount;
  }
}

async function fetchBookedMasses() {
  if (!form.mass_date || !form.mass_type_id) return;

  try {
    const response = await fetch(
      route('fund.mass-intentions.index') + '?mass_date=' + encodeURIComponent(form.mass_date) + '&mass_type_id=' + form.mass_type_id + '&status=all',
    );
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
    error('Please fill in all required fields');
    return;
  }

  if (form.member_type === 'member' && !form.member_id) {
    error('Please select a member');
    return;
  }

  if (form.member_type === 'external' && !form.external_name) {
    error('Please enter the name for non-member');
    return;
  }

  if (!form.intention_for) {
    error('Please enter what the intention is for');
    return;
  }

  if (!form.status) {
    error('Please select a status');
    return;
  }

  // Prevent duplicate submissions
  if (isSubmitting.value) return;
  isSubmitting.value = true;

  // Submit the form
  form.post(route('fund.mass-intentions.store'), {
    onSuccess: () => {
      success('Mass intention booked successfully!');
      // Reset form and show success message
      form.reset();
      selectedMember.value = null;
      bookedMasses.value = [];
      isSubmitting.value = false;
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
      error('Please check the form for errors and try again.');
      isSubmitting.value = false;
    },
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
