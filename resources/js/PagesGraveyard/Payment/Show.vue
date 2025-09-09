<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, CreditCard, Download, FileText, Receipt } from 'lucide-vue-next';

interface Payment {
  id: number;
  payment_reference: string;
  payment_status: 'pending' | 'partial' | 'completed' | 'refunded';
  total_amount: number;
  paid_amount: number;
  balance_amount: number;
  concession_amount: number;
  payment_date: string;
  payment_mode: string;
  transaction_reference?: string;
  payment_notes?: string;
  receipt_number?: string;
  receipt_generated_at?: string;
  service_charges: Array<{
    service_id: number;
    service_name: string;
    quantity: number;
    unit_cost: number;
    total_cost: number;
  }>;
  paymentMethod: {
    name: string;
  };
  payable: {
    id: number;
    booking_reference: string;
    // For permanent grave bookings
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
    // For temporary grave bookings
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
  creator: {
    name: string;
  };
  created_at: string;
  updated_at: string;
}

interface Props {
  payment: Payment;
}

const props = defineProps<Props>();
const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  partial: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  refunded: 'bg-red-100 text-red-800',
};

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
  const payable = props.payment.payable;
  
  // For permanent grave bookings
  if (payable.valid_member) {
    return `${payable.valid_member.first_name} ${payable.valid_member.last_name}`;
  }
  
  // For temporary grave bookings
  if (payable.dead_first_name && payable.dead_last_name) {
    return `${payable.dead_first_name} ${payable.dead_last_name}`;
  }
  
  // Fallback
  return 'Unknown';
};

const calculateServiceSubtotal = () => {
  if (!props.payment.service_charges) return 0;
  return props.payment.service_charges.reduce((sum, service) => sum + service.total_cost, 0);
};

const getBookingRoute = () => {
  const payable = props.payment.payable;
  
  // For permanent grave bookings
  if (payable.permanent_grave) {
    return route('graveyard.permanent-grave-bookings.show', payable.id);
  }
  
  // For temporary grave bookings
  if (payable.temporary_grave) {
    return route('graveyard.temporary-grave-bookings.show', payable.id);
  }
  
  // Fallback to graveyard dashboard
  return route('graveyard.dashboard');
};

const generateReceipt = () => {
  window.open(route('graveyard.payments.receipt', props.payment.id), '_blank');
};
</script>

