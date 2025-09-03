<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle, DollarSign, FileText, MapPin, Phone, User, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface PermanentGraveBooking {
  id: number;
  booking_reference: string;
  status: 'pending' | 'confirmed' | 'cancelled';
  permanent_grave: {
    id: number;
    grave_no: string;
    section: string;
    row_no: string;
    owner_name: string;
    last_burial_date?: string;
  };
  valid_member: {
    id: number;
    first_name: string;
    last_name: string;
    relationship: string;
    member?: {
      first_name: string;
      last_name: string;
      member_no: string;
    };
  };
  died_on: string;
  buried_on: string;
  cause_of_death: string;
  minister?: string;
  applicant_type: 'member' | 'non_member';
  applicant_name: string;
  contact_no: string;
  contact_email?: string;
  permit_no?: string;
  selected_services?: number[];
  total_cost: number;
  paid_amount: number;
  balance_amount: number;
  payment_status: 'pending' | 'partial' | 'paid';
  special_requirements?: string;
  remarks?: string;
  created_at: string;
  updated_at: string;
  creator: {
    name: string;
  };
  updater?: {
    name: string;
  };
}

interface Props {
  booking: PermanentGraveBooking;
}

const props = defineProps<Props>();

const showCancelDialog = ref(false);
const cancelReason = ref('');
const isProcessing = ref(false);

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
};

const paymentStatusColors = {
  pending: 'bg-orange-100 text-orange-800',
  partial: 'bg-blue-100 text-blue-800',
  paid: 'bg-green-100 text-green-800',
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
  const validMember = props.booking.valid_member;
  if (validMember.member) {
    return `${validMember.member.first_name} ${validMember.member.last_name}`;
  }
  return `${validMember.first_name} ${validMember.last_name}`;
};

const confirmBooking = () => {
  router.post(route('graveyard.permanent-grave-bookings.confirm', props.booking.id));
};

const cancelBooking = () => {
  if (!cancelReason.value.trim()) return;

  isProcessing.value = true;
  router.post(
    route('graveyard.permanent-grave-bookings.cancel', props.booking.id),
    {
      cancellation_reason: cancelReason.value,
    },
    {
      onFinish: () => {
        isProcessing.value = false;
        showCancelDialog.value = false;
        cancelReason.value = '';
      },
    },
  );
};

const canConfirm = () => {
  return props.booking.status === 'pending';
};

const canCancel = () => {
  return ['pending', 'confirmed'].includes(props.booking.status);
};
</script>

