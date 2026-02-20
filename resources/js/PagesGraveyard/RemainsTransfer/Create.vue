<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Calendar, CheckCircle, MapPin, User, Users } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { DateInput } from '@/components/ui/date-input';
import { formatDateForDisplay } from '@/lib/utils';

interface ValidMember {
  id: number;
  full_name: string;
  first_name: string;
  last_name: string;
  relationship: string;
  member_type: string;
  is_deceased: boolean;
  death_date?: string;
  burial_date?: string;
}

interface EligibleBooking {
  id: number;
  booking_reference: string;
  full_name: string;
  grave_no: string;
  buried_on: string;
  expected_transfer_date: string;
  is_overdue: boolean;
  temporary_grave: {
    grave_no: string;
    section: string;
    row_no: string;
  };
}

interface AvailableNiche {
  id: number;
  niche_no: string;
  section: string;
  row_no: string;
  location?: string;
  owner_name?: string;
  last_occupation_date?: string;
  cost: number;
  valid_members?: ValidMember[];
  has_valid_members?: boolean;
}

interface AvailablePermanentGrave {
  id: number;
  block: string;
  row: number;
  column: number;
  owner_name: string;
  valid_members?: ValidMember[];
  has_valid_members?: boolean;
}

interface Relationship {
  id: number;
  name: string;
  description?: string;
}

interface Props {
  eligibleBookings: EligibleBooking[];
  availableNiches: AvailableNiche[];
  availablePermanentGraves: AvailablePermanentGrave[];
  relationships: Relationship[];
  selectedBooking?: EligibleBooking;
}

const props = defineProps<Props>();

const form = useForm({
  from_booking_id: props.selectedBooking?.id || null,
  transfer_type: '' as '' | 'niche' | 'permanent_grave' | 'removal',
  to_niche_id: null as number | null,
  to_permanent_grave_id: null as number | null,
  proposed_transfer_date: '',
  transfer_reason: '',
  applicant_name: '',
  contact_no: '',
  contact_email: '',
  applicant_address: '',
  relationship_id: null as number | null,
});

// Pre-select booking if provided
const selectedBooking = ref<EligibleBooking | null>(props.selectedBooking || null);
const selectedNiche = ref<AvailableNiche | null>(null);
const availableValidMembers = ref<ValidMember[]>([]);
const selectedValidMember = ref<ValidMember | null>(null);

// Niche search functionality
const nicheSearch = ref('');
const showNicheDropdown = ref(false);

const filteredNiches = computed(() => {
  if (!nicheSearch.value) return props.availableNiches;

  const search = nicheSearch.value.toLowerCase();
  return props.availableNiches.filter(
    (niche) =>
      String(niche.niche_no ?? '').toLowerCase().includes(search) ||
      String(niche.owner_name ?? '').toLowerCase().includes(search) ||
      String(niche.location ?? '').toLowerCase().includes(search),
  );
});

const selectNiche = (niche: AvailableNiche) => {
  selectedNiche.value = niche;
  form.to_niche_id = niche.id;
  nicheSearch.value = `${niche.niche_no} - ${niche.location || 'No location'}`;
  showNicheDropdown.value = false;
  availableValidMembers.value = niche.valid_members || [];
  selectedValidMember.value = null;
};

const clearNicheSelection = () => {
  selectedNiche.value = null;
  form.to_niche_id = null;
  nicheSearch.value = '';
  showNicheDropdown.value = false;
  availableValidMembers.value = [];
  selectedValidMember.value = null;
};

const selectValidMember = (member: ValidMember) => {
  selectedValidMember.value = member;
};

const navigateToAddValidMember = () => {
  if (selectedNiche.value) {
    const url = route('graveyard.valid-members.create') + '?niche_id=' + selectedNiche.value.id;
    window.location.href = url;
  }
};

// Permanent grave search functionality
const permanentGraveSearch = ref('');
const showPermanentGraveDropdown = ref(false);
const selectedPermanentGrave = ref<AvailablePermanentGrave | null>(null);

