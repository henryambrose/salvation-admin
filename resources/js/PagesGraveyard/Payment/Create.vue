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
  type: string;
  applicable_to: string;
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
  contact_no: string;
  died_on: string;
  buried_on: string;
  selected_services: number[];
  total_cost: number;
  paid_amount: number;
  balance_amount: number;
  payment_status: string;
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
  concession_amount: 0,
});

// Initialize form

// Reactive data
const selectedServiceIds = ref<number[]>([]);

// Initialize selected services from booking
const initializeServices = () => {
  if (props.selectedServices && props.selectedServices.length > 0) {
    selectedServiceIds.value = [...props.selectedServices];
    form.selected_services = props.selectedServices
      .map((serviceId) => {
        const service = props.serviceTypes.find((s) => s.id === serviceId);
        if (service) {
          const serviceCost = Number(service.cost) || 0;
          return {
            service_id: service.id,
            service_name: service.name,
            quantity: 1,
            unit_cost: serviceCost,
            total_cost: serviceCost,
          };
        }
        return null;
      })
      .filter(Boolean) as SelectedService[];
  } else {
    // If no pre-selected services, ensure arrays are empty
    selectedServiceIds.value = [];
    form.selected_services = [];
  }
};

// Initialize on component mount
initializeServices();

// Helper function to get booking type filter
const getBookingTypeFilter = () => {
  return props.bookingType; // 'permanent', 'temporary', 'niche'
};

// Computed properties
const availableServices = computed(() => {
  let filteredServices = props.serviceTypes.filter((service) => !selectedServiceIds.value.includes(service.id));

  // Filter services based on booking type
  const bookingTypeFilter = getBookingTypeFilter();

  if (bookingTypeFilter) {
    filteredServices = filteredServices.filter(
      (service) => service.applicable_to === bookingTypeFilter || service.applicable_to === 'all' || !service.applicable_to, // Include services with null/undefined applicable_to
    );
  }

  // If free services are selected, hide all paid services
  if (hasFreeServices.value) {
    filteredServices = filteredServices.filter((service) => service.type === 'free');
  }
  // If paid services are selected, hide all free services
  else if (hasPaidServices.value) {
    filteredServices = filteredServices.filter((service) => service.type !== 'free');
  }

  return filteredServices;
});

const hasFreeServices = computed(() => {
  const freeServicesSelected = form.selected_services.some((service) => {
    const serviceType = props.serviceTypes.find((s) => s.id === service.service_id);
    return serviceType?.type === 'free';
  });

  // Auto-set payment amount to 0 for free services
  if (freeServicesSelected && form.paid_amount !== 0) {
    form.paid_amount = 0;
  }

  return freeServicesSelected;
});

const hasPaidServices = computed(() => {
  return form.selected_services.some((service) => {
    const serviceType = props.serviceTypes.find((s) => s.id === service.service_id);
    return serviceType?.type !== 'free';
  });
});

const hasConcessionServices = computed(() => {
  return form.selected_services.some((service) => {
    const serviceType = props.serviceTypes.find((s) => s.id === service.service_id);
    return serviceType?.type === 'concession';
  });
});

const hasNormalServices = computed(() => {
  return form.selected_services.some((service) => {
    const serviceType = props.serviceTypes.find((s) => s.id === service.service_id);
    return serviceType?.type === 'normal';
  });
});

const showConcessionField = computed(() => {
  return hasConcessionServices.value && (hasNormalServices.value || hasConcessionServices.value);
});

const totalAmount = computed(() => {
  const serviceTotal = form.selected_services.reduce((sum, service) => {
    const cost = Number(service.total_cost) || 0;
    return sum + cost;
  }, 0);

  const concessionAmount = Number(form.concession_amount) || 0;
  const servicesTotal = Math.max(0, serviceTotal - concessionAmount);

  // If no services selected, this is a balance payment - show the outstanding balance
  if (form.selected_services.length === 0 && props.booking.balance_amount > 0) {
    return Number(props.booking.balance_amount) || 0;
  }

  return servicesTotal;
});

const balanceAmount = computed(() => {
  const currentPaidAmount = Number(form.paid_amount) || 0;

  // Calculate remaining balance after current payment
  return Math.max(0, totalAmount.value - currentPaidAmount);
});

const paymentStatus = computed(() => {
  const paidAmount = Number(form.paid_amount) || 0;
  if (paidAmount === 0) return 'pending';

  // For balance payments or service payments, check if fully paid
  if (paidAmount >= totalAmount.value) return 'completed';
  return 'partial';
});

