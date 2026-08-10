<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DateInput } from '@/components/ui/date-input';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDateForDisplay } from '@/lib/utils';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Calculator, CreditCard, IndianRupee } from 'lucide-vue-next';
import { computed } from 'vue';

interface PaymentMethod {
  id: number;
  name: string;
  is_active: boolean;
}

interface OriginalPayment {
  id: number;
  payment_reference: string;
  payment_status: string;
  total_amount: number;
  paid_amount: number;
  balance_amount: number;
  concession_amount: number;
  payable: {
    id: number;
    booking_reference: string;
    // For permanent graves
    permanent_grave?: {
      grave_no: string;
      owner_name: string;
      section: string;
      row_no: string;
    };
    valid_member?: {
      first_name: string;
      last_name: string;
      relationship: string;
    };
    // For temporary graves
    temporary_grave?: {
      grave_no: string;
      section: string;
      row_no: string;
    };
    dead_first_name?: string;
    dead_last_name?: string;
    // Common fields
    applicant_name: string;
    died_on: string;
    buried_on: string;
  };
}

interface Props {
  originalPayment: OriginalPayment;
  paymentMethods: PaymentMethod[];
}

const props = defineProps<Props>();

// Form setup
const form = useForm({
  payment_method_id: '',
  paid_amount: props.originalPayment.balance_amount, // Default to full balance
  payment_date: new Date().toISOString().split('T')[0],
  transaction_reference: '',
  payment_notes: '',
});

// Computed properties
const statusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-800',
  partial: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
};

const balanceAfterPayment = computed(() => {
  const paidAmount = Number(form.paid_amount) || 0;
  return Math.max(0, props.originalPayment.balance_amount - paidAmount);
});

const paymentStatus = computed(() => {
  if (balanceAfterPayment.value <= 0) return 'completed';
  return 'partial';
});

// Methods
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
  })
    .format(amount)
    .replace('₹', '₹ ');
};

const getDeceasedName = () => {
  const payable = props.originalPayment.payable;

  // For permanent graves - use valid_member info
  if (payable.valid_member) {
    return `${payable.valid_member.first_name} ${payable.valid_member.last_name}`;
  }

  // For temporary graves - use dead_first_name and dead_last_name
  if (payable.dead_first_name && payable.dead_last_name) {
    return `${payable.dead_first_name} ${payable.dead_last_name}`;
  }

  return 'N/A';
};

const getGraveDetails = () => {
  const payable = props.originalPayment.payable;

  // For permanent graves
  if (payable.permanent_grave) {
    const grave = payable.permanent_grave;
    return `${grave.grave_no} - Section ${grave.section}, Row ${grave.row_no}`;
  }

  // For temporary graves
  if (payable.temporary_grave) {
    const grave = payable.temporary_grave;
    return `${grave.grave_no} - Section ${grave.section}, Row ${grave.row_no}`;
  }

  return 'N/A';
};

const setPaymentToBalance = () => {
  form.paid_amount = props.originalPayment.balance_amount;
};

const submit = () => {
  const formData = {
    ...form.data(),
    payment_method_id: parseInt(form.payment_method_id) || null,
  };

  form.transform(() => formData).post(route('graveyard.payments.balance.store', props.originalPayment.id));
};
</script>

