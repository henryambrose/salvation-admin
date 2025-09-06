<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Calculator } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface ServiceType {
  id: number;
  name: string;
  cost: number;
  category: string;
  is_active: boolean;
}

interface PaymentMethod {
  id: number;
  name: string;
  is_active: boolean;
}

interface SelectedService {
  service_id: number;
  service_name: string;
  quantity: number;
  unit_cost: number;
  total_cost: number;
  [key: string]: any;
}

interface Booking {
  id: number;
  booking_reference: string;
  status: string;
  permanent_grave: {
    grave_no: string;
    owner_name: string;
    section: string;
    row_no: string;
  };
  valid_member: {
    first_name: string;
    last_name: string;
    relationship: string;
  };
  applicant_name: string;
  contact_no: string;
  died_on: string;
  buried_on: string;
  selected_services: number[];
}

interface Props {
  booking: Booking;
  bookingType: string;
  serviceTypes: ServiceType[];
  paymentMethods: PaymentMethod[];
  selectedServices: number[];
  existingPayment?: any;
}

const props = defineProps<Props>();

// Form setup
const form = useForm({
  booking_type: props.bookingType,
  booking_id: props.booking.id,
  selected_services: [] as SelectedService[],
  payment_method_id: '',
  paid_amount: 0,
  payment_date: new Date().toISOString().split('T')[0],
  transaction_reference: '',
  payment_notes: '',
});

// Initialize form - Debug all received data
console.log('=== PAYMENT FORM DATA DEBUG ===');
console.log('PaymentMethods available:', props.paymentMethods);
console.log('ServiceTypes available:', props.serviceTypes);
console.log('Booking data:', props.booking);
console.log('Selected services from booking:', props.selectedServices);
console.log('Existing payment:', props.existingPayment);
console.log('================================');

// Reactive data
const selectedServiceIds = ref<number[]>([]);

// Initialize selected services from booking
const initializeServices = () => {
  console.log('Initializing services:', props.selectedServices);
  console.log('Available service types:', props.serviceTypes);

  if (props.selectedServices && props.selectedServices.length > 0) {
    selectedServiceIds.value = [...props.selectedServices];
    form.selected_services = props.selectedServices
      .map((serviceId) => {
        const service = props.serviceTypes.find((s) => s.id === serviceId);
        if (service) {
          return {
            service_id: service.id,
            service_name: service.name,
            quantity: 1,
            unit_cost: service.cost,
            total_cost: service.cost,
          };
        }
        return null;
      })
      .filter(Boolean) as SelectedService[];
    console.log('Initialized services:', form.selected_services);
  } else {
    // If no pre-selected services, ensure arrays are empty
    selectedServiceIds.value = [];
    form.selected_services = [];
    console.log('No services pre-selected');
  }
};

// Initialize on component mount
initializeServices();

// Computed properties
const availableServices = computed(() => {
  return props.serviceTypes.filter((service) => !selectedServiceIds.value.includes(service.id));
});

const totalAmount = computed(() => {
  return form.selected_services.reduce((sum, service) => sum + service.total_cost, 0);
});

const balanceAmount = computed(() => {
  return Math.max(0, totalAmount.value - form.paid_amount);
});

const paymentStatus = computed(() => {
  if (form.paid_amount === 0) return 'pending';
  if (form.paid_amount >= totalAmount.value) return 'completed';
  return 'partial';
});

// Computed properties for displaying selected values
const selectedPaymentMethod = computed(() => {
  return props.paymentMethods.find((method) => method.id === parseInt(form.payment_method_id));
});

// Methods
const addService = (serviceId: number) => {
  console.log('Adding service:', serviceId);
  const service = props.serviceTypes.find((s) => s.id === serviceId);
  if (service) {
    console.log('Found service:', service.name);
    selectedServiceIds.value.push(serviceId);
    form.selected_services.push({
      service_id: service.id,
      service_name: service.name,
      quantity: 1,
      unit_cost: service.cost,
      total_cost: service.cost,
    });
    console.log('Updated selected services:', form.selected_services);
  } else {
    console.log('Service not found for ID:', serviceId);
  }
};

const removeService = (index: number) => {
  const serviceId = form.selected_services[index].service_id;
  selectedServiceIds.value = selectedServiceIds.value.filter((id) => id !== serviceId);
  form.selected_services.splice(index, 1);
};

const updateServiceCost = (index: number) => {
  const service = form.selected_services[index];
  service.total_cost = service.quantity * service.unit_cost;
};

