<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

interface Payment {
  id: number;
  payment_reference: string;
  receipt_number: string;
  payment_status: string;
  total_amount: number;
  paid_amount: number;
  payment_date: string;
  payment_mode?: string;
  transaction_reference?: string;
  service_charges?: Array<{
    service_id: number;
    service_name: string;
    quantity: number;
    unit_cost: number;
    total_cost: number;
  }>;
  paymentMethod?: {
    name: string;
  };
  payment_method?: {
    name: string;
  };
  payable?: {
    booking_reference?: string;
    permanent_grave?: {
      grave_no: string;
      section: string;
      row_no: string;
    };
    valid_member?: {
      first_name: string;
      last_name: string;
      relationship: string;
    };
    applicant_name?: string;
    died_on?: string;
    buried_on?: string;
  };
  creator?: {
    name: string;
  };
  created_at: string;
}

interface Props {
  payment: Payment;
}

const props = defineProps<Props>();

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
  })
    .format(amount)
    .replace('₹', '₹ ');
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-IN');
};

const formatDateTime = (date: string) => {
  return new Date(date).toLocaleString('en-IN');
};

const getDeceasedName = () => {
  const validMember = props.payment.payable?.valid_member;
  if (validMember) {
    return `${validMember.first_name} ${validMember.last_name}`;
  }
  return props.payment.payable?.applicant_name || 'N/A';
};

const printReceipt = () => {
  window.print();
};

onMounted(() => {
  // Auto-focus for printing
  window.focus();
});
</script>

