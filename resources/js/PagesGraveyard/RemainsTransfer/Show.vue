<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Calendar, CreditCard, FileText, IndianRupee, User } from 'lucide-vue-next';

interface RemainsTransfer {
  id: number;
  transfer_reference: string;
  status: 'pending' | 'completed';
  from_temporary_grave: {
    id: number;
    grave_no: string;
    section: string;
    row_no: string;
    owner_name?: string;
  };
  from_booking: {
    id: number;
    booking_reference: string;
    dead_first_name: string;
    dead_last_name: string;
    buried_on: string;
    applicant_name: string;
    contact_no: string;
    total_cost: number;
    paid_amount: number;
    balance_amount: number;
    payment_status?: 'pending' | 'partial' | 'completed';
    temporary_grave: {
      grave_no: string;
      section: string;
      row_no: string;
    };
  };
  to_niche: {
    id: number;
    niche_no: string;
    section: string;
    row_no: string;
    location?: string;
    owner_name?: string;
  };
  proposed_transfer_date: string;
  actual_transfer_date?: string;
  transfer_reason: string;
  applicant_name: string;
  contact_no: string;
  contact_email?: string;
  applicant_address?: string;
  relationship?: {
    id: number;
    name: string;
  };
  niche_cost: number;
  transfer_cost: number;
  total_cost: number;
  paid_amount: number;
  balance_amount: number;
  payment_status?: 'pending' | 'partial' | 'completed';
  created_at: string;
  updated_at: string;
  completed_at?: string;
  creator: {
    name: string;
  };
  updater?: {
    name: string;
  };
}

interface Props {
  transfer: RemainsTransfer;
}

const props = defineProps<Props>();

// const showRejectDialog = ref(false);
// const showCancelDialog = ref(false);
// const rejectionReason = ref('');
// const cancellationReason = ref('');
// const isProcessing = ref(false);

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-blue-100 text-blue-800',
  rejected: 'bg-red-100 text-red-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-gray-100 text-gray-800',
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
  return `${props.transfer.from_booking.dead_first_name} ${props.transfer.from_booking.dead_last_name}`;
};

const isOverdue = () => {
  return new Date(props.transfer.proposed_transfer_date) < new Date();
};

const canMakePayment = () => {
  return props.transfer.status === 'pending' && props.transfer.payment_status !== 'completed';
};

// const canBeApproved = () => {
//   return props.transfer.status === 'pending';
// };

// const canBeRejected = () => {
//   return props.transfer.status === 'pending';
// };

// const canBeCompleted = () => {
//   return props.transfer.status === 'approved';
// };

// const canBeCancelled = () => {
//   return ['pending', 'approved'].includes(props.transfer.status);
// };
</script>

