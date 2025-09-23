<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Maintenance Fee Payment" />

    <div class="mx-auto max-w-4xl">
      <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
        <h2 class="mb-4 text-2xl font-bold text-blue-700">Annual Maintenance Fee Payment</h2>

        <!-- Grave Information -->
        <div class="mb-6 rounded-lg bg-gray-50 p-4">
          <h3 class="mb-3 text-lg font-semibold text-gray-800">Grave Information</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Grave Position</label>
              <p class="text-lg font-semibold">{{ grave.section }}-{{ grave.row_no }}-{{ grave.grave_no }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Owner</label>
              <p class="text-lg">
                {{ grave.owner_name || (grave.member ? `${grave.member.first_name} ${grave.member.last_name}` : 'N/A') }}
              </p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Pending Amount</label>
              <p class="text-xl font-bold text-red-600">₹{{ Number(pendingAmount).toLocaleString('en-IN') }}</p>
            </div>
          </div>
        </div>

        <!-- Months Selection for Partial Payment -->
        <div v-if="availableMonths.length > 0" class="mb-6 rounded-lg bg-blue-50 p-4">
          <div class="mb-3 flex items-center justify-between">
            <h4 class="text-lg font-semibold text-blue-800">Select Months for Payment ({{ currentYear }})</h4>
            <label class="flex items-center space-x-2 cursor-pointer">
              <input
                type="checkbox"
                v-model="selectAllMonths"
                @change="toggleAllMonths"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              <span class="text-sm font-medium text-blue-800">Select All</span>
            </label>
          </div>
          <div class="grid grid-cols-2 gap-2 md:grid-cols-4">
            <label v-for="month in availableMonths" :key="month.value"
                   class="flex items-center space-x-2 cursor-pointer">
              <input
                type="checkbox"
                :value="month.value"
                v-model="form.months_paying_for"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              <span class="text-sm font-medium">{{ month.label }}</span>
            </label>
          </div>
          <p class="mt-2 text-sm text-blue-600">
            Selected: {{ form.months_paying_for.length }} months =
            ₹{{ Number(calculateRoundedAmount(form.months_paying_for.length * monthlyFee)).toLocaleString('en-IN') }}
          </p>
        </div>

        <!-- Payment Form -->
        <form @submit.prevent="submitPayment" class="space-y-6">
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Payer Name -->
            <div>
              <label for="payer_name" class="block text-sm font-medium text-gray-700 mb-2">
                Name of Person Making Payment <span class="text-red-500">*</span>
              </label>
              <input
                id="payer_name"
                v-model="form.payer_name"
                type="text"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                placeholder="Full name of the person making payment"
                required
              />
            </div>

            <!-- Payer Phone -->
            <div>
              <label for="payer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                Phone Number <span class="text-red-500">*</span>
              </label>
              <input
                id="payer_phone"
                v-model="form.payer_phone"
                type="tel"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                placeholder="Contact number"
                required
              />
            </div>

            <!-- Payer Email -->
            <div>
              <label for="payer_email" class="block text-sm font-medium text-gray-700 mb-2">
                Email Address
              </label>
              <input
                id="payer_email"
                v-model="form.payer_email"
                type="email"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                placeholder="Email address (optional)"
              />
            </div>

            <!-- Payment Amount -->
            <div>
              <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-2">
                Payment Amount <span class="text-red-500">*</span>
              </label>
              <input
                id="payment_amount"
                v-model="form.payment_amount"
                type="number"
                step="0.01"
                min="0.01"
                :max="pendingAmount"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                required
              />
              <p class="mt-1 text-sm text-gray-500">
                Annual Fee: ₹{{ Number(annualFee).toLocaleString('en-IN') }} |
                Monthly Rate: ₹{{ Number(monthlyFee).toLocaleString('en-IN') }}
              </p>
            </div>

            <!-- Payment Method -->
            <div>
              <label for="payment_method_id" class="block text-sm font-medium text-gray-700 mb-2">
                Payment Method <span class="text-red-500">*</span>
              </label>
              <select
                id="payment_method_id"
                v-model="form.payment_method_id"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                required
              >
                <option value="">Select Payment Method</option>
                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                  {{ method.name }}
                </option>
              </select>
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
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                required
              />
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
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                placeholder="Reference number, cheque number, etc."
              />
            </div>
          </div>

          <!-- Payment Notes -->
          <div>
            <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-2">
              Payment Notes
            </label>
            <textarea
              id="payment_notes"
              v-model="form.payment_notes"
              rows="3"
              class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
              placeholder="Additional notes about this payment..."
            ></textarea>
          </div>

          <!-- Submit Buttons -->
          <div class="flex items-center justify-between border-t pt-6">
            <Button
              type="button"
              variant="secondary"
              @click="router.visit('/graveyard/permanent-graves')"
              class="rounded-lg bg-gray-100 px-6 py-2 text-gray-700 hover:bg-gray-200"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="form.processing"
              class="rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
            >
              {{ form.processing ? 'Processing...' : 'Record Payment' }}
            </Button>
          </div>
        </form>
      </div>

      <!-- Payment History -->
      <div v-if="paymentHistory.length > 0" class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
        <h3 class="mb-4 text-xl font-bold text-blue-700">Payment History</h3>
        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-blue-50">
                <th class="border-b p-3 text-left font-semibold text-gray-700">Date</th>
                <th class="border-b p-3 text-left font-semibold text-gray-700">Amount</th>
                <th class="border-b p-3 text-left font-semibold text-gray-700">Method</th>
                <th class="border-b p-3 text-left font-semibold text-gray-700">Reference</th>
                <th class="border-b p-3 text-left font-semibold text-gray-700">Recorded By</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="payment in paymentHistory" :key="payment.id" class="hover:bg-gray-50">
                <td class="border-b p-3">{{ formatDate(payment.payment_date) }}</td>
                <td class="border-b p-3 font-semibold text-green-600">
                  ₹{{ Number(payment.paid_amount).toLocaleString('en-IN') }}
                </td>
                <td class="border-b p-3">{{ payment.payment_method?.name || 'N/A' }}</td>
                <td class="border-b p-3">{{ payment.transaction_reference || '-' }}</td>
                <td class="border-b p-3">{{ payment.creator?.name || 'N/A' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';

interface Props {
  grave: any;
  pendingAmount: number;
  paymentMethods: any[];
  paymentHistory: any[];
  annualFee: number;
  monthlyFee: number;
  availableMonths: any[];
  currentYear: number;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Permanent Graves', href: '/graveyard/permanent-graves' },
  { title: 'Maintenance Payment', href: '#' },
];

// Select all months functionality
const selectAllMonths = ref(false);

// Round up partial payment amounts (e.g., 488.33 -> 489)
const calculateRoundedAmount = (amount: number): number => {
  if (amount < props.pendingAmount) {
    // For partial payments, round up to next whole number
    return Math.ceil(amount);
  }
  return amount;
};

// Form setup
const form = useForm({
  grave_id: props.grave.id,
  payer_name: '',
  payer_phone: '',
  payer_email: '',
  payment_amount: calculateRoundedAmount(props.pendingAmount),
  payment_method_id: '',
  payment_date: new Date().toISOString().split('T')[0],
  months_paying_for: [] as number[],
  transaction_reference: '',
  payment_notes: '',
});

// Toggle all months selection
const toggleAllMonths = () => {
  if (selectAllMonths.value) {
    form.months_paying_for = props.availableMonths.map(m => m.value);
  } else {
    form.months_paying_for = [];
  }
};

// Watch months selection and update amount
watch(() => form.months_paying_for, (newMonths) => {
  if (newMonths.length > 0) {
    const rawAmount = newMonths.length * props.monthlyFee;
    form.payment_amount = calculateRoundedAmount(rawAmount);
  }

  // Update select all checkbox state
  selectAllMonths.value = newMonths.length === props.availableMonths.length;
}, { deep: true });

// Watch payment amount and suggest months
watch(() => form.payment_amount, (newAmount) => {
  if (newAmount && newAmount > 0) {
    const suggestedMonths = Math.min(Math.floor(newAmount / props.monthlyFee), props.availableMonths.length);
    if (suggestedMonths !== form.months_paying_for.length) {
      form.months_paying_for = props.availableMonths.slice(0, suggestedMonths).map(m => m.value);
    }
  }
});

// Submit payment
const submitPayment = () => {
  form.post('/graveyard/payments/maintenance', {
    onSuccess: () => {
      // Success handled by redirect in controller
    },
    onError: (errors) => {
      console.error('Payment submission failed:', errors);
    }
  });
};

// Format date helper
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-IN');
};
</script>