<template>
  <Head :title="`Receipt #${payment.receipt_number}`" />

  <div class="min-h-screen bg-white">
    <!-- Print Button (hidden when printing) -->
    <div class="no-print fixed top-4 right-4 z-10">
      <button @click="printReceipt" class="rounded-lg bg-blue-600 px-4 py-2 text-white shadow-lg transition-colors hover:bg-blue-700">
        Print Receipt
      </button>
    </div>

    <!-- First Copy - Office Copy -->
    <div class="receipt-copy">
      <div class="copy-label">OFFICE COPY</div>
      <div class="mx-auto max-w-4xl p-8">
      <!-- Header -->
      <div class="mb-8 text-center">
        <h1 class="mb-2 text-3xl font-bold text-gray-800">OUR LADY OF SALVATION CHURCH</h1>
        <p class="mb-1 text-lg text-gray-600">Graveyard Services</p>
        <p class="text-sm text-gray-500">Payment Receipt</p>
      </div>

      <!-- Receipt Details -->
      <div class="mb-6 rounded-lg bg-gray-50 p-6">
        <div class="grid grid-cols-2 gap-6">
          <div>
            <h3 class="mb-3 text-lg font-semibold">Receipt Information</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="font-medium">Receipt Number:</span>
                <span>{{ payment.receipt_number }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Payment Reference:</span>
                <span>{{ payment.payment_reference }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Payment Date:</span>
                <span>{{ formatDate(payment.payment_date) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Receipt Generated:</span>
                <span>{{ formatDateTime(payment.created_at) }}</span>
              </div>
            </div>
          </div>

          <div>
            <h3 class="mb-3 text-lg font-semibold">Booking Details</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="font-medium">Booking Reference:</span>
                <span>{{ payment.payable?.booking_reference || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Deceased Person:</span>
                <span>{{ getDeceasedName() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Grave Number:</span>
                <span>{{ payment.payable?.permanent_grave?.grave_no || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Applicant:</span>
                <span>{{ payment.payable?.applicant_name || 'N/A' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Service Details -->
      <div v-if="payment.service_charges && payment.service_charges.length > 0" class="mb-6">
        <h3 class="mb-4 text-lg font-semibold">Service Details</h3>
        <div class="overflow-hidden rounded-lg border border-gray-200">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-700">Service</th>
                <th class="px-4 py-3 text-center font-medium text-gray-700">Quantity</th>
                <th class="px-4 py-3 text-right font-medium text-gray-700">Unit Cost</th>
                <th class="px-4 py-3 text-right font-medium text-gray-700">Total Cost</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="service in payment.service_charges" :key="service.service_id">
                <td class="px-4 py-3">{{ service.service_name }}</td>
                <td class="px-4 py-3 text-center">{{ service.quantity }}</td>
                <td class="px-4 py-3 text-right">{{ formatCurrency(service.unit_cost) }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ formatCurrency(service.total_cost) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payment Summary -->
      <div class="mb-6 rounded-lg bg-gray-50 p-6">
        <h3 class="mb-4 text-lg font-semibold">Payment Summary</h3>
        <div class="space-y-3">
          <div class="flex justify-between text-lg">
            <span class="font-medium">Total Amount:</span>
            <span class="font-semibold">{{ formatCurrency(payment.total_amount) }}</span>
          </div>
          <div class="flex justify-between text-lg">
            <span class="font-medium">Amount Paid:</span>
            <span class="font-semibold text-green-600">{{ formatCurrency(payment.paid_amount) }}</span>
          </div>
          <div class="border-t border-gray-300 pt-3">
            <div class="flex justify-between text-xl font-bold">
              <span>Status:</span>
              <span :class="payment.payment_status === 'completed' ? 'text-green-600' : 'text-blue-600'">
                {{ payment.payment_status.toUpperCase() }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Method -->
      <div class="mb-8 grid grid-cols-2 gap-6">
        <div>
          <h3 class="mb-3 text-lg font-semibold">Payment Method</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">Method:</span>
              <span>{{ payment.paymentMethod?.name || payment.payment_method?.name || 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">Mode:</span>
              <span class="capitalize">{{ payment.payment_mode || 'N/A' }}</span>
            </div>
            <div v-if="payment.transaction_reference" class="flex justify-between">
              <span class="font-medium">Reference:</span>
              <span>{{ payment.transaction_reference }}</span>
            </div>
          </div>
        </div>

        <div>
          <h3 class="mb-3 text-lg font-semibold">Authorized By</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">Recorded By:</span>
              <span>{{ payment.creator?.name || 'N/A' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="border-t border-gray-200 pt-6 text-center text-sm text-gray-500">
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <p class="mt-2">For any queries, please contact the church office.</p>
        <p class="mt-4 font-medium">Thank you for your contribution to St. Lawrence Church</p>
      </div>
    </div>
    </div>

    <!-- Second Copy - Customer Copy -->
    <div class="receipt-copy">
      <div class="copy-label">CUSTOMER COPY</div>
      <div class="mx-auto max-w-4xl p-8">
      <!-- Header -->
      <div class="mb-8 text-center">
        <h1 class="mb-2 text-3xl font-bold text-gray-800">OUR LADY OF SALVATION CHURCH</h1>
        <p class="mb-1 text-lg text-gray-600">Graveyard Services</p>
        <p class="text-sm text-gray-500">Payment Receipt</p>
      </div>

      <!-- Receipt Details -->
      <div class="mb-6 rounded-lg bg-gray-50 p-6">
        <div class="grid grid-cols-2 gap-6">
          <div>
            <h3 class="mb-3 text-lg font-semibold">Receipt Information</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="font-medium">Receipt Number:</span>
                <span>{{ payment.receipt_number }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Payment Reference:</span>
                <span>{{ payment.payment_reference }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Payment Date:</span>
                <span>{{ formatDate(payment.payment_date) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Receipt Generated:</span>
                <span>{{ formatDateTime(payment.created_at) }}</span>
              </div>
            </div>
          </div>

          <div>
            <h3 class="mb-3 text-lg font-semibold">Booking Details</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="font-medium">Booking Reference:</span>
                <span>{{ payment.payable?.booking_reference || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Deceased Person:</span>
                <span>{{ getDeceasedName() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Grave Number:</span>
                <span>{{ payment.payable?.permanent_grave?.grave_no || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Applicant:</span>
                <span>{{ payment.payable?.applicant_name || 'N/A' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Service Details -->
      <div v-if="payment.service_charges && payment.service_charges.length > 0" class="mb-6">
        <h3 class="mb-4 text-lg font-semibold">Service Details</h3>
        <div class="overflow-hidden rounded-lg border border-gray-200">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-700">Service</th>
                <th class="px-4 py-3 text-center font-medium text-gray-700">Quantity</th>
                <th class="px-4 py-3 text-right font-medium text-gray-700">Unit Cost</th>
                <th class="px-4 py-3 text-right font-medium text-gray-700">Total Cost</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="service in payment.service_charges" :key="service.service_id">
                <td class="px-4 py-3">{{ service.service_name }}</td>
                <td class="px-4 py-3 text-center">{{ service.quantity }}</td>
                <td class="px-4 py-3 text-right">{{ formatCurrency(service.unit_cost) }}</td>
                <td class="px-4 py-3 text-right font-medium">{{ formatCurrency(service.total_cost) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payment Summary -->
      <div class="mb-6 rounded-lg bg-gray-50 p-6">
        <h3 class="mb-4 text-lg font-semibold">Payment Summary</h3>
        <div class="space-y-3">
          <div class="flex justify-between text-lg">
            <span class="font-medium">Total Amount:</span>
            <span class="font-semibold">{{ formatCurrency(payment.total_amount) }}</span>
          </div>
          <div class="flex justify-between text-lg">
            <span class="font-medium">Amount Paid:</span>
            <span class="font-semibold text-green-600">{{ formatCurrency(payment.paid_amount) }}</span>
          </div>
          <div class="border-t border-gray-300 pt-3">
            <div class="flex justify-between text-xl font-bold">
              <span>Status:</span>
              <span :class="payment.payment_status === 'completed' ? 'text-green-600' : 'text-blue-600'">
                {{ payment.payment_status.toUpperCase() }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Method -->
      <div class="mb-8 grid grid-cols-2 gap-6">
        <div>
          <h3 class="mb-3 text-lg font-semibold">Payment Method</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">Method:</span>
              <span>{{ payment.paymentMethod?.name || payment.payment_method?.name || 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">Mode:</span>
              <span class="capitalize">{{ payment.payment_mode || 'N/A' }}</span>
            </div>
            <div v-if="payment.transaction_reference" class="flex justify-between">
              <span class="font-medium">Reference:</span>
              <span>{{ payment.transaction_reference }}</span>
            </div>
          </div>
        </div>

        <div>
          <h3 class="mb-3 text-lg font-semibold">Authorized By</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">Recorded By:</span>
              <span>{{ payment.creator?.name || 'N/A' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="border-t border-gray-200 pt-6 text-center text-sm text-gray-500">
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <p class="mt-2">For any queries, please contact the church office.</p>
        <p class="mt-4 font-medium">Thank you for your contribution to St. Lawrence Church</p>
      </div>
    </div>
    </div>
  </div>
</template>

<style scoped>
.receipt-copy {
  page-break-after: always;
}

.copy-label {
  text-align: center;
  font-size: 12px;
  color: #999;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
}

@media print {
  .no-print {
    display: none !important;
  }

  body {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  @page {
    margin: 1in;
    size: A4;
  }

  .receipt-copy:last-child {
    page-break-after: avoid;
  }
}
</style>
