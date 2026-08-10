<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { DateInput } from '@/components/ui/date-input';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDateForDisplay } from '@/lib/utils';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, CheckCircle, MapPin, Phone, Users } from 'lucide-vue-next';

interface ValidMember {
  id: number;
  full_name: string;
  first_name: string;
  last_name: string;
  relationship: { name: string };
  member_type: string;
  is_deceased: boolean;
  death_date?: string;
  burial_date?: string;
}

interface PermanentGrave {
  id: number;
  block: string;
  row: number;
  column: number;
  owner_name: string;
  last_burial_date?: string;
  is_eligible: boolean;
  eligibility_message: string;
  pending_maintenance_fee: number;
  has_pending_maintenance: boolean;
  valid_members: ValidMember[];
  has_valid_members: boolean;
}

interface Booking {
  id: number;
  booking_reference: string;
  status: 'pending' | 'confirmed' | 'cancelled';
  valid_member_id: number | null;
  died_on: string;
  buried_on: string;
  cause_of_death: string;
  minister?: string;
  applicant_type: 'member' | 'external';
  applicant_name: string;
  contact_no: string;
  contact_email?: string;
  permit_no?: string;
  special_requirements?: string;
}

interface Props {
  booking: Booking;
  grave: PermanentGrave;
}

const props = defineProps<Props>();
const { success, error } = useToast();

const form = useForm({
  valid_member_id: props.booking.valid_member_id as number | null,
  died_on: props.booking.died_on || '',
  buried_on: props.booking.buried_on || '',
  cause_of_death: props.booking.cause_of_death || '',
  minister: props.booking.minister || '',
  applicant_type: props.booking.applicant_type || ('external' as 'member' | 'external'),
  applicant_name: props.booking.applicant_name || '',
  contact_no: props.booking.contact_no || '',
  contact_email: props.booking.contact_email || '',
  permit_no: props.booking.permit_no || '',
  special_requirements: props.booking.special_requirements || '',
});

const selectValidMember = (member: ValidMember) => {
  form.valid_member_id = member.id;
  form.clearErrors('valid_member_id');
};

const navigateToAddValidMember = () => {
  window.location.href = route('graveyard.valid-members.create') + '?permanent_grave_id=' + props.grave.id;
};

const openMaintenancePayment = () => {
  window.open(`/graveyard/payments/create/maintenance/${props.grave.id}`, '_blank');
};

const submit = () => {
  form.put(route('graveyard.permanent-grave-bookings.update', props.booking.id), {
    onSuccess: () => {
      success('Permanent grave booking updated successfully!');
    },
    onError: () => {
      error('Failed to update booking. Please check the form and try again.');
    },
  });
};
</script>