const maxPaymentAmount = computed(() => {
  // For balance payments, limit to booking's remaining balance
  if (form.selected_services.length === 0 && props.booking.balance_amount > 0) {
    return Number(props.booking.balance_amount);
  }

  // For service payments, limit to total amount
  return totalAmount.value;
});

const paymentAmountError = computed(() => {
  const paidAmount = Number(form.paid_amount) || 0;
  if (paidAmount > maxPaymentAmount.value) {
    const amountType = form.selected_services.length === 0 ? 'balance' : 'total';
    return `Payment amount cannot exceed the ${amountType} amount of ${formatCurrency(maxPaymentAmount.value)}`;
  }
  return null;
});

// Computed properties for displaying selected values
const selectedPaymentMethod = computed(() => {
  return props.paymentMethods.find((method) => method.id === parseInt(form.payment_method_id));
});

const bookingShowRoute = computed(() => {
  const routes = {
    permanent: 'graveyard.permanent-grave-bookings.show',
    temporary: 'graveyard.temporary-grave-bookings.show',
    niche: 'graveyard.niche-transfers.show',
  };
  return routes[props.bookingType as keyof typeof routes] || 'graveyard.dashboard';
});

// Methods
const addService = (serviceId: number) => {
  const service = props.serviceTypes.find((s) => s.id === serviceId);
  if (!service) {
    return;
  }

  // Check for conflicts between free and paid services
  const isServiceFree = service.type === 'free';
  const isServicePaid = service.type !== 'free';

  if (isServiceFree && hasPaidServices.value) {
    alert('You cannot add free services when paid services are selected. Please remove all paid services first.');
    return;
  }

  if (isServicePaid && hasFreeServices.value) {
    alert('You cannot add paid services when free services are selected. Please remove all free services first.');
    return;
  }

  selectedServiceIds.value.push(serviceId);
  const serviceCost = isServiceFree ? 0 : Number(service.cost) || 0;
  form.selected_services.push({
    service_id: service.id,
    service_name: service.name,
    quantity: 1,
    unit_cost: serviceCost,
    total_cost: serviceCost,
  });
};

const removeService = (index: number) => {
  const serviceId = form.selected_services[index].service_id;
  selectedServiceIds.value = selectedServiceIds.value.filter((id) => id !== serviceId);
  form.selected_services.splice(index, 1);

  // Clear concession amount if no concession services remain
  if (!hasConcessionServices.value) {
    form.concession_amount = 0;
  }
};

const updateServiceCost = (index: number) => {
  const service = form.selected_services[index];
  const quantity = Number(service.quantity) || 1;
  const unitCost = Number(service.unit_cost) || 0;
  service.total_cost = quantity * unitCost;
};

const setPaymentToTotal = () => {
  form.paid_amount = totalAmount.value;
};

const setPaymentToBalance = () => {
  if (form.selected_services.length === 0 && props.booking.balance_amount > 0) {
    form.paid_amount = Number(props.booking.balance_amount);
  }
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
  if (props.bookingType === 'temporary' || props.bookingType === 'niche-transfer') {
    return `${props.booking.dead_first_name} ${props.booking.dead_last_name}`;
  } else if (props.bookingType === 'permanent') {
    return `${props.booking?.valid_member?.first_name} ${props.booking?.valid_member?.last_name}`;
  }
  return 'Unknown';
};