const setPaymentToTotal = () => {
  form.paid_amount = totalAmount.value;
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

const getDeceasedName = () => {
  return `${props.booking.valid_member.first_name} ${props.booking.valid_member.last_name}`;
};

const submit = () => {
  // Create a copy of form data with proper types
  const formData = {
    ...form.data(),
    payment_method_id: parseInt(form.payment_method_id) || null,
    payment_mode: 'online', // Set default payment mode since we removed the dropdown
  };

  console.log('Submitting payment data:', formData);

  form
    .transform((data) => formData)
    .post(route('graveyard.payments.store'), {
      onSuccess: () => {
        console.log('Payment submitted successfully');
        // Will redirect to payment show page
      },
      onError: (errors) => {
        console.error('Payment submission errors:', errors);
      },
    });
};
</script>

<template>
  <Head title="Make Payment" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Button variant="outline" size="sm" as-child>
                  <Link :href="route('graveyard.permanent-grave-bookings.show', booking.id)">
                    <ArrowLeft class="h-4 w-4" />
                  </Link>
                </Button>
                <div>
                  <h3 class="text-base leading-6 font-semibold text-gray-900">Payment for Booking #{{ booking.booking_reference }}</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Record payment for grave booking services</p>
                </div>
              </div>
              <Badge class="bg-yellow-100 text-yellow-800"> Payment Required </Badge>
            </div>
          </div>

          <form @submit.prevent="submit" class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <!-- Left Column - Booking Details -->
              <div class="space-y-6">
                <!-- Booking Summary -->
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
                        <p class="text-base">{{ formatDate(booking.died_on) }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Date of Burial</Label>
                        <p class="text-base">{{ formatDate(booking.buried_on) }}</p>
                      </div>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Grave Details</Label>
                      <p class="text-base">
                        {{ booking.permanent_grave.grave_no }} - Section {{ booking.permanent_grave.section }}, Row
                        {{ booking.permanent_grave.row_no }}
                      </p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Applicant</Label>
                      <p class="text-base">{{ booking.applicant_name }}</p>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Right Column - Payment Details -->
              <div class="space-y-6">
                <!-- Payment Information -->
                <Card>
                  <CardHeader>
                    <CardTitle>Payment Details</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <!-- Service Selection -->
                    <div>
                      <Label for="service_selection">Select Services *</Label>
                      <select
                        @change="
                          (e) => {
                            const target = e.target as HTMLSelectElement;
                            if (target.value) {
                              addService(parseInt(target.value));
                              target.value = '';
                            }
                          }
                        "
                        class="mb-2 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                      >
                        <option value="">Add a service...</option>
                        <option v-for="service in props.serviceTypes" :key="service.id" :value="service.id.toString()">
                          {{ service.name }} - {{ formatCurrency(service.cost) }}
                        </option>
                      </select>

                      <!-- Selected Services List -->
                      <div v-if="form.selected_services.length > 0" class="mb-4 space-y-2">
                        <div
                          v-for="(service, index) in form.selected_services"
                          :key="service.service_id"
                          class="flex items-center justify-between rounded-md bg-gray-50 p-2 text-sm"
                        >
                          <div class="flex-1">
                            <span class="font-medium">{{ service.service_name }}</span>
                            <span class="ml-2 text-gray-500">{{ formatCurrency(service.total_cost) }}</span>
                          </div>
                          <button @click="removeService(index)" type="button" class="px-2 py-1 text-xs text-red-600 hover:text-red-800">
                            Remove
                          </button>
                        </div>
                        <div class="border-t pt-2 font-semibold">Total: {{ formatCurrency(totalAmount) }}</div>
                      </div>

                      <div class="text-xs text-gray-500">
                        Services available: {{ props.serviceTypes?.length || 0 }} | Selected: {{ form.selected_services.length }}
                      </div>
                    </div>

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
                      <Label for="paid_amount">Amount Paid *</Label>
                      <div class="flex space-x-2">
                        <Input
                          id="paid_amount"
                          v-model.number="form.paid_amount"
                          type="number"
                          min="0"
                          step="0.01"
                          placeholder="0.00"
                          class="flex-1"
                        />
                        <Button @click="setPaymentToTotal" type="button" variant="outline" size="sm" class="whitespace-nowrap">
                          <Calculator class="mr-1 h-4 w-4" />
                          Full
                        </Button>
                      </div>
                      <div v-if="form.errors.paid_amount" class="mt-1 text-sm text-red-600">
                        {{ form.errors.paid_amount }}
                      </div>
                    </div>

                    <div>
                      <Label for="payment_date">Payment Date *</Label>
                      <Input id="payment_date" v-model="form.payment_date" type="date" />
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
                      <Textarea id="payment_notes" v-model="form.payment_notes" placeholder="Additional payment notes..." rows="3" />
                    </div>
                  </CardContent>
                </Card>

                <!-- Payment Summary -->
                <Card>
                  <CardHeader>
                    <CardTitle>Payment Summary</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                      <div class="flex justify-between">
                        <span class="text-gray-500">Total Amount:</span>
                        <span class="font-medium">{{ formatCurrency(totalAmount) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Amount Paying:</span>
                        <span class="font-medium">{{ formatCurrency(form.paid_amount) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Balance:</span>
                        <span class="font-medium" :class="balanceAmount > 0 ? 'text-red-600' : 'text-green-600'">
                          {{ formatCurrency(balanceAmount) }}
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Status:</span>
                        <Badge
                          :class="{
                            'bg-green-100 text-green-800': paymentStatus === 'completed',
                            'bg-blue-100 text-blue-800': paymentStatus === 'partial',
                            'bg-yellow-100 text-yellow-800': paymentStatus === 'pending',
                          }"
                        >
                          {{ paymentStatus }}
                        </Badge>
                      </div>
                    </div>

                    <Separator />

                    <div class="flex space-x-3">
                      <Button type="submit" :disabled="form.processing || form.selected_services.length === 0" class="flex-1">
                        {{ form.processing ? 'Recording...' : 'Record Payment' }}
                      </Button>
                      <Button type="button" variant="outline" as-child>
                        <Link :href="route('graveyard.permanent-grave-bookings.show', booking.id)"> Cancel </Link>
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