<template>
  <Head :title="`Payment #${payment.payment_reference}`" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-4 lg:px-6">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Button variant="outline" size="sm" as-child>
                  <Link :href="getBookingRoute()">
                    <ArrowLeft class="h-4 w-4" />
                  </Link>
                </Button>
                <div>
                  <h3 class="text-base leading-6 font-semibold text-gray-900">Payment #{{ payment.payment_reference }}</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Payment details for booking #{{ payment.payable.booking_reference }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <Badge :class="statusColors[payment.payment_status]">
                  {{ payment.payment_status }}
                </Badge>
                <Button 
                  v-if="payment.payment_status === 'partial' && payment.balance_amount > 0" 
                  as-child 
                  class="bg-blue-600 hover:bg-blue-700"
                >
                  <Link :href="route('graveyard.payments.balance', payment.id)">
                    <CreditCard class="mr-2 h-4 w-4" />
                    Pay Balance
                  </Link>
                </Button>
                <Button v-if="payment.receipt_number" @click="generateReceipt" variant="outline">
                  <Download class="mr-2 h-4 w-4" />
                  Download Receipt
                </Button>
              </div>
            </div>
          </div>

          <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <!-- Left Column -->
              <div class="space-y-6">
                <!-- Payment Details -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <CreditCard class="h-5 w-5" />
                      <span>Payment Information</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Payment Reference</Label>
                        <p class="text-base font-medium">{{ payment.payment_reference }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Payment Date</Label>
                        <p class="text-base">{{ formatDate(payment.payment_date) }}</p>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Payment Method</Label>
                        <p class="text-base">{{ payment.paymentMethod }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Payment Mode</Label>
                        <p class="text-base capitalize">{{ payment.payment_mode }}</p>
                      </div>
                    </div>

                    <div v-if="payment.transaction_reference">
                      <Label class="text-sm font-medium text-gray-500">Transaction Reference</Label>
                      <p class="text-base">{{ payment.transaction_reference }}</p>
                    </div>

                    <div v-if="payment.payment_notes">
                      <Label class="text-sm font-medium text-gray-500">Notes</Label>
                      <p class="text-base">{{ payment.payment_notes }}</p>
                    </div>
                  </CardContent>
                </Card>

                <!-- Booking Details -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <FileText class="h-5 w-5" />
                      <span>Booking Details</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label class="text-sm font-medium text-gray-500">Booking Reference</Label>
                      <p class="text-base font-medium">{{ payment.payable.booking_reference }}</p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Deceased Person</Label>
                      <p class="text-base font-medium">{{ getDeceasedName() }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Date of Death</Label>
                        <p class="text-base">{{ formatDate(payment.payable.died_on) }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Date of Burial</Label>
                        <p class="text-base">{{ formatDate(payment.payable.buried_on) }}</p>
                      </div>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Grave Details</Label>
                      <p class="text-base">
                        <template v-if="payment.payable.permanent_grave">
                          {{ payment.payable.permanent_grave.grave_no }} - Section {{ payment.payable.permanent_grave.section }}, Row {{ payment.payable.permanent_grave.row_no }}
                        </template>
                        <template v-else-if="payment.payable.temporary_grave">
                          {{ payment.payable.temporary_grave.grave_no }} - Section {{ payment.payable.temporary_grave.section }}, Row {{ payment.payable.temporary_grave.row_no }}
                        </template>
                        <template v-else>
                          Grave details not available
                        </template>
                      </p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Applicant</Label>
                      <p class="text-base">{{ payment.payable.applicant_name }}</p>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Amount Summary -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <Receipt class="h-5 w-5" />
                      <span>Amount Summary</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                      <!-- Show service subtotal and concession breakdown if concession was applied -->
                      <div v-if="payment.concession_amount > 0" class="flex items-center justify-between border-b pb-2">
                        <span class="text-sm font-medium text-gray-500">Service Subtotal</span>
                        <span class="text-base font-medium">{{ formatCurrency(calculateServiceSubtotal()) }}</span>
                      </div>

                      <div v-if="payment.concession_amount > 0" class="flex items-center justify-between border-b pb-2">
                        <span class="text-sm font-medium text-orange-600">Concession Discount</span>
                        <span class="text-base font-medium text-orange-600">- {{ formatCurrency(payment.concession_amount) }}</span>
                      </div>

                      <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Total Amount</span>
                        <span class="text-lg font-bold">{{ formatCurrency(payment.total_amount) }}</span>
                      </div>

                      <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Amount Paid</span>
                        <span class="text-lg font-semibold text-green-600">{{ formatCurrency(payment.paid_amount) }}</span>
                      </div>

                      <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Balance Amount</span>
                        <span class="text-lg font-semibold" :class="payment.balance_amount > 0 ? 'text-red-600' : 'text-green-600'">
                          {{ formatCurrency(payment.balance_amount) }}
                        </span>
                      </div>

                      <!-- Show total savings if concession was applied -->
                      <div v-if="payment.concession_amount > 0" class="rounded-md bg-orange-50 p-3 mt-4">
                        <div class="flex items-center justify-between">
                          <div>
                            <p class="text-sm font-medium text-orange-800">Total Savings</p>
                            <p class="text-xs text-orange-600">Concession discount applied</p>
                          </div>
                          <span class="text-lg font-bold text-orange-700">{{ formatCurrency(payment.concession_amount) }}</span>
                        </div>
                      </div>
                    </div>

                    <Separator />

                    <div v-if="payment.receipt_number" class="rounded-md bg-green-50 p-3">
                      <div class="flex items-center space-x-2">
                        <Receipt class="h-4 w-4 text-green-600" />
                        <div>
                          <p class="text-sm font-medium text-green-800">Receipt Generated</p>
                          <p class="text-xs text-green-600">{{ payment.receipt_number }}</p>
                          <p class="text-xs text-green-600">{{ formatDateTime(payment.receipt_generated_at!) }}</p>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Service Breakdown -->
                <Card v-if="payment.service_charges && payment.service_charges.length > 0">
                  <CardHeader>
                    <CardTitle>Service Breakdown</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div
                      v-for="service in payment.service_charges"
                      :key="service.service_id"
                      class="flex items-center justify-between border-b border-gray-100 py-2 last:border-b-0"
                    >
                      <div>
                        <p class="font-medium">{{ service.service_name }}</p>
                        <p class="text-sm text-gray-500">{{ service.quantity }} × {{ formatCurrency(service.unit_cost) }}</p>
                      </div>
                      <div class="text-right">
                        <p class="font-medium">{{ formatCurrency(service.total_cost) }}</p>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Audit Information -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <Calendar class="h-5 w-5" />
                      <span>Record Information</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-1 gap-2 text-sm text-gray-500">
                      <div class="flex justify-between">
                        <span>Recorded by:</span>
                        <span>{{ payment.creator.name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span>Recorded on:</span>
                        <span>{{ formatDateTime(payment.created_at) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span>Last updated:</span>
                        <span>{{ formatDateTime(payment.updated_at) }}</span>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-between">
              <Button variant="outline" as-child>
                <Link :href="getBookingRoute()"> Back to Booking </Link>
              </Button>

              <div class="flex space-x-3">
                <Button v-if="payment.receipt_number" @click="generateReceipt" variant="outline">
                  <Download class="mr-2 h-4 w-4" />
                  Download Receipt
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