<template>
  <Head :title="`Booking #${booking.booking_reference}`" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Button variant="outline" size="sm" as-child>
                  <Link :href="route('graveyard.permanent-grave-bookings.index')">
                    <ArrowLeft class="h-4 w-4" />
                  </Link>
                </Button>
                <div>
                  <h3 class="text-base leading-6 font-semibold text-gray-900">Booking #{{ booking.booking_reference }}</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Permanent grave booking details</p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <Badge :class="statusColors[booking.status]">
                  {{ booking.status }}
                </Badge>
                <div class="flex space-x-2">
                  <Button v-if="canConfirm()" @click="confirmBooking" class="bg-green-600 hover:bg-green-700">
                    <CheckCircle class="mr-2 h-4 w-4" />
                    Confirm
                  </Button>
                  <Dialog v-if="canCancel()" v-model:open="showCancelDialog">
                    <DialogTrigger as-child>
                      <Button variant="outline" class="border-red-200 text-red-600 hover:bg-red-50">
                        <XCircle class="mr-2 h-4 w-4" />
                        Cancel
                      </Button>
                    </DialogTrigger>
                    <DialogContent>
                      <DialogHeader>
                        <DialogTitle>Cancel Booking</DialogTitle>
                        <DialogDescription> Are you sure you want to cancel this booking? This action cannot be undone. </DialogDescription>
                      </DialogHeader>
                      <div class="py-4">
                        <Label for="cancel-reason">Cancellation Reason *</Label>
                        <Textarea
                          id="cancel-reason"
                          v-model="cancelReason"
                          placeholder="Please provide a reason for cancellation..."
                          rows="3"
                          class="mt-1"
                        />
                      </div>
                      <DialogFooter>
                        <Button variant="outline" @click="showCancelDialog = false"> Keep Booking </Button>
                        <Button variant="destructive" @click="cancelBooking" :disabled="!cancelReason.trim() || isProcessing">
                          {{ isProcessing ? 'Cancelling...' : 'Cancel Booking' }}
                        </Button>
                      </DialogFooter>
                    </DialogContent>
                  </Dialog>
                </div>
              </div>
            </div>
          </div>

          <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <!-- Left Column -->
              <div class="space-y-6">
                <!-- Deceased Information -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <User class="h-5 w-5" />
                      <span>Deceased Information</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label class="text-sm font-medium text-gray-500">Full Name</Label>
                      <p class="text-base font-medium">{{ getDeceasedName() }}</p>
                    </div>

                    <div v-if="booking.valid_member.member">
                      <Label class="text-sm font-medium text-gray-500">Member Number</Label>
                      <p class="text-base">{{ booking.valid_member.member.member_no }}</p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Relationship</Label>
                      <p class="text-base">{{ booking.valid_member.relationship }}</p>
                    </div>

                    <Separator />

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
                      <Label class="text-sm font-medium text-gray-500">Cause of Death</Label>
                      <p class="text-base">{{ booking.cause_of_death }}</p>
                    </div>

                    <div v-if="booking.minister">
                      <Label class="text-sm font-medium text-gray-500">Minister</Label>
                      <p class="text-base">{{ booking.minister }}</p>
                    </div>
                  </CardContent>
                </Card>

                <!-- Grave Information -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <MapPin class="h-5 w-5" />
                      <span>Grave Information</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label class="text-sm font-medium text-gray-500">Grave Number</Label>
                      <p class="text-base font-medium">{{ booking.permanent_grave.grave_no }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Section</Label>
                        <p class="text-base">{{ booking.permanent_grave.section }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Row</Label>
                        <p class="text-base">{{ booking.permanent_grave.row_no }}</p>
                      </div>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Owner</Label>
                      <p class="text-base">{{ booking.permanent_grave.owner_name }}</p>
                    </div>

                    <div v-if="booking.permanent_grave.last_burial_date">
                      <Label class="text-sm font-medium text-gray-500">Last Burial</Label>
                      <p class="text-base">{{ formatDate(booking.permanent_grave.last_burial_date) }}</p>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Contact Information -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <Phone class="h-5 w-5" />
                      <span>Contact Information</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label class="text-sm font-medium text-gray-500">Applicant Type</Label>
                      <Badge :class="booking.applicant_type === 'member' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'">
                        {{ booking.applicant_type === 'member' ? 'Church Member' : 'Non-Member' }}
                      </Badge>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Applicant Name</Label>
                      <p class="text-base font-medium">{{ booking.applicant_name }}</p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Contact Number</Label>
                      <p class="text-base">{{ booking.contact_no }}</p>
                    </div>

                    <div v-if="booking.contact_email">
                      <Label class="text-sm font-medium text-gray-500">Email</Label>
                      <p class="text-base">{{ booking.contact_email }}</p>
                    </div>

                    <div v-if="booking.permit_no">
                      <Label class="text-sm font-medium text-gray-500">BMC Permit Number</Label>
                      <p class="text-base">{{ booking.permit_no }}</p>
                    </div>
                  </CardContent>
                </Card>

                <!-- Financial Information -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <DollarSign class="h-5 w-5" />
                      <span>Financial Details</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Total Cost</Label>
                        <p class="text-lg font-bold text-gray-900">{{ formatCurrency(booking.total_cost) }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Payment Status</Label>
                        <Badge :class="paymentStatusColors[booking.payment_status]">
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
                        <Label class="text-sm font-medium text-gray-500">Balance</Label>
                        <p class="text-base font-medium text-red-600">{{ formatCurrency(booking.balance_amount) }}</p>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Additional Information -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <FileText class="h-5 w-5" />
                      <span>Additional Information</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div v-if="booking.special_requirements">
                      <Label class="text-sm font-medium text-gray-500">Special Requirements</Label>
                      <p class="text-base">{{ booking.special_requirements }}</p>
                    </div>

                    <div v-if="booking.remarks">
                      <Label class="text-sm font-medium text-gray-500">Remarks</Label>
                      <p class="text-base">{{ booking.remarks }}</p>
                    </div>

                    <Separator />

                    <div class="grid grid-cols-1 gap-2 text-sm text-gray-500">
                      <div class="flex justify-between">
                        <span>Created by:</span>
                        <span>{{ booking.creator.name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span>Created on:</span>
                        <span>{{ formatDateTime(booking.created_at) }}</span>
                      </div>
                      <div v-if="booking.updater" class="flex justify-between">
                        <span>Updated by:</span>
                        <span>{{ booking.updater.name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span>Last updated:</span>
                        <span>{{ formatDateTime(booking.updated_at) }}</span>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
