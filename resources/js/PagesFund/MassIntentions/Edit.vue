<template>
  <div class="py-6">
    <div class="mx-auto max-w-7xl">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Edit Mass Intention</h1>
            <Link
              :href="route('fund.mass-intentions.index')"
              class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-900"
            >
              Back to List
            </Link>
          </div>

          <div class="mx-auto max-w-4xl">
            <form @submit.prevent="updateIntention" class="space-y-6">
              <!-- Member Information Section -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Member Information</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Member</label>
                    <select
                      v-model="form.member_id"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                      <option value="">Select Member</option>
                      <option v-for="member in members" :key="member.id" :value="member.id">
                        {{ member.first_name }} {{ member.last_name }} - {{ member.family_no }}
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Non-Member Name</label>
                    <input
                      v-model="form.external_name"
                      type="text"
                      class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      placeholder="Enter name if not a member"
                    />
                  </div>
                </div>
              </div>

              <!-- Scheduling Section -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Scheduling</h3>

                <!-- Mass Date -->
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Mass Date</label>
                  <DateInput v-model="form.mass_date" class="w-full" />
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
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                  >
                    <option value="">Select Intention Type</option>
                    <option v-for="type in massIntentionTypes" :key="type.id" :value="type.id">{{ type.name }} - ₹{{ type.default_amount }}</option>
                  </select>
                  <p class="mt-1 text-xs text-gray-500">Current: {{ form.mass_intention_type_id }}</p>
                </div>

                <!-- Intention For -->
                <div class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Intention For</label>
                  <input
                    v-model="form.intention_for"
                    type="text"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Enter what the intention is for"
                    required
                  />
                </div>

                <!-- Amount -->
                <div class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Amount</label>
                  <input
                    v-model="form.amount"
                    type="number"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  />
                </div>

                <!-- Status -->
                <div class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                  <select
                    v-model="form.status"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
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
                  <label class="mb-2 block text-sm font-medium text-gray-700">Special Instructions</label>
                  <textarea
                    v-model="form.special_instructions"
                    rows="3"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Any special instructions or notes"
                  ></textarea>
                </div>

                <!-- Payment Method -->
                <div class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Payment Method</label>
                  <select
                    v-model="form.payment_method_id"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required
                  >
                    <option value="">Select Payment Method</option>
                    <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                      {{ method.name }}
                    </option>
                  </select>
                </div>

                <!-- Transaction Reference -->
                <div class="mb-4">
                  <label class="mb-2 block text-sm font-medium text-gray-700">Transaction Reference</label>
                  <input
                    v-model="form.transaction_reference"
                    type="text"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Check No, UPI ID, etc."
                  />
                  <p class="mt-1 text-sm text-gray-500">Enter check number, UPI transaction ID, or other payment reference</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('fund.mass-intentions.index')"
                  class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="updating"
                  class="rounded-md bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
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
import { DateInput } from '@/components/ui/date-input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

defineOptions({
  layout: AppLayout,
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
  external_name: '',
  mass_date: '',
  mass_intention_type_id: '',
  intention_for: '',
  amount: '',
  payment_method_id: '',
  transaction_reference: '',
  special_instructions: '',
  status: '',
});

// Initialize form data when props are available
onMounted(() => {
  initializeForm();
});

// Watch for changes in massIntention prop
watch(
  () => props.massIntention,
  (newValue) => {
    if (newValue) {
      initializeForm();
    }
  },
  { immediate: true },
);

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
      external_name: intention.external_name || '',
      mass_date: formattedDate,
      mass_intention_type_id: intention.mass_intention_type_id || intention.massIntentionType?.id || '',
      intention_for: intention.intention_for || '',
      amount: intention.amount || '',
      payment_method_id: intention.payment_method_id || '',
      transaction_reference: intention.transaction_reference || '',
      special_instructions: intention.special_instructions || '',
      status: intention.status || 'pending',
    };
  }
}

// Update intention
function updateIntention() {
  updating.value = true;

  router.put(route('fund.mass-intentions.update', props.massIntention.id), form.value, {
    onSuccess: () => {
      updating.value = false;
      // Redirect to index page after successful update
      router.visit(route('fund.mass-intentions.index'));

      // Fallback redirect in case router.visit doesn't work
      setTimeout(() => {
        window.location.href = route('fund.mass-intentions.index');
      }, 1000);
    },
    onError: () => {
      updating.value = false;
    },
  });
}
</script>
