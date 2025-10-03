<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, CheckCircle, MapPin, Phone, Search, Users } from 'lucide-vue-next';
import { ref } from 'vue';

interface ValidMember {
  id: number;
  full_name: string;
  first_name: string;
  last_name: string;
  relationship: {
    name: string;
  };
  member_type: string;
  is_deceased: boolean;
  death_date?: string;
  burial_date?: string;
}

interface PermanentGrave {
  id: number;
  grave_no: string;
  section: string;
  row_no: string;
  contact_no?: string;
  owner_name: string;
  last_burial_date?: string;
  is_eligible: boolean;
  eligibility_message: string;
  pending_maintenance_fee: number;
  has_pending_maintenance: boolean;
  valid_members: ValidMember[];
  has_valid_members: boolean;
  available_members: ValidMember[];
  member?: {
    full_name: string;
    family_no: string;
  };
}

interface Props {
  // No props needed for this component
}

const props = defineProps<Props>();

const { success, error } = useToast();

const form = useForm({
  permanent_grave_id: null as number | null,
  valid_member_id: null as number | null,
  died_on: '',
  buried_on: '',
  cause_of_death: '',
  minister: '',
  applicant_type: 'external' as 'member' | 'external',
  applicant_name: '',
  contact_no: '',
  contact_email: '',
  permit_no: '',
  special_requirements: '',
});

// Grave search
const graveSearchTerm = ref('');
const searchResults = ref<PermanentGrave[]>([]);
const isSearching = ref(false);
const selectedGrave = ref<PermanentGrave | null>(null);
const availableValidMembers = ref<ValidMember[]>([]);

// No longer needed - modal functionality removed

const searchGraves = async () => {
  if (graveSearchTerm.value.length < 2) {
    searchResults.value = [];
    return;
  }

  isSearching.value = true;
  try {
    const response = await fetch(route('graveyard.permanent-grave-bookings.search-permanent-grave'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        search_term: graveSearchTerm.value,
      }),
    });

    const data = await response.json();
    searchResults.value = data.graves;
  } catch (err) {
    console.error('Search failed:', err);
    error('Failed to search graves. Please try again.');
  } finally {
    isSearching.value = false;
  }
};

const selectGrave = (grave: PermanentGrave) => {
  selectedGrave.value = grave;
  form.permanent_grave_id = grave.id;
  form.valid_member_id = null; // Reset member selection
  availableValidMembers.value = grave.valid_members || [];
  searchResults.value = [];
  graveSearchTerm.value = '';
};

const clearGraveSelection = () => {
  selectedGrave.value = null;
  form.permanent_grave_id = null;
  form.valid_member_id = null;
  availableValidMembers.value = [];
};

const selectValidMember = (member: ValidMember) => {
  form.valid_member_id = member.id;
  // Clear validation errors when a member is selected
  form.clearErrors('valid_member_id');
};

const navigateToAddValidMember = () => {
  if (selectedGrave.value) {
    // Navigate to ValidMember create page with permanent grave pre-selected
    const url = route('graveyard.valid-members.create') + '?permanent_grave_id=' + selectedGrave.value.id;
    window.location.href = url;
  }
};

// Modal functions removed - now redirecting to ValidMember/Create.vue

// Add a function to refresh grave data
// const refreshGraveData = async () => {
//   if (!selectedGrave.value) return;

//   try {
//     const response = await fetch(route('graveyard.permanent-grave-bookings.search-permanent-grave'), {
//       method: 'POST',
//       headers: {
//         'Content-Type': 'application/json',
//         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
//       },
//       body: JSON.stringify({
//         search_term: selectedGrave.value.grave_no,
//       }),
//     });

//     const data = await response.json();
//     const updatedGrave = data.graves?.find((g: any) => g.id === selectedGrave.value?.id);

//     if (updatedGrave) {
//       availableValidMembers.value = updatedGrave.valid_members || [];
//     }
//   } catch (error) {
//     console.error('Failed to refresh grave data:', error);
//   }
// };

// Debounced search
let searchTimeout: number;
const debouncedSearchGraves = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    searchGraves();
  }, 300);
};

// Calculate total cost
// const formatCurrency = (amount: number) => {
//   return new Intl.NumberFormat('en-IN', {
//     style: 'currency',
//     currency: 'INR',
//   })
//     .format(amount)
//     .replace('₹', '₹ ');
// };

const formatDate = (date: string) => {
  return date ? new Date(date).toLocaleDateString('en-IN') : '';
};