<template>
  <Head :title="`Edit Booking #${booking.booking_reference}`" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center space-x-3">
              <Button variant="outline" size="sm" as-child>
                <Link :href="route('graveyard.permanent-grave-bookings.index')">
                  <ArrowLeft class="h-4 w-4" />
                </Link>
              </Button>
              <div>
                <h3 class="text-base leading-6 font-semibold text-gray-900">Edit Booking #{{ booking.booking_reference }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Update permanent grave booking details</p>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <!-- Left Column - Main Form -->
              <div class="space-y-6 lg:col-span-2">
                <!-- Grave (read-only) -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <MapPin class="h-5 w-5" />
                      <span>Permanent Grave</span>
                    </CardTitle>
                    <CardDescription>Grave cannot be changed after booking is created</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                      <div class="flex items-start space-x-3">
                        <CheckCircle class="mt-0.5 h-5 w-5 text-blue-600" />
                        <div>
                          <h4 class="font-medium text-blue-900">Block {{ grave.block }}, Row {{ grave.row }}, Col {{ grave.column }}</h4>
                          <p class="text-sm text-blue-700">Owner: {{ grave.owner_name }}</p>
                          <div class="mt-2">
                            <Badge :class="grave.is_eligible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                              {{ grave.eligibility_message }}
                            </Badge>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Pending Maintenance Fee Warning -->
                    <div v-if="grave.has_pending_maintenance" class="mt-3 rounded-lg border border-amber-300 bg-amber-50 p-4">
                      <div class="flex items-start space-x-3">
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                          <path
                            fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <div class="flex-1">
                          <h4 class="font-medium text-amber-900">Pending Maintenance Fees</h4>
                          <p class="mt-1 text-sm text-amber-800">
                            This grave has pending maintenance fees of
                            <span class="font-semibold"
                              >₹{{
                                grave.pending_maintenance_fee.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                              }}</span
                            >.
                          </p>
                          <div class="mt-3">
                            <Button
                              type="button"
                              variant="outline"
                              size="sm"
                              class="border-amber-400 bg-white text-amber-900 hover:bg-amber-100"
                              @click="openMaintenancePayment"
                            >
                              Pay Pending Maintenance Fees
                            </Button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Death & Burial Details -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <Calendar class="h-5 w-5" />
                      <span>Death & Burial Details</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                      <Label for="died_on">Date of Death *</Label>
                      <DateInput id="died_on" v-model="form.died_on" class="mt-1 w-full" />
                      <div v-if="form.errors.died_on" class="mt-1 text-sm text-red-600">{{ form.errors.died_on }}</div>
                    </div>

                    <div>
                      <Label for="buried_on">Date of Burial *</Label>
                      <DateInput id="buried_on" v-model="form.buried_on" class="mt-1 w-full" />
                      <div v-if="form.errors.buried_on" class="mt-1 text-sm text-red-600">{{ form.errors.buried_on }}</div>
                    </div>

                    <div class="sm:col-span-2">
                      <Label for="cause_of_death">Cause of Death *</Label>
                      <Input id="cause_of_death" v-model="form.cause_of_death" :class="form.errors.cause_of_death && 'border-red-500'" class="mt-1" />
                      <div v-if="form.errors.cause_of_death" class="mt-1 text-sm text-red-600">{{ form.errors.cause_of_death }}</div>
                    </div>

                    <div class="sm:col-span-2">
                      <Label for="minister">Minister</Label>
                      <Input id="minister" v-model="form.minister" class="mt-1" />
                    </div>
                  </CardContent>
                </Card>

                <!-- Applicant Details -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <Phone class="h-5 w-5" />
                      <span>Applicant Details</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label for="applicant_type">Applicant Type *</Label>
                      <select
                        id="applicant_type"
                        v-model="form.applicant_type"
                        class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                      >
                        <option value="member">Church Member</option>
                        <option value="external">Non-Member</option>
                      </select>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                      <div>
                        <Label for="applicant_name">Applicant Name *</Label>
                        <Input
                          id="applicant_name"
                          v-model="form.applicant_name"
                          :class="form.errors.applicant_name && 'border-red-500'"
                          class="mt-1"
                        />
                        <div v-if="form.errors.applicant_name" class="mt-1 text-sm text-red-600">{{ form.errors.applicant_name }}</div>
                      </div>

                      <div>
                        <Label for="contact_no">Contact Number *</Label>
                        <Input id="contact_no" v-model="form.contact_no" :class="form.errors.contact_no && 'border-red-500'" class="mt-1" />
                        <div v-if="form.errors.contact_no" class="mt-1 text-sm text-red-600">{{ form.errors.contact_no }}</div>
                      </div>

                      <div>
                        <Label for="contact_email">Email</Label>
                        <Input id="contact_email" v-model="form.contact_email" type="email" class="mt-1" />
                      </div>

                      <div>
                        <Label for="permit_no">BMC Permit Number</Label>
                        <Input id="permit_no" v-model="form.permit_no" class="mt-1" />
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Additional Information -->
                <Card>
                  <CardHeader>
                    <CardTitle>Additional Information</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <Label for="special_requirements">Special Requirements</Label>
                    <Textarea
                      id="special_requirements"
                      v-model="form.special_requirements"
                      :rows="3"
                      placeholder="Any special requirements or notes..."
                      class="mt-1"
                    />
                  </CardContent>
                </Card>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-5">
                  <Button variant="outline" as-child>
                    <Link :href="route('graveyard.permanent-grave-bookings.index')">Cancel</Link>
                  </Button>
                  <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                  </Button>
                </div>
              </div>

              <!-- Right Sidebar - Valid Members -->
              <div class="lg:col-span-1">
                <div class="sticky top-6">
                  <Card v-if="grave.has_valid_members">
                    <CardHeader>
                      <CardTitle class="flex items-center space-x-2">
                        <Users class="h-5 w-5" />
                        <span>Valid Members</span>
                      </CardTitle>
                      <CardDescription>Select the deceased person</CardDescription>
                    </CardHeader>
                    <CardContent>
                      <div class="mb-4">
                        <Button type="button" @click="navigateToAddValidMember" class="w-full" variant="outline">
                          <Users class="mr-2 h-4 w-4" />
                          Add New Person to This Grave
                        </Button>
                      </div>

                      <div class="space-y-3">
                        <div
                          v-for="member in grave.valid_members"
                          :key="member.id"
                          :class="[
                            'cursor-pointer rounded-lg border p-3 transition-colors',
                            form.valid_member_id === member.id
                              ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200'
                              : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50',
                          ]"
                          @click="selectValidMember(member)"
                        >
                          <div class="flex items-start justify-between">
                            <div class="flex-1">
                              <div class="flex items-center gap-2">
                                <h4 class="font-medium text-gray-900">{{ member.full_name }}</h4>
                                <Badge v-if="member.is_deceased" variant="destructive" class="text-xs">Deceased</Badge>
                              </div>
                              <div class="mt-1 space-y-1">
                                <p class="text-sm text-gray-600"><span class="font-medium">Relationship:</span> {{ member.relationship.name }}</p>
                                <p v-if="member.member_type" class="text-sm text-gray-600">
                                  <span class="font-medium">Type:</span> {{ member.member_type }}
                                </p>
                                <div v-if="member.is_deceased && (member.death_date || member.burial_date)" class="space-y-1 text-xs text-gray-500">
                                  <p v-if="member.death_date">
                                    <span class="font-medium">Death Date:</span> {{ formatDateForDisplay(member.death_date) }}
                                  </p>
                                  <p v-if="member.burial_date">
                                    <span class="font-medium">Burial Date:</span> {{ formatDateForDisplay(member.burial_date) }}
                                  </p>
                                </div>
                              </div>
                            </div>
                            <CheckCircle v-if="form.valid_member_id === member.id" class="h-5 w-5 text-blue-600" />
                          </div>
                        </div>
                      </div>

                      <div v-if="form.errors.valid_member_id" class="mt-3 text-sm text-red-600">
                        {{ form.errors.valid_member_id }}
                      </div>
                    </CardContent>
                  </Card>

                  <!-- No valid members yet -->
                  <Card v-else>
                    <CardHeader>
                      <CardTitle class="flex items-center space-x-2">
                        <Users class="h-5 w-5" />
                        <span>Valid Members</span>
                      </CardTitle>
                      <CardDescription>This grave has no valid members yet</CardDescription>
                    </CardHeader>
                    <CardContent>
                      <Button type="button" @click="navigateToAddValidMember" class="w-full" variant="outline">
                        <Users class="mr-2 h-4 w-4" />
                        Add New Person to This Grave
                      </Button>
                    </CardContent>
                  </Card>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