<template>
  <Head :title="`Transfer #${transfer.transfer_reference}`" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <Button variant="outline" size="sm" as-child>
                  <Link :href="route('graveyard.remains-transfers.index')">
                    <ArrowLeft class="h-4 w-4" />
                  </Link>
                </Button>
                <div>
                  <h3 class="text-base leading-6 font-semibold text-gray-900">Transfer #{{ transfer.transfer_reference }}</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Niche transfer request details</p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <Badge :class="statusColors[transfer.status]">
                  {{ transfer.status }}
                </Badge>
                <Badge v-if="isOverdue()" class="bg-red-100 text-red-800"> Overdue </Badge>
                <div class="flex space-x-2">
                  <!-- Make Payment Button -->
                  <Button v-if="canMakePayment()" as-child class="bg-green-600 hover:bg-green-700">
                    <Link :href="route('graveyard.payments.create', ['remains-transfer', transfer.id])">
                      <CreditCard class="mr-2 h-4 w-4" />
                      Make Payment
                    </Link>
                  </Button>
                </div>
                <!-- <div class="flex space-x-2">
                  <Dialog v-if="canBeApproved()" v-model:open="showApproveDialog">
                    <DialogTrigger as-child>
                      <Button class="bg-green-600 hover:bg-green-700">
                        <ThumbsUp class="mr-2 h-4 w-4" />
                        Approve
                      </Button>
                    </DialogTrigger>
                    <DialogContent>
                      <DialogHeader>
                        <DialogTitle>Approve Transfer Request</DialogTitle>
                        <DialogDescription>
                          Approve this transfer request to allow the deceased to be moved from temporary grave to niche.
                        </DialogDescription>
                      </DialogHeader>
                      <div class="py-4">
                        <Label for="admin-notes">Admin Notes</Label>
                        <Textarea id="admin-notes" v-model="adminNotes" placeholder="Add any notes for approval..." rows="3" class="mt-1" />
                      </div>
                      <DialogFooter>
                        <Button variant="outline" @click="showApproveDialog = false"> Cancel </Button>
                        <Button
                          @click="approveTransfer"
                          :disabled="adminNotes.trim().length === 0 || isProcessing"
                          class="bg-green-600 hover:bg-green-700"
                        >
                          {{ isProcessing ? 'Approving...' : 'Approve Transfer' }}
                        </Button>
                      </DialogFooter>
                    </DialogContent>
                  </Dialog>

                  <Dialog v-if="canBeRejected()" v-model:open="showRejectDialog">
                    <DialogTrigger as-child>
                      <Button variant="outline" class="border-red-200 text-red-600 hover:bg-red-50">
                        <ThumbsDown class="mr-2 h-4 w-4" />
                        Reject
                      </Button>
                    </DialogTrigger>
                    <DialogContent>
                      <DialogHeader>
                        <DialogTitle>Reject Transfer Request</DialogTitle>
                        <DialogDescription> Reject this transfer request. Please provide a clear reason for rejection. </DialogDescription>
                      </DialogHeader>
                      <div class="py-4">
                        <Label for="rejection-reason">Rejection Reason *</Label>
                        <Textarea
                          id="rejection-reason"
                          v-model="rejectionReason"
                          placeholder="Please provide a clear reason for rejection..."
                          rows="3"
                          class="mt-1"
                        />
                      </div>
                      <DialogFooter>
                        <Button variant="outline" @click="showRejectDialog = false"> Cancel </Button>
                        <Button variant="destructive" @click="rejectTransfer" :disabled="rejectionReason.trim().length === 0 || isProcessing">
                          {{ isProcessing ? 'Rejecting...' : 'Reject Transfer' }}
                        </Button>
                      </DialogFooter>
                    </DialogContent>
                  </Dialog>

                  <Button v-if="canBeCompleted()" @click="completeTransfer" class="bg-blue-600 hover:bg-blue-700">
                    <CheckCircle class="mr-2 h-4 w-4" />
                    Complete
                  </Button>

                  <Dialog v-if="canBeCancelled()" v-model:open="showCancelDialog">
                    <DialogTrigger as-child>
                      <Button variant="outline" class="border-gray-200 text-gray-600 hover:bg-gray-50">
                        <XCircle class="mr-2 h-4 w-4" />
                        Cancel
                      </Button>
                    </DialogTrigger>
                    <DialogContent>
                      <DialogHeader>
                        <DialogTitle>Cancel Transfer</DialogTitle>
                        <DialogDescription> Are you sure you want to cancel this transfer request? This action cannot be undone. </DialogDescription>
                      </DialogHeader>
                      <div class="py-4">
                        <Label for="cancel-reason">Cancellation Reason *</Label>
                        <Textarea
                          id="cancel-reason"
                          v-model="cancellationReason"
                          placeholder="Please provide a reason for cancellation..."
                          rows="3"
                          class="mt-1"
                        />
                      </div>
                      <DialogFooter>
                        <Button variant="outline" @click="showCancelDialog = false"> Keep Transfer </Button>
                        <Button variant="destructive" @click="cancelTransfer" :disabled="cancellationReason.trim().length === 0 || isProcessing">
                          {{ isProcessing ? 'Cancelling...' : 'Cancel Transfer' }}
                        </Button>
                      </DialogFooter>
                    </DialogContent>
                  </Dialog>
                </div> -->
              </div>
            </div>
          </div>

          <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <!-- Left Column -->
              <div class="space-y-6">
                <!-- Transfer Overview -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <ArrowRight class="h-5 w-5" />
                      <span>Transfer Overview</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                      <!-- From: Temporary Grave Details -->
                      <div class="rounded-lg border border-gray-200 p-4">
                        <Label class="mb-3 block text-sm font-medium text-gray-500">From (Temporary Grave)</Label>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                          <div>
                            <span class="font-medium text-gray-700">Section:</span>
                            <span class="ml-1 text-gray-900">{{ transfer.from_temporary_grave.section }}</span>
                          </div>
                          <div>
                            <span class="font-medium text-gray-700">Row No:</span>
                            <span class="ml-1 text-gray-900">{{ transfer.from_temporary_grave.row_no }}</span>
                          </div>
                          <div>
                            <span class="font-medium text-gray-700">Grave No:</span>
                            <span class="ml-1 font-semibold text-gray-900">{{ transfer.from_temporary_grave.grave_no }}</span>
                          </div>
                          <div v-if="transfer.from_temporary_grave.owner_name">
                            <span class="font-medium text-gray-700">Owner Name:</span>
                            <span class="ml-1 text-gray-900">{{ transfer.from_temporary_grave.owner_name }}</span>
                          </div>
                        </div>
                      </div>

                      <!-- Arrow -->
                      <div class="flex justify-center">
                        <div class="flex items-center space-x-2">
                          <div class="h-px w-8 bg-gray-300"></div>
                          <ArrowRight class="h-6 w-6 text-gray-400" />
                          <div class="h-px w-8 bg-gray-300"></div>
                        </div>
                      </div>

                      <!-- To: Niche Details -->
                      <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                        <Label class="mb-3 block text-sm font-medium text-gray-500">To (Niche)</Label>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                          <div>
                            <span class="font-medium text-gray-700">Niche No:</span>
                            <span class="ml-1 font-semibold text-gray-900">{{ transfer.to_niche.niche_no }}</span>
                          </div>
                          <div v-if="transfer.to_niche.location">
                            <span class="font-medium text-gray-700">Location:</span>
                            <span class="ml-1 text-gray-900">{{ transfer.to_niche.location }}</span>
                          </div>
                          <div>
                            <span class="font-medium text-gray-700">Section:</span>
                            <span class="ml-1 text-gray-900">{{ transfer.to_niche.section }}</span>
                          </div>
                          <div>
                            <span class="font-medium text-gray-700">Row No:</span>
                            <span class="ml-1 text-gray-900">{{ transfer.to_niche.row_no }}</span>
                          </div>
                          <div v-if="transfer.to_niche.owner_name" class="col-span-2">
                            <span class="font-medium text-gray-700">Owner Name:</span>
                            <span class="ml-1 text-gray-900">{{ transfer.to_niche.owner_name }}</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <Separator />

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Deceased Person</Label>
                      <p class="text-base font-medium">{{ getDeceasedName() }}</p>
                      <p class="text-sm text-gray-600">Original Booking: #{{ transfer.from_booking.booking_reference }}</p>
                      <p class="text-sm text-gray-600">Buried: {{ formatDate(transfer.from_booking.buried_on) }}</p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Transfer Reason</Label>
                      <p class="text-base">{{ transfer.transfer_reason }}</p>
                    </div>
                  </CardContent>
                </Card>

                <!-- Transfer Dates -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <Calendar class="h-5 w-5" />
                      <span>Transfer Timeline</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label class="text-sm font-medium text-gray-500">Proposed Transfer Date</Label>
                      <div class="flex items-center space-x-2">
                        <p class="text-base font-medium">{{ formatDate(transfer.proposed_transfer_date) }}</p>
                        <Badge v-if="isOverdue()" class="bg-red-100 text-xs text-red-800"> Overdue </Badge>
                      </div>
                    </div>

                    <div v-if="transfer.actual_transfer_date">
                      <Label class="text-sm font-medium text-gray-500">Actual Transfer Date</Label>
                      <p class="text-base font-medium text-green-600">{{ formatDate(transfer.actual_transfer_date) }}</p>
                    </div>

                    <!-- <div v-if="transfer.approved_at">
                      <Label class="text-sm font-medium text-gray-500">Approved On</Label>
                      <p class="text-base">{{ formatDateTime(transfer.approved_at) }}</p>
                      <p v-if="transfer.approver" class="text-sm text-gray-600">by {{ transfer.approver.name }}</p>
                    </div>

                    <div v-if="transfer.rejected_at">
                      <Label class="text-sm font-medium text-gray-500">Rejected On</Label>
                      <p class="text-base">{{ formatDateTime(transfer.rejected_at) }}</p>
                      <p v-if="transfer.rejecter" class="text-sm text-gray-600">by {{ transfer.rejecter.name }}</p>
                    </div> -->

                    <div v-if="transfer.completed_at">
                      <Label class="text-sm font-medium text-gray-500">Completed On</Label>
                      <p class="text-base text-green-600">{{ formatDateTime(transfer.completed_at) }}</p>
                    </div>
                  </CardContent>
                </Card>
              </div>

              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Applicant Information -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <User class="h-5 w-5" />
                      <span>Transfer Applicant</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label class="text-sm font-medium text-gray-500">Applicant Name</Label>
                      <p class="text-base font-medium">{{ transfer.applicant_name }}</p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Contact Number</Label>
                      <p class="text-base">{{ transfer.contact_no }}</p>
                    </div>

                    <div v-if="transfer.contact_email">
                      <Label class="text-sm font-medium text-gray-500">Email</Label>
                      <p class="text-base">{{ transfer.contact_email }}</p>
                    </div>

                    <div>
                      <Label class="text-sm font-medium text-gray-500">Relationship to Deceased</Label>
                      <p class="text-base">{{ transfer.relationship?.name }}</p>
                    </div>

                    <div v-if="transfer.applicant_address">
                      <Label class="text-sm font-medium text-gray-500">Address</Label>
                      <p class="text-base">{{ transfer.applicant_address }}</p>
                    </div>
                  </CardContent>
                </Card>

                <!-- Financial Information -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <IndianRupee class="h-5 w-5" />
                      <span>Financial Details</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Total Cost </Label>
                        <p class="text-lg font-bold text-gray-900">{{ formatCurrency(transfer.total_cost) }}</p>
                      </div>
                      <div>
                        <Label class="text-sm font-medium text-gray-500">Balance</Label>
                        <p class="text-lg font-bold text-red-600">{{ formatCurrency(transfer.balance_amount) }}</p>
                      </div>

                      <div v-if="transfer.paid_amount > 0">
                        <Label class="text-sm font-medium text-gray-500">Paid Amount</Label>
                        <p class="text-base font-medium text-green-600">{{ formatCurrency(transfer.paid_amount) }}</p>
                      </div>

                      <div v-if="transfer.payment_status">
                        <Label class="text-sm font-medium text-gray-500">Payment Status</Label>
                        <div class="mt-1">
                          <Badge
                            :class="{
                              'bg-yellow-100 text-yellow-800': transfer.payment_status === 'pending',
                              'bg-blue-100 text-blue-800': transfer.payment_status === 'partial',
                              'bg-green-100 text-green-800': transfer.payment_status === 'completed',
                            }"
                          >
                            {{ transfer.payment_status }}
                          </Badge>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Admin Notes & Status -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <FileText class="h-5 w-5" />
                      <span>Administrative Notes</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <!-- <div v-if="transfer.admin_notes">
                      <Label class="text-sm font-medium text-gray-500">Admin Notes</Label>
                      <p class="text-base">{{ transfer.admin_notes }}</p>
                    </div>

                    <div v-if="transfer.rejection_reason">
                      <Label class="text-sm font-medium text-gray-500">Rejection Reason</Label>
                      <p class="text-base text-red-700">{{ transfer.rejection_reason }}</p>
                    </div>

                    <Separator /> -->

                    <div class="grid grid-cols-1 gap-2 text-sm text-gray-500">
                      <div class="flex justify-between">
                        <span>Created by:</span>
                        <span>{{ transfer.creator.name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span>Created on:</span>
                        <span>{{ formatDateTime(transfer.created_at) }}</span>
                      </div>
                      <div v-if="transfer.updater" class="flex justify-between">
                        <span>Updated by:</span>
                        <span>{{ transfer.updater.name }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span>Last updated:</span>
                        <span>{{ formatDateTime(transfer.updated_at) }}</span>
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