const openMaintenancePayment = () => {
  if (selectedGrave.value?.id) {
    window.open(`/graveyard/payments/create/maintenance/${selectedGrave.value.id}`, '_blank');
  }
};

const submit = () => {
  // Check if all required fields are filled
  if (!form.permanent_grave_id) {
    error('Please select a permanent grave');
    return;
  }

  if (!form.valid_member_id) {
    error('Please select a valid member');
    return;
  }

  if (!form.died_on || !form.buried_on || !form.cause_of_death || !form.applicant_name || !form.contact_no) {
    error('Please fill in all required fields');
    return;
  }

  form.post(route('graveyard.permanent-grave-bookings.store'), {
    onStart: () => {},
    onSuccess: (page) => {
      success('Permanent grave booking created successfully!');
    },
    onError: (errors) => {
      console.error('Booking creation error:', errors);
      error('Failed to create permanent grave booking. Please check the form and try again.');
    },
    onFinish: () => {},
  });
};
</script>

<template>
  <Head title="Book Permanent Grave" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center space-x-3">
                  <Button variant="outline" size="sm" as-child>
                    <Link :href="route('graveyard.permanent-grave-bookings.index')">
                      <ArrowLeft class="h-4 w-4" />
                    </Link>
                  </Button>
                  <div>
                    <h3 class="text-base leading-6 font-semibold text-gray-900">Book Permanent Grave</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Create a new permanent grave booking for a valid member</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="px-4 py-5 sm:p-6">
            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
              <!-- Left Column - Main Form -->
              <div class="space-y-6 lg:col-span-2">
                <!-- Grave Selection -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <MapPin class="h-5 w-5" />
                      <span>Select Permanent Grave</span>
                    </CardTitle>
                    <CardDescription> Search by owner name, contact, old number </CardDescription>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <!-- Selected Grave Display -->
                    <div v-if="selectedGrave" class="space-y-3">
                      <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                        <div class="flex items-start justify-between">
                          <div class="flex items-start space-x-3">
                            <CheckCircle class="mt-0.5 h-5 w-5 text-green-600" />
                            <div>
                              <h4 class="font-medium text-green-900">Grave {{ selectedGrave.grave_no }}</h4>
                              <p class="text-sm text-green-700">{{ selectedGrave.section }}, Row {{ selectedGrave.row_no }}</p>
                              <p class="text-sm text-green-600">Owner: {{ selectedGrave.owner_name }}</p>
                              <div class="mt-2">
                                <Badge :class="selectedGrave.is_eligible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                  {{ selectedGrave.eligibility_message }}
                                </Badge>
                              </div>
                            </div>
                          </div>
                          <Button variant="outline" size="sm" @click="clearGraveSelection"> Change </Button>
                        </div>
                      </div>

                      <!-- Pending Maintenance Fee Warning -->
                      <div v-if="selectedGrave.has_pending_maintenance" class="rounded-lg border border-amber-300 bg-amber-50 p-4">
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
                                  selectedGrave.pending_maintenance_fee.toLocaleString('en-IN', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                  })
                                }}</span
                              >
                              that need to be paid separately.
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
                    </div>

                    <!-- Grave Search -->
                    <div v-else class="space-y-4">
                      <div>
                        <Label>Search Permanent Graves</Label>
                        <div class="relative mt-1">
                          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <Search class="h-5 w-5 text-gray-400" />
                          </div>
                          <Input
                            v-model="graveSearchTerm"
                            placeholder="Search by owner name, contact, grave number, old number, or member name..."
                            class="pl-10"
                            @input="debouncedSearchGraves"
                            @keyup.enter="searchGraves"
                          />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Start typing to search (minimum 2 characters)</p>
                      </div>

                      <!-- Search Results -->
                      <div v-if="searchResults.length > 0" class="space-y-2">
                        <Label>Search Results ({{ searchResults.length }} found)</Label>
                        <!-- {{ searchResults }} -->
                        <div class="max-h-60 space-y-2 overflow-y-auto rounded-lg border p-2">
                          <div
                            v-for="grave in searchResults"
                            :key="grave.id"
                            :class="[
                              'cursor-pointer rounded-lg border p-3 transition-colors',
                              grave.is_eligible ? 'border-green-200 bg-green-50 hover:bg-green-100' : 'border-red-200 bg-red-50 hover:bg-red-100',
                            ]"
                            @click="grave.is_eligible && selectGrave(grave)"
                          >
                            <div class="flex items-start justify-between">
                              <div class="flex-1">
                                <h4 class="font-medium">
                                  Grave {{ grave.grave_no }} <span class="text-sm text-gray-500">({{ grave.section }}, Row {{ grave.row_no }})</span>
                                </h4>
                                <p class="text-sm text-gray-600">Owner: {{ grave.owner_name }}</p>
                                <p v-if="grave.member" class="text-sm text-blue-600">
                                  Member: {{ grave.member.full_name }} ({{ grave.member.family_no }})
                                </p>
                                <p v-if="grave.contact_no" class="text-sm text-gray-500">Contact: {{ grave.contact_no }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                  <Badge :class="grave.is_eligible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    {{ grave.is_eligible ? 'Eligible' : 'Not Eligible' }}
                                  </Badge>
                                  <Badge v-if="grave.has_valid_members" class="bg-blue-100 text-blue-800">
                                    {{ grave.valid_members.filter((member) => !member.is_deceased).length }} Valid Members
                                  </Badge>
                                  <Badge v-if="grave.has_pending_maintenance" class="bg-amber-100 text-amber-800">
                                    Pending: ₹{{ grave.pending_maintenance_fee.toLocaleString('en-IN', { minimumFractionDigits: 2 }) }}
                                  </Badge>
                                </div>
                              </div>
                              <!-- <Button v-if="grave.is_eligible" variant="outline" size="sm" @click.stop="selectGrave(grave)"> Select </Button> -->
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- No Results -->
                      <div v-if="graveSearchTerm.length >= 2 && !isSearching && searchResults.length === 0" class="py-4 text-center text-gray-500">
                        <p>No eligible permanent graves found for "{{ graveSearchTerm }}"</p>
                      </div>
                    </div>

                    <div v-if="form.errors.permanent_grave_id" class="text-sm text-red-600">
                      {{ form.errors.permanent_grave_id }}
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
                      <Input
                        id="died_on"
                        v-model="form.died_on"
                        type="date"
                        :max="new Date().toISOString().split('T')[0]"
                        :class="form.errors.died_on && 'border-red-500'"
                        class="mt-1"
                      />
                      <div v-if="form.errors.died_on" class="mt-1 text-sm text-red-600">
                        {{ form.errors.died_on }}
                      </div>
                    </div>

                    <div>
                      <Label for="buried_on">Date of Burial *</Label>
                      <Input
                        id="buried_on"
                        v-model="form.buried_on"
                        type="date"
                        :min="form.died_on"
                        :max="new Date().toISOString().split('T')[0]"
                        :class="form.errors.buried_on && 'border-red-500'"
                        class="mt-1"
                      />
                      <div v-if="form.errors.buried_on" class="mt-1 text-sm text-red-600">
                        {{ form.errors.buried_on }}
                      </div>
                      <p v-if="form.died_on" class="mt-1 text-xs text-gray-500">Must be on or after the date of death</p>
                    </div>

                    <div class="sm:col-span-2">
                      <Label for="cause_of_death">Cause of Death *</Label>
                      <Input id="cause_of_death" v-model="form.cause_of_death" :class="form.errors.cause_of_death && 'border-red-500'" class="mt-1" />
                      <div v-if="form.errors.cause_of_death" class="mt-1 text-sm text-red-600">
                        {{ form.errors.cause_of_death }}
                      </div>
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
                        <option value="">Select applicant type</option>
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
                        <div v-if="form.errors.applicant_name" class="mt-1 text-sm text-red-600">
                          {{ form.errors.applicant_name }}
                        </div>
                      </div>

                      <div>
                        <Label for="contact_no">Contact Number *</Label>
                        <Input id="contact_no" v-model="form.contact_no" :class="form.errors.contact_no && 'border-red-500'" class="mt-1" />
                        <div v-if="form.errors.contact_no" class="mt-1 text-sm text-red-600">
                          {{ form.errors.contact_no }}
                        </div>
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

                <!-- Special Requirements -->
                <Card>
                  <CardHeader>
                    <CardTitle>Additional Information</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div>
                      <Label for="special_requirements">Special Requirements</Label>
                      <Textarea
                        id="special_requirements"
                        v-model="form.special_requirements"
                        :rows="3"
                        placeholder="Any special requirements or notes..."
                        class="mt-1"
                      />
                    </div>
                  </CardContent>
                </Card>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-5">
                  <Button variant="outline" as-child>
                    <Link :href="route('graveyard.permanent-grave-bookings.index')"> Cancel </Link>
                  </Button>
                  <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating Booking...' : 'Create Booking' }}
                  </Button>
                </div>
              </div>

              <!-- Right Sidebar - Valid Members -->
              <div class="lg:col-span-1">
                <div class="sticky top-6">
                  <Card v-if="selectedGrave && availableValidMembers.length > 0">
                    <CardHeader>
                      <CardTitle class="flex items-center space-x-2">
                        <Users class="h-5 w-5" />
                        <span>Valid Members</span>
                      </CardTitle>
                      <CardDescription> Select the deceased person from {{ availableValidMembers.length }} available members </CardDescription>
                    </CardHeader>
                    <CardContent>
                      <!-- Add New Person Button -->
                      <div class="mb-4">
                        <Button type="button" @click="navigateToAddValidMember" class="w-full" variant="outline">
                          <Users class="mr-2 h-4 w-4" />
                          Add New Person to This Grave
                        </Button>
                      </div>

                      <div class="space-y-3">
                        <div
                          v-for="member in availableValidMembers"
                          :key="member.id"
                          :class="[
                            'cursor-pointer rounded-lg border p-3 transition-colors',
                            form.valid_member_id === member.id
                              ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200'
                              : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50',
                          ]"
                          @click="selectValidMember(member)"
                        >
                          <div class="space-y-2">
                            <div class="flex items-start justify-between">
                              <div class="flex-1">
                                <div class="flex items-center gap-2">
                                  <h4 class="font-medium text-gray-900">{{ member.full_name }}</h4>
                                  <Badge v-if="member.is_deceased" variant="destructive" class="text-xs"> Deceased </Badge>
                                  <!-- <Badge v-else variant="outline" class="border-green-200 bg-green-50 text-xs text-green-700"> Living </Badge> -->
                                </div>
                                <div class="mt-1 space-y-1">
                                  <p class="text-sm text-gray-600"><span class="font-medium">Relationship:</span> {{ member.relationship.name }}</p>
                                  <p v-if="member.member_type" class="text-sm text-gray-600">
                                    <span class="font-medium">Type:</span> {{ member.member_type }}
                                  </p>
                                  <div v-if="member.is_deceased && (member.death_date || member.burial_date)" class="space-y-1 text-xs text-gray-500">
                                    <p v-if="member.death_date"><span class="font-medium">Death Date:</span> {{ formatDate(member.death_date) }}</p>
                                    <p v-if="member.burial_date">
                                      <span class="font-medium">Burial Date:</span> {{ formatDate(member.burial_date) }}
                                    </p>
                                  </div>
                                </div>
                              </div>
                              <div v-if="form.valid_member_id === member.id" class="text-blue-600">
                                <CheckCircle class="h-5 w-5" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div v-if="form.errors.valid_member_id" class="mt-3 text-sm text-red-600">
                        {{ form.errors.valid_member_id }}
                      </div>
                    </CardContent>
                  </Card>

                  <!-- Empty State -->
                  <Card v-else-if="selectedGrave">
                    <CardHeader>
                      <CardTitle class="flex items-center space-x-2">
                        <Users class="h-5 w-5" />
                        <span>Valid Members</span>
                      </CardTitle>
                      <CardDescription>This grave has no valid members yet. Add the first person.</CardDescription>
                    </CardHeader>
                    <CardContent>
                      <!-- Add New Person Button -->
                      <div class="mb-4">
                        <Button type="button" @click="navigateToAddValidMember" class="w-full" variant="outline">
                          <Users class="mr-2 h-4 w-4" />
                          Add New Person to This Grave
                        </Button>
                      </div>

                      <div class="py-4 text-center">
                        <Users class="mx-auto mb-2 h-8 w-8 text-gray-400" />
                        <p class="text-sm text-gray-500">Click the button above to add the first valid member for this grave.</p>
                      </div>
                    </CardContent>
                  </Card>

                  <!-- Selection Prompt -->
                  <Card v-else>
                    <CardContent class="py-8 text-center">
                      <MapPin class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                      <h3 class="mb-2 text-lg font-medium text-gray-900">Select a Grave First</h3>
                      <p class="text-sm text-gray-500">Choose a permanent grave to see available valid members.</p>
                    </CardContent>
                  </Card>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal removed - now redirecting to ValidMember/Create.vue page -->
  </AppLayout>
</template>