const submit = () => {
  // Create a copy of form data with proper types
  const formData = {
    ...form.data(),
    // For free services, set payment_method_id to null, otherwise parse the selected value
    payment_method_id: hasFreeServices.value ? null : parseInt(form.payment_method_id) || null,
  };

  form.transform((data) => formData).post(route('graveyard.payments.store'));
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
                  <Link :href="route(bookingShowRoute, booking.id)">
                    <ArrowLeft class="h-4 w-4" />
                  </Link>
                </Button>
                <div>
                  <h3 class="text-base leading-6 font-semibold text-gray-900">Payment for Booking #{{ booking.booking_reference }}</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Record payment for grave booking services</p>
                </div>
              </div>
              <Badge :class="hasFreeServices ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                {{ hasFreeServices ? 'Free Service - No Payment Required' : 'Payment Required' }}
              </Badge>
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
                        <template v-if="bookingType === 'permanent' && booking.permanent_grave">
                          {{ booking.permanent_grave.grave_no }} - Section {{ booking.permanent_grave.section }}, Row
                          {{ booking.permanent_grave.row_no }}
                        </template>
                        <template v-else-if="bookingType === 'temporary' && booking.temporary_grave">
                          {{ booking.temporary_grave.grave_no }} - Section {{ booking.temporary_grave.section }}, Row
                          {{ booking.temporary_grave.row_no }}
                        </template>
                      </p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Applicant</Label>
                      <p class="text-base">{{ booking.applicant_name }}</p>
                    </div>
                  </CardContent>
                </Card>

                <!-- Existing Payment Status -->
                <Card v-if="booking.total_cost > 0 || booking.paid_amount > 0">
                  <CardHeader>
                    <CardTitle>Payment Status</CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Total Cost</Label>
                        <p class="text-base font-medium">{{ formatCurrency(booking.total_cost) }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Payment Status</Label>
                        <Badge
                          :class="{
                            'bg-yellow-100 text-yellow-800': booking.payment_status === 'pending',
                            'bg-blue-100 text-blue-800': booking.payment_status === 'partial',
                            'bg-green-100 text-green-800': ['paid', 'completed'].includes(booking.payment_status),
                          }"
                        >
                          {{ booking.payment_status }}
                        </Badge>
                      </div>
                    </div>

                    <div v-if="booking.paid_amount > 0" class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Paid Amount</Label>
                        <p class="text-base font-medium text-green-600">{{ formatCurrency(booking.paid_amount) }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Balance Due</Label>
                        <p class="text-base font-medium text-red-600">{{ formatCurrency(booking.balance_amount) }}</p>
                      </div>
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
                      <div class="mb-2 rounded bg-blue-50 px-2 py-1 text-xs text-blue-600">
                        📋 Showing services for {{ bookingType }} grave bookings
                      </div>
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
                        <option v-for="service in availableServices" :key="service.id" :value="service.id.toString()">
                          {{ service.name }} -
                          <span v-if="service.type === 'free'">FREE</span>
                          <span v-else-if="service.type === 'concession'">{{ formatCurrency(service.cost) }} (Concession Available)</span>
                          <span v-else>{{ formatCurrency(service.cost) }}</span>
                        </option>
                      </select>

                      <!-- Selected Services List -->
                      <div v-if="form.selected_services.length > 0" class="mb-4 space-y-2">
                        <div
                          v-for="(service, index) in form.selected_services"
                          :key="service.service_id"
                          :class="{
                            'flex items-center justify-between rounded-md p-2 text-sm': true,
                            'border border-green-200 bg-green-50': service.total_cost === 0,
                            'border border-orange-200 bg-orange-50':
                              props.serviceTypes.find((s) => s.id === service.service_id)?.type === 'concession',
                            'bg-gray-50': service.total_cost > 0 && props.serviceTypes.find((s) => s.id === service.service_id)?.type === 'normal',
                          }"
                        >
                          <div class="flex-1">
                            <span class="font-medium">{{ service.service_name }}</span>
                            <span
                              :class="{
                                'ml-2 font-semibold text-green-600': service.total_cost === 0,
                                'ml-2 font-medium text-orange-600':
                                  props.serviceTypes.find((s) => s.id === service.service_id)?.type === 'concession',
                                'ml-2 text-gray-500':
                                  service.total_cost > 0 && props.serviceTypes.find((s) => s.id === service.service_id)?.type === 'normal',
                              }"
                            >
                              <span v-if="service.total_cost === 0">FREE</span>
                              <span v-else-if="props.serviceTypes.find((s) => s.id === service.service_id)?.type === 'concession'">
                                {{ formatCurrency(service.total_cost) }} (Concession)
                              </span>
                              <span v-else>{{ formatCurrency(service.total_cost) }}</span>
                            </span>
                          </div>
                          <button @click="removeService(index)" type="button" class="px-2 py-1 text-xs text-red-600 hover:text-red-800">
                            Remove
                          </button>
                        </div>
                        <div class="border-t pt-2 font-semibold">Total: {{ formatCurrency(totalAmount) }}</div>
                      </div>

                      <div class="text-xs text-gray-500">
                        Services available: {{ availableServices?.length || 0 }} | Selected: {{ form.selected_services.length }}
                        <div v-if="hasFreeServices" class="mt-1 text-green-600">
                          ✓ Free services selected - only additional free services can be added
                        </div>
                        <div v-else-if="hasPaidServices" class="mt-1 text-blue-600">✓ Paid services selected - free services are not available</div>
                      </div>
                    </div>

                    <div>
                      <Label for="payment_method_id">Payment Method <span v-if="!hasFreeServices">*</span></Label>
                      <select
                        v-model="form.payment_method_id"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                        :class="hasFreeServices ? 'bg-gray-100 text-gray-500' : ''"
                        :required="!hasFreeServices"
                        :disabled="hasFreeServices"
                      >
                        <option value="">{{ hasFreeServices ? 'Not applicable for free services' : 'Select payment method' }}</option>
                        <option v-for="method in paymentMethods" :key="method.id" :value="method.id.toString()" :disabled="hasFreeServices">
                          {{ method.name }}
                        </option>
                      </select>
                      <div class="mt-1 text-xs" :class="hasFreeServices ? 'text-green-600' : 'text-gray-500'">
                        <span v-if="hasFreeServices">Payment method not required for free services</span>
                        <span v-else>Select how the payment was received</span>
                      </div>
                      <div v-if="form.errors.payment_method_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.payment_method_id }}
                      </div>
                    </div>

                    <!-- Concession Amount Field (only show if concession services are selected) -->
                    <div v-if="showConcessionField">
                      <Label for="concession_amount">Concession Amount</Label>
                      <Input
                        id="concession_amount"
                        v-model.number="form.concession_amount"
                        type="number"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        class="w-full"
                      />
                      <div class="mt-1 text-xs text-gray-500">Enter the concession amount to be deducted from the total</div>
                      <div v-if="form.errors.concession_amount" class="mt-1 text-sm text-red-600">
                        {{ form.errors.concession_amount }}
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
                          :max="maxPaymentAmount"
                          step="0.01"
                          placeholder="0.00"
                          class="flex-1"
                          :class="paymentAmountError ? 'border-red-500' : ''"
                          :disabled="hasFreeServices"
                          :readonly="hasFreeServices"
                        />
                        <Button
                          @click="setPaymentToTotal"
                          type="button"
                          variant="outline"
                          size="sm"
                          class="whitespace-nowrap"
                          :disabled="hasFreeServices"
                        >
                          <Calculator class="mr-1 h-4 w-4" />
                          {{ form.selected_services.length === 0 && booking.balance_amount > 0 ? 'Balance' : 'Full' }}
                        </Button>
                      </div>
                      <div class="mt-1 text-xs" :class="hasFreeServices ? 'text-green-600' : 'text-gray-500'">
                        <span v-if="hasFreeServices">Free service selected - No payment required</span>
                        <span v-else>Maximum: {{ formatCurrency(maxPaymentAmount) }}</span>
                      </div>
                      <div v-if="paymentAmountError || form.errors.paid_amount" class="mt-1 text-sm text-red-600">
                        {{ paymentAmountError || form.errors.paid_amount }}
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
                    <!-- Free Service Special Notice -->
                    <div v-if="hasFreeServices" class="rounded-md border border-green-200 bg-green-50 p-3">
                      <div class="flex items-center space-x-2">
                        <span class="font-medium text-green-600">✓ Free Service Selected</span>
                      </div>
                      <div class="mt-1 text-sm text-green-700">This service is provided free of charge as a privilege for community members.</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                      <!-- Show service subtotal if concession is applied -->
                      <div v-if="showConcessionField && form.concession_amount > 0" class="col-span-2 flex justify-between">
                        <span class="text-gray-500">Service Subtotal:</span>
                        <span class="font-medium">{{
                          formatCurrency(form.selected_services.reduce((sum, service) => sum + (Number(service.total_cost) || 0), 0))
                        }}</span>
                      </div>
                      <div v-if="showConcessionField && form.concession_amount > 0" class="col-span-2 flex justify-between">
                        <span class="text-orange-600">Concession Discount:</span>
                        <span class="font-medium text-orange-600">- {{ formatCurrency(Number(form.concession_amount) || 0) }}</span>
                      </div>
                      <!-- Balance Payment Info -->
                      <div
                        v-if="form.selected_services.length === 0 && booking.balance_amount > 0"
                        class="col-span-2 rounded-md border border-blue-200 bg-blue-50 p-2"
                      >
                        <div class="mb-2 text-sm font-medium text-blue-800">Balance Payment</div>
                        <div class="text-xs text-blue-700">Outstanding balance from previous payment</div>
                      </div>

                      <div class="flex justify-between">
                        <span class="text-gray-500">
                          {{ form.selected_services.length > 0 ? 'Service Total:' : 'Outstanding Balance:' }}
                        </span>
                        <span class="font-medium">{{ formatCurrency(totalAmount) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-500">Amount Paying:</span>
                        <span class="font-medium">{{ formatCurrency(Number(form.paid_amount) || 0) }}</span>
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
                      <Button
                        type="submit"
                        :disabled="form.processing || (form.selected_services.length === 0 && booking.balance_amount <= 0) || !!paymentAmountError"
                        class="flex-1"
                      >
                        <span v-if="form.processing">{{ hasFreeServices ? 'Confirming...' : 'Recording...' }}</span>
                        <span v-else-if="hasFreeServices">Confirm Free Service</span>
                        <span v-else>Record Payment</span>
                      </Button>
                      <Button type="button" variant="outline" as-child>
                        <Link :href="route(bookingShowRoute, booking.id)"> Cancel </Link>
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