const filteredPermanentGraves = computed(() => {
  if (!permanentGraveSearch.value) return props.availablePermanentGraves;

  const search = permanentGraveSearch.value.toLowerCase();
  return props.availablePermanentGraves.filter(
    (grave) =>
      grave.block.toLowerCase().includes(search) ||
      String(grave.row).includes(search) ||
      String(grave.column).includes(search) ||
      (grave.owner_name && grave.owner_name.toLowerCase().includes(search)),
  );
});

const permanentGraveValidMembers = ref<ValidMember[]>([]);
const selectedPermanentGraveValidMember = ref<ValidMember | null>(null);

const selectPermanentGrave = (grave: AvailablePermanentGrave) => {
  selectedPermanentGrave.value = grave;
  form.to_permanent_grave_id = grave.id;
  permanentGraveSearch.value = `Block ${grave.block}, Row ${grave.row}, Col ${grave.column}`;
  showPermanentGraveDropdown.value = false;
  permanentGraveValidMembers.value = grave.valid_members || [];
  selectedPermanentGraveValidMember.value = null;
};

const clearPermanentGraveSelection = () => {
  selectedPermanentGrave.value = null;
  form.to_permanent_grave_id = null;
  permanentGraveSearch.value = '';
  showPermanentGraveDropdown.value = false;
  permanentGraveValidMembers.value = [];
  selectedPermanentGraveValidMember.value = null;
};

const selectPermanentGraveValidMember = (member: ValidMember) => {
  selectedPermanentGraveValidMember.value = member;
};

const navigateToAddValidMemberForPermanentGrave = () => {
  if (selectedPermanentGrave.value) {
    window.location.href = route('graveyard.valid-members.create') + '?permanent_grave_id=' + selectedPermanentGrave.value.id;
  }
};

// Clear destination selections when transfer type changes
watch(
  () => form.transfer_type,
  () => {
    clearNicheSelection();
    clearPermanentGraveSelection();
  },
);

// Watch for booking selection
watch(
  () => form.from_booking_id,
  (newBookingId) => {
    selectedBooking.value = props.eligibleBookings.find((b) => b.id === newBookingId) || null;
  },
);

// Set minimum date to tomorrow
onMounted(() => {
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);

  // Set default transfer date to 1 month from now
  const defaultDate = new Date();
  defaultDate.setMonth(defaultDate.getMonth() + 1);
  form.proposed_transfer_date = defaultDate.toISOString().split('T')[0];

  // Close dropdowns when clicking outside
  document.addEventListener('click', (event) => {
    const target = event.target as HTMLElement;

    if (!target.closest('[data-niche-dropdown]')) {
      showNicheDropdown.value = false;
    }

    if (!target.closest('[data-permanent-grave-dropdown]')) {
      showPermanentGraveDropdown.value = false;
    }
  });
});

const submit = () => {
  form.post(route('graveyard.remains-transfers.store'));
};
</script>