<template>
  <Head title="Make Balance Payment" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Button variant="outline" size="sm" as-child>
                  <Link :href="route('graveyard.payments.show', originalPayment.id)">
                    <ArrowLeft class="h-4 w-4" />
                  </Link>
                </Button>
                <div>
                  <h3 class="text-base leading-6 font-semibold text-gray-900">Balance Payment for #{{ originalPayment.payment_reference }}</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Pay remaining balance for booking #{{ originalPayment.payable.booking_reference }}
                  </p>
                </div>
              </div>
              <Badge :class="statusColors[originalPayment.payment_status]">
                {{ originalPayment.payment_status }}
              </Badge>
            </div>
          </div>

          <form @submit.prevent="submit" class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <!-- Left Column - Original Payment Details -->
              <div class="space-y-6">
                <!-- Original Payment Summary -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <CreditCard class="h-5 w-5" />
                      <span>Original Payment Details</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                      <div class="flex justify-between">
                        <span class="text-gray-500">Total Amount:</span>
                        <span class="font-medium">{{ formatCurrency(originalPayment.total_amount) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Amount Paid:</span>
                        <span class="font-medium text-green-600">{{ formatCurrency(originalPayment.paid_amount) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Balance Due:</span>
                        <span class="font-medium text-red-600">{{ formatCurrency(originalPayment.balance_amount) }}</span>
                      </div>
                    </div>

                    <div v-if="originalPayment.concession_amount > 0" class="mt-4 rounded-md bg-orange-50 p-3">
                      <div class="flex items-center justify-between">
                        <div>
                          <p class="text-sm font-medium text-orange-800">Concession Applied</p>
                          <p class="text-xs text-orange-600">Discount already deducted</p>
                        </div>
                        <span class="text-base font-bold text-orange-700">{{ formatCurrency(originalPayment.concession_amount) }}</span>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Booking Details -->
                <Card>
                  <CardHeader>
                    <CardTitle>Booking Summary</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label class="text-sm font-medium text-gray-500">Deceased Person</Label>
                      <p class="text-base font-medium">{{ getDeceasedName() }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Date of Death</Label>
                        <p class="text-base">{{ formatDateForDisplay(originalPayment.payable.died_on) }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Date of Burial</Label>
                        <p class="text-base">{{ formatDateForDisplay(originalPayment.payable.buried_on) }}</p>
                      </div>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Grave Details</Label>
                      <p class="text-base">{{ getGraveDetails() }}</p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Applicant</Label>
                      <p class="text-base">{{ originalPayment.payable.applicant_name }}</p>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Right Column - Balance Payment Form -->
              <div class="space-y-6">
                <!-- Payment Form -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <IndianRupee class="h-5 w-5" />
                      <span>Balance Payment Details</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label for="payment_method_id">Payment Method *</Label>
                      <select
                        v-model="form.payment_method_id"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        required
                      >
                        <option value="">Select payment method</option>
                        <option v-for="method in paymentMethods" :key="method.id" :value="method.id.toString()">
                          {{ method.name }}
                        </option>
                      </select>
                      <div v-if="form.errors.payment_method_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.payment_method_id }}
                      </div>
                    </div>

                    <div>
                      <Label for="paid_amount">Amount to Pay *</Label>
                      <div class="flex space-x-2">
                        <Input
                          id="paid_amount"
                          v-model.number="form.paid_amount"
                          type="number"
                          min="0.01"
                          :max="originalPayment.balance_amount"
                          step="0.01"
                          placeholder="0.00"
                          class="flex-1"
                        />
                        <Button @click="setPaymentToBalance" type="button" variant="outline" size="sm" class="whitespace-nowrap">
                          <Calculator class="mr-1 h-4 w-4" />
                          Full
                        </Button>
                      </div>
                      <div class="mt-1 text-xs text-gray-500">Maximum: {{ formatCurrency(originalPayment.balance_amount) }}</div>
                      <div v-if="form.errors.paid_amount" class="mt-1 text-sm text-red-600">
                        {{ form.errors.paid_amount }}
                      </div>
                    </div>

                    <div>
                      <Label for="payment_date">Payment Date *</Label>
                      <DateInput id="payment_date" v-model="form.payment_date" class="w-full" />
                      <div v-if="form.errors.payment_date" class="mt-1 text-sm text-red-600">
                        {{ form.errors.payment_date }}
                      </div>
                    </div>

                    <div>
                      <Label for="transaction_reference">Transaction Reference</Label>
                      <Input id="transaction_reference" v-model="form.transaction_reference" placeholder="Cheque number, transaction ID, etc." />
                      <div v-if="form.errors.transaction_reference" class="mt-1 text-sm text-red-600">
                        {{ form.errors.transaction_reference }}
                      </div>
                    </div>

                    <div>
                      <Label for="payment_notes">Notes</Label>
                      <Textarea id="payment_notes" v-model="form.payment_notes" placeholder="Additional payment notes..." :rows="3" />
                    </div>
                  </CardContent>
                </Card>

                <!-- Balance Payment Summary -->
                <Card>
                  <CardHeader>
                    <CardTitle>Balance Payment Summary</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                      <div class="flex justify-between">
                        <span class="text-gray-500">Balance Due:</span>
                        <span class="font-medium">{{ formatCurrency(originalPayment.balance_amount) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Amount Paying:</span>
                        <span class="font-medium">{{ formatCurrency(Number(form.paid_amount) || 0) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Remaining Balance:</span>
                        <span class="font-medium" :class="balanceAfterPayment > 0 ? 'text-red-600' : 'text-green-600'">
                          {{ formatCurrency(balanceAfterPayment) }}
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Status After Payment:</span>
                        <Badge
                          :class="{
                            'bg-green-100 text-green-800': paymentStatus === 'completed',
                            'bg-blue-100 text-blue-800': paymentStatus === 'partial',
                          }"
                        >
                          {{ paymentStatus }}
                        </Badge>
                      </div>
                    </div>

                    <Separator />

                    <div class="flex space-x-3">
                      <Button type="submit" :disabled="form.processing" class="flex-1">
                        {{ form.processing ? 'Processing...' : 'Record Balance Payment' }}
                      </Button>
                      <Button type="button" variant="outline" as-child>
                        <Link :href="route('graveyard.payments.show', originalPayment.id)"> Cancel </Link>
                      </Button>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