<template>
  <Head title="Request Remains Transfer" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center space-x-3">
            <Button variant="outline" size="sm" as-child>
              <Link :href="route('graveyard.remains-transfers.index')">
                <ArrowLeft class="h-4 w-4" />
              </Link>
            </Button>
            <div>
              <h3 class="text-2xl font-bold text-blue-700">Request Remains Transfer</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Transfer remains from temporary grave to chosen destination</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
          <!-- Main Form Content -->
          <div class="lg:col-span-3">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
              <form @submit.prevent="submit" class="space-y-6 px-4 py-5 sm:p-6">
                <!-- Booking Selection -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <MapPin class="h-5 w-5" />
                      <span>Select Temporary Grave Booking</span>
                    </CardTitle>
                    <CardDescription> Choose the confirmed booking to transfer from temporary grave </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div>
                      <Label for="from_booking_id">Eligible Graves *</Label>
                      <select
                        v-model="form.from_booking_id"
                        :disabled="!!props.selectedBooking"
                        :class="[
                          'mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500',
                          form.errors.from_booking_id && 'border-red-500',
                        ]"
                      >
                        <option value="" disabled>Select a booking</option>
                        <option v-for="booking in eligibleBookings" :key="booking.id" :value="booking.id">
                          {{ booking.full_name }} ({{ booking.grave_no }}) - Buried: {{ formatDateForDisplay(booking.buried_on)
                          }}{{ booking.is_overdue ? ' - Overdue' : '' }}
                        </option>
                      </select>
                      <div v-if="form.errors.from_booking_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.from_booking_id }}
                      </div>

                      <!-- Selected Booking Details -->
                      <div v-if="selectedBooking" class="mt-3 rounded-lg border border-blue-200 bg-blue-50 p-3">
                        <div class="flex items-start space-x-3">
                          <CheckCircle class="mt-0.5 h-5 w-5 text-blue-600" />
                          <div>
                            <h4 class="font-medium text-blue-900">{{ selectedBooking.full_name }}</h4>
                            <p class="text-sm text-blue-700">
                              From: {{ selectedBooking.temporary_grave.grave_no }} ({{ selectedBooking.temporary_grave.section }}, Row
                              {{ selectedBooking.temporary_grave.row_no }})
                            </p>
                            <p class="text-sm text-blue-600">
                              Buried: {{ formatDateForDisplay(selectedBooking.buried_on) }} • Transfer due:
                              {{ formatDateForDisplay(selectedBooking.expected_transfer_date) }}
                            </p>
                            <Badge v-if="selectedBooking.is_overdue" class="mt-1 bg-red-100 text-xs text-red-800"> Transfer Overdue </Badge>
                          </div>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Transfer Destination -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <ArrowRight class="h-5 w-5" />
                      <span>Transfer Destination</span>
                    </CardTitle>
                    <CardDescription> Select how and where the remains should be transferred </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <!-- Transfer Type Radio Buttons -->
                    <div>
                      <Label class="text-base font-medium">Transfer Type *</Label>
                      <div class="mt-3 space-y-3">
                        <label class="flex cursor-pointer items-start space-x-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50"
                          :class="form.transfer_type === 'niche' ? 'border-blue-400 bg-blue-50' : ''"
                        >
                          <input
                            v-model="form.transfer_type"
                            type="radio"
                            value="niche"
                            class="mt-0.5 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                          />
                          <div>
                            <div class="text-sm font-medium text-gray-900">Transfer to Niche</div>
                            <div class="text-xs text-gray-500">Remains will be placed in a permanent niche</div>
                          </div>
                        </label>

                        <label class="flex cursor-pointer items-start space-x-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50"
                          :class="form.transfer_type === 'permanent_grave' ? 'border-blue-400 bg-blue-50' : ''"
                        >
                          <input
                            v-model="form.transfer_type"
                            type="radio"
                            value="permanent_grave"
                            class="mt-0.5 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                          />
                          <div>
                            <div class="text-sm font-medium text-gray-900">Transfer to Permanent Grave</div>
                            <div class="text-xs text-gray-500">Remains will be transferred to an available permanent grave</div>
                          </div>
                        </label>

                        <label class="flex cursor-pointer items-start space-x-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50"
                          :class="form.transfer_type === 'removal' ? 'border-blue-400 bg-blue-50' : ''"
                        >
                          <input
                            v-model="form.transfer_type"
                            type="radio"
                            value="removal"
                            class="mt-0.5 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                          />
                          <div>
                            <div class="text-sm font-medium text-gray-900">Remove Bones</div>
                            <div class="text-xs text-gray-500">Remains will be removed with no on-site destination</div>
                          </div>
                        </label>
                      </div>
                      <div v-if="form.errors.transfer_type" class="mt-1 text-sm text-red-600">
                        {{ form.errors.transfer_type }}
                      </div>
                    </div>

                    <!-- Niche Search (shown when transfer_type === 'niche') -->
                    <div v-if="form.transfer_type === 'niche'" class="mt-5">
                      <Label for="to_niche_id">Available Niches *</Label>
                      <div class="relative mt-1" data-niche-dropdown>
                        <Input
                          v-model="nicheSearch"
                          placeholder="Search by niche number, owner name, or location..."
                          :class="form.errors.to_niche_id && 'border-red-500'"
                          @focus="showNicheDropdown = true"
                          @input="showNicheDropdown = true"
                        />
                        <div
                          v-if="showNicheDropdown && filteredNiches.length > 0"
                          class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-md border border-gray-300 bg-white shadow-lg"
                        >
                          <div
                            v-for="niche in filteredNiches"
                            :key="niche.id"
                            class="cursor-pointer border-b border-gray-100 px-4 py-2 last:border-b-0 hover:bg-gray-100"
                            @click.stop="selectNiche(niche)"
                          >
                            <div class="font-medium text-gray-900">Niche {{ niche.niche_no }}</div>
                            <div class="text-sm text-gray-600">Location: {{ niche.location || 'No location specified' }}</div>
                            <div v-if="niche.owner_name" class="text-sm text-gray-500">Owner: {{ niche.owner_name }}</div>
                            <div v-if="niche.last_occupation_date" class="text-xs text-gray-400">
                              Last occupied: {{ formatDateForDisplay(niche.last_occupation_date) }}
                            </div>
                          </div>
                        </div>
                        <div
                          v-if="showNicheDropdown && filteredNiches.length === 0 && nicheSearch"
                          class="absolute z-10 mt-1 w-full rounded-md border border-gray-300 bg-white p-4 text-center text-gray-500 shadow-lg"
                        >
                          No niches found matching your search
                        </div>
                      </div>
                      <div v-if="selectedNiche" class="mt-2">
                        <Button type="button" variant="outline" size="sm" @click="clearNicheSelection"> Clear Selection </Button>
                      </div>
                      <div v-if="form.errors.to_niche_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.to_niche_id }}
                      </div>

                      <!-- Selected Niche Details -->
                      <div v-if="selectedNiche" class="mt-3 rounded-lg border border-green-200 bg-green-50 p-3">
                        <div class="flex items-start space-x-3">
                          <CheckCircle class="mt-0.5 h-5 w-5 text-green-600" />
                          <div>
                            <h4 class="font-medium text-green-900">Niche {{ selectedNiche.niche_no }}</h4>
                            <p class="text-sm text-green-700">
                              Location: {{ selectedNiche.location || `${selectedNiche.section}, Row ${selectedNiche.row_no}` }}
                            </p>
                            <p v-if="selectedNiche.owner_name" class="text-sm text-green-600">Owner: {{ selectedNiche.owner_name }}</p>
                            <p v-if="selectedNiche.last_occupation_date" class="text-xs text-green-500">
                              Last occupied: {{ formatDateForDisplay(selectedNiche.last_occupation_date) }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Permanent Grave Search (shown when transfer_type === 'permanent_grave') -->
                    <div v-if="form.transfer_type === 'permanent_grave'" class="mt-5">
                      <Label for="to_permanent_grave_id">Available Permanent Graves *</Label>
                      <div class="relative mt-1" data-permanent-grave-dropdown>
                        <Input
                          v-model="permanentGraveSearch"
                          placeholder="Search by grave number, section, or owner name..."
                          :class="form.errors.to_permanent_grave_id && 'border-red-500'"
                          @focus="showPermanentGraveDropdown = true"
                          @input="showPermanentGraveDropdown = true"
                        />
                        <div
                          v-if="showPermanentGraveDropdown && filteredPermanentGraves.length > 0"
                          class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-md border border-gray-300 bg-white shadow-lg"
                        >
                          <div
                            v-for="grave in filteredPermanentGraves"
                            :key="grave.id"
                            class="cursor-pointer border-b border-gray-100 px-4 py-2 last:border-b-0 hover:bg-gray-100"
                            @click.stop="selectPermanentGrave(grave)"
                          >
                            <div class="font-medium text-gray-900">Block {{ grave.block }}</div>
                            <div class="text-sm text-gray-600">Row {{ grave.row }}, Col {{ grave.column }}</div>
                            <div v-if="grave.owner_name" class="text-sm text-gray-500">Owner: {{ grave.owner_name }}</div>
                          </div>
                        </div>
                        <div
                          v-if="showPermanentGraveDropdown && filteredPermanentGraves.length === 0 && permanentGraveSearch"
                          class="absolute z-10 mt-1 w-full rounded-md border border-gray-300 bg-white p-4 text-center text-gray-500 shadow-lg"
                        >
                          No available permanent graves found matching your search
                        </div>
                      </div>
                      <div v-if="selectedPermanentGrave" class="mt-2">
                        <Button type="button" variant="outline" size="sm" @click="clearPermanentGraveSelection"> Clear Selection </Button>
                      </div>
                      <div v-if="form.errors.to_permanent_grave_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.to_permanent_grave_id }}
                      </div>

                      <!-- Selected Permanent Grave Details -->
                      <div v-if="selectedPermanentGrave" class="mt-3 rounded-lg border border-green-200 bg-green-50 p-3">
                        <div class="flex items-start space-x-3">
                          <CheckCircle class="mt-0.5 h-5 w-5 text-green-600" />
                          <div>
                            <h4 class="font-medium text-green-900">Block {{ selectedPermanentGrave.block }}</h4>
                            <p class="text-sm text-green-700">Row {{ selectedPermanentGrave.row }}, Col {{ selectedPermanentGrave.column }}</p>
                            <p v-if="selectedPermanentGrave.owner_name" class="text-sm text-green-600">Owner: {{ selectedPermanentGrave.owner_name }}</p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Removal Info Banner (shown when transfer_type === 'removal') -->
                    <div v-if="form.transfer_type === 'removal'" class="mt-5 rounded-lg border border-orange-200 bg-orange-50 p-4">
                      <div class="flex items-start space-x-3">
                        <MapPin class="mt-0.5 h-5 w-5 text-orange-600" />
                        <div>
                          <h4 class="font-medium text-orange-900">No destination required</h4>
                          <p class="mt-1 text-sm text-orange-700">
                            The remains will be removed from the temporary grave with no on-site destination. Only the transfer details and applicant
                            information are needed.
                          </p>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Transfer Details -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <Calendar class="h-5 w-5" />
                      <span>Transfer Details</span>
                    </CardTitle>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div>
                      <Label for="proposed_transfer_date">Proposed Transfer Date *</Label>
                      <DateInput
                        id="proposed_transfer_date"
                        v-model="form.proposed_transfer_date"
                        class="mt-1 w-full"
                      />
                      <div v-if="form.errors.proposed_transfer_date" class="mt-1 text-sm text-red-600">
                        {{ form.errors.proposed_transfer_date }}
                      </div>
                      <p class="mt-1 text-sm text-gray-500">The date when the transfer should take place</p>
                    </div>

                    <div>
                      <Label for="transfer_reason">Reason for Transfer</Label>
                      <Textarea
                        id="transfer_reason"
                        v-model="form.transfer_reason"
                        :rows="3"
                        placeholder="Optional: Provide the reason for requesting this transfer..."
                        :class="form.errors.transfer_reason && 'border-red-500'"
                        class="mt-1"
                      />
                      <div v-if="form.errors.transfer_reason" class="mt-1 text-sm text-red-600">
                        {{ form.errors.transfer_reason }}
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Applicant Details -->
                <Card>
                  <CardHeader>
                    <CardTitle class="flex items-center space-x-2">
                      <User class="h-5 w-5" />
                      <span>Transfer Applicant Details</span>
                    </CardTitle>
                    <CardDescription> Details of the person requesting the transfer </CardDescription>
                  </CardHeader>
                  <CardContent class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                      <div>
                        <Label for="applicant_name">Applicant Name{{ form.transfer_type !== 'removal' ? ' *' : '' }}</Label>
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
                        <Label for="contact_no">Contact Number{{ form.transfer_type !== 'removal' ? ' *' : '' }}</Label>
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
                        <Label for="relationship_id">Relationship to Deceased{{ form.transfer_type !== 'removal' ? ' *' : '' }}</Label>
                        <select
                          id="relationship_id"
                          v-model="form.relationship_id"
                          :class="[
                            'mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500',
                            form.errors.relationship_id && 'border-red-500',
                          ]"
                        >
                          <option value="" disabled>Select relationship</option>
                          <option v-for="relationship in relationships" :key="relationship.id" :value="relationship.id">
                            {{ relationship.name }}
                          </option>
                        </select>
                        <div v-if="form.errors.relationship_id" class="mt-1 text-sm text-red-600">
                          {{ form.errors.relationship_id }}
                        </div>
                      </div>

                      <div class="sm:col-span-2">
                        <Label for="applicant_address">Applicant Address</Label>
                        <Textarea id="applicant_address" v-model="form.applicant_address" :rows="2" class="mt-1" />
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-5">
                  <Button variant="outline" as-child>
                    <Link :href="route('graveyard.remains-transfers.index')"> Cancel </Link>
                  </Button>
                  <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating Request...' : 'Create Transfer Request' }}
                  </Button>
                </div>
              </form>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <div class="sticky top-6">
              <!-- Valid Members Section (only shown for niche transfers) -->
              <template v-if="form.transfer_type === 'niche'">
                <!-- Selection Prompt -->
                <div v-if="!selectedNiche" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                  <div class="p-8 text-center">
                    <MapPin class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                    <h3 class="mb-2 text-lg font-medium text-gray-900">Select a Niche First</h3>
                    <p class="text-sm text-gray-500">Choose a niche from the form above to see valid members.</p>
                  </div>
                </div>

                <!-- Valid Members Section -->
                <div v-if="selectedNiche && availableValidMembers.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                  <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="flex items-center text-sm font-medium text-gray-900">
                      <Users class="mr-2 h-4 w-4 text-blue-600" />
                      Valid Members
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">{{ availableValidMembers.length }} members available for this niche</p>
                  </div>
                  <div class="p-4">
                    <div class="mb-4">
                      <Button type="button" @click="navigateToAddValidMember" class="w-full text-xs" variant="outline">
                        <Users class="mr-2 h-3 w-3" />
                        Add New Person to This Niche
                      </Button>
                    </div>

                    <div class="max-h-64 space-y-3 overflow-y-auto">
                      <div
                        v-for="member in availableValidMembers"
                        :key="member.id"
                        @click="selectValidMember(member)"
                        :class="[
                          'cursor-pointer rounded-lg border p-3 transition-colors',
                          selectedValidMember?.id === member.id
                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200'
                            : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50'
                        ]"
                      >
                        <div class="space-y-2">
                          <div class="flex items-start justify-between">
                            <div class="flex-1">
                              <div class="flex items-center gap-2">
                                <h4 class="text-sm font-medium text-gray-900">{{ member.full_name }}</h4>
                                <Badge v-if="member.is_deceased" variant="destructive" class="text-xs"> Deceased </Badge>
                                <Badge v-else variant="outline" class="border-green-200 bg-green-50 text-xs text-green-700"> Living </Badge>
                              </div>
                              <div class="mt-1 space-y-1">
                                <p class="text-xs text-gray-600"><span class="font-medium">Relationship:</span> {{ member.relationship }}</p>
                                <p v-if="member.member_type" class="text-xs text-gray-600">
                                  <span class="font-medium">Type:</span> {{ member.member_type }}
                                </p>
                                <div v-if="member.is_deceased && (member.death_date || member.burial_date)" class="space-y-1 text-xs text-gray-500">
                                  <p v-if="member.death_date"><span class="font-medium">Death Date:</span> {{ formatDateForDisplay(member.death_date) }}</p>
                                  <p v-if="member.burial_date"><span class="font-medium">Burial Date:</span> {{ formatDateForDisplay(member.burial_date) }}</p>
                                </div>
                              </div>
                            </div>
                            <div v-if="selectedValidMember?.id === member.id" class="text-blue-600">
                              <CheckCircle class="h-5 w-5" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Empty State for Valid Members -->
                <div v-else-if="selectedNiche" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                  <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="flex items-center text-sm font-medium text-gray-900">
                      <Users class="mr-2 h-4 w-4 text-blue-600" />
                      Valid Members
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">This niche has no valid members yet</p>
                  </div>
                  <div class="p-4">
                    <div class="mb-4">
                      <Button type="button" @click="navigateToAddValidMember" class="w-full text-xs" variant="outline">
                        <Users class="mr-2 h-3 w-3" />
                        Add New Person to This Niche
                      </Button>
                    </div>
                    <div class="py-4 text-center">
                      <Users class="mx-auto mb-2 h-8 w-8 text-gray-400" />
                      <p class="text-xs text-gray-500">Click the button above to add the first valid member for this niche.</p>
                    </div>
                  </div>
                </div>

                <!-- Selected Valid Member Summary -->
                <div v-if="selectedValidMember" class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                  <div class="bg-green-50 px-4 py-3 border-b border-green-200">
                    <h3 class="text-sm font-medium text-green-900 flex items-center">
                      <CheckCircle class="h-4 w-4 mr-2 text-green-600" />
                      Selected Member
                    </h3>
                  </div>
                  <div class="p-4">
                    <div class="text-sm font-medium text-gray-900 mb-2">{{ selectedValidMember.full_name }}</div>
                    <div class="text-xs space-y-1">
                      <div class="text-gray-600">
                        <span class="font-medium">Relationship:</span> {{ selectedValidMember.relationship }}
                      </div>
                      <div v-if="selectedValidMember.member_type" class="text-gray-600">
                        <span class="font-medium">Type:</span> {{ selectedValidMember.member_type }}
                      </div>
                      <div class="text-gray-600">
                        <span class="font-medium">Status:</span>
                        <Badge v-if="selectedValidMember.is_deceased" variant="destructive" class="text-xs ml-1"> Deceased </Badge>
                        <Badge v-else variant="outline" class="border-green-200 bg-green-50 text-xs text-green-700 ml-1"> Living </Badge>
                      </div>
                      <div v-if="selectedValidMember.is_deceased && (selectedValidMember.death_date || selectedValidMember.burial_date)" class="space-y-1 text-gray-500">
                        <div v-if="selectedValidMember.death_date">
                          <span class="font-medium">Death Date:</span> {{ formatDateForDisplay(selectedValidMember.death_date) }}
                        </div>
                        <div v-if="selectedValidMember.burial_date">
                          <span class="font-medium">Burial Date:</span> {{ formatDateForDisplay(selectedValidMember.burial_date) }}
                        </div>
                      </div>
                    </div>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="selectedValidMember = null"
                      class="mt-3 w-full text-xs"
                    >
                      Clear Selection
                    </Button>
                  </div>
                </div>
              </template>

              <!-- Sidebar for permanent_grave type -->
              <template v-else-if="form.transfer_type === 'permanent_grave'">
                <div v-if="!selectedPermanentGrave" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                  <div class="p-8 text-center">
                    <MapPin class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                    <h3 class="mb-2 text-lg font-medium text-gray-900">Select a Permanent Grave First</h3>
                    <p class="text-sm text-gray-500">Choose a permanent grave from the form above to see valid members.</p>
                  </div>
                </div>

                <div v-if="selectedPermanentGrave && permanentGraveValidMembers.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                  <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="flex items-center text-sm font-medium text-gray-900">
                      <Users class="mr-2 h-4 w-4 text-blue-600" />
                      Valid Members
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">{{ permanentGraveValidMembers.length }} members for this grave</p>
                  </div>
                  <div class="p-4">
                    <div class="mb-4">
                      <Button type="button" @click="navigateToAddValidMemberForPermanentGrave" class="w-full text-xs" variant="outline">
                        <Users class="mr-2 h-3 w-3" />
                        Add New Person to This Grave
                      </Button>
                    </div>
                    <div class="max-h-64 space-y-3 overflow-y-auto">
                      <div
                        v-for="member in permanentGraveValidMembers"
                        :key="member.id"
                        @click="selectPermanentGraveValidMember(member)"
                        :class="[
                          'cursor-pointer rounded-lg border p-3 transition-colors',
                          selectedPermanentGraveValidMember?.id === member.id
                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200'
                            : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50',
                        ]"
                      >
                        <div class="flex items-start justify-between">
                          <div class="flex-1">
                            <div class="flex items-center gap-2">
                              <h4 class="text-sm font-medium text-gray-900">{{ member.full_name }}</h4>
                              <Badge v-if="member.is_deceased" variant="destructive" class="text-xs">Deceased</Badge>
                              <Badge v-else variant="outline" class="border-green-200 bg-green-50 text-xs text-green-700">Living</Badge>
                            </div>
                            <div class="mt-1 space-y-1">
                              <p class="text-xs text-gray-600"><span class="font-medium">Relationship:</span> {{ member.relationship }}</p>
                              <p v-if="member.member_type" class="text-xs text-gray-600"><span class="font-medium">Type:</span> {{ member.member_type }}</p>
                              <div v-if="member.is_deceased && (member.death_date || member.burial_date)" class="space-y-1 text-xs text-gray-500">
                                <p v-if="member.death_date"><span class="font-medium">Death Date:</span> {{ formatDateForDisplay(member.death_date) }}</p>
                                <p v-if="member.burial_date"><span class="font-medium">Burial Date:</span> {{ formatDateForDisplay(member.burial_date) }}</p>
                              </div>
                            </div>
                          </div>
                          <div v-if="selectedPermanentGraveValidMember?.id === member.id" class="text-blue-600">
                            <CheckCircle class="h-5 w-5" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else-if="selectedPermanentGrave" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                  <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="flex items-center text-sm font-medium text-gray-900">
                      <Users class="mr-2 h-4 w-4 text-blue-600" />
                      Valid Members
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">This grave has no valid members yet</p>
                  </div>
                  <div class="p-4">
                    <div class="mb-4">
                      <Button type="button" @click="navigateToAddValidMemberForPermanentGrave" class="w-full text-xs" variant="outline">
                        <Users class="mr-2 h-3 w-3" />
                        Add New Person to This Grave
                      </Button>
                    </div>
                    <div class="py-4 text-center">
                      <Users class="mx-auto mb-2 h-8 w-8 text-gray-400" />
                      <p class="text-xs text-gray-500">Click the button above to add the first valid member for this grave.</p>
                    </div>
                  </div>
                </div>

                <div v-if="selectedPermanentGraveValidMember" class="mt-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                  <div class="border-b border-green-200 bg-green-50 px-4 py-3">
                    <h3 class="flex items-center text-sm font-medium text-green-900">
                      <CheckCircle class="mr-2 h-4 w-4 text-green-600" />
                      Selected Member
                    </h3>
                  </div>
                  <div class="p-4">
                    <div class="mb-2 text-sm font-medium text-gray-900">{{ selectedPermanentGraveValidMember.full_name }}</div>
                    <div class="space-y-1 text-xs">
                      <div class="text-gray-600"><span class="font-medium">Relationship:</span> {{ selectedPermanentGraveValidMember.relationship }}</div>
                      <div v-if="selectedPermanentGraveValidMember.member_type" class="text-gray-600">
                        <span class="font-medium">Type:</span> {{ selectedPermanentGraveValidMember.member_type }}
                      </div>
                      <div class="text-gray-600">
                        <span class="font-medium">Status:</span>
                        <Badge v-if="selectedPermanentGraveValidMember.is_deceased" variant="destructive" class="ml-1 text-xs">Deceased</Badge>
                        <Badge v-else variant="outline" class="ml-1 border-green-200 bg-green-50 text-xs text-green-700">Living</Badge>
                      </div>
                      <div v-if="selectedPermanentGraveValidMember.is_deceased && (selectedPermanentGraveValidMember.death_date || selectedPermanentGraveValidMember.burial_date)" class="space-y-1 text-gray-500">
                        <div v-if="selectedPermanentGraveValidMember.death_date">
                          <span class="font-medium">Death Date:</span> {{ formatDateForDisplay(selectedPermanentGraveValidMember.death_date) }}
                        </div>
                        <div v-if="selectedPermanentGraveValidMember.burial_date">
                          <span class="font-medium">Burial Date:</span> {{ formatDateForDisplay(selectedPermanentGraveValidMember.burial_date) }}
                        </div>
                      </div>
                    </div>
                    <Button type="button" variant="outline" size="sm" @click="selectedPermanentGraveValidMember = null" class="mt-3 w-full text-xs">
                      Clear Selection
                    </Button>
                  </div>
                </div>
              </template>

              <!-- Sidebar for removal type -->
              <div v-else-if="form.transfer_type === 'removal'" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                  <ArrowRight class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                  <h3 class="mb-2 text-lg font-medium text-gray-900">No Destination</h3>
                  <p class="text-sm text-gray-500">Remains will be removed with no on-site destination.</p>
                </div>
              </div>

              <!-- Sidebar placeholder for no type selected -->
              <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                  <ArrowRight class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                  <h3 class="mb-2 text-lg font-medium text-gray-900">Select Transfer Type</h3>
                  <p class="text-sm text-gray-500">Choose a transfer destination type from the form to continue.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
