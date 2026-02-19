<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Calendar, CheckCircle, MapPin, User } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { DateInput } from '@/components/ui/date-input';
import { formatDateForDisplay } from '@/lib/utils';

interface NicheOption {
  id: number;
  niche_no: string;
  section: string;
  row_no: string;
  location?: string;
  owner_name?: string;
  last_occupation_date?: string;
  cost?: number;
}

interface PermanentGraveOption {
  id: number;
  grave_no: string;
  section: string;
  row_no: string;
  owner_name?: string;
}

interface Relationship {
  id: number;
  name: string;
}

interface RemainsTransfer {
  id: number;
  transfer_reference: string;
  status: string;
  transfer_type: 'niche' | 'permanent_grave' | 'removal';
  from_temporary_grave: {
    grave_no: string;
    section: string;
    row_no: string;
  };
  from_booking: {
    booking_reference: string;
    dead_first_name: string;
    dead_last_name: string;
    buried_on: string;
  };
  to_niche?: {
    id: number;
    niche_no: string;
    section: string;
    row_no: string;
    location?: string;
    owner_name?: string;
  };
  to_permanent_grave?: {
    id: number;
    grave_no: string;
    section: string;
    row_no: string;
    owner_name?: string;
  };
  proposed_transfer_date: string;
  transfer_reason?: string;
  applicant_name?: string;
  contact_no?: string;
  contact_email?: string;
  applicant_address?: string;
  relationship?: {
    id: number;
    name: string;
  };
  creator: { name: string };
  updater?: { name: string };
  created_at: string;
  updated_at: string;
}

interface Props {
  transfer: RemainsTransfer;
  relationships: Relationship[];
  availableNiches: NicheOption[];
  availablePermanentGraves: PermanentGraveOption[];
}

const props = defineProps<Props>();

const form = useForm({
  proposed_transfer_date: props.transfer.proposed_transfer_date || '',
  transfer_reason: props.transfer.transfer_reason || '',
  to_niche_id: props.transfer.to_niche?.id ?? (null as number | null),
  to_permanent_grave_id: props.transfer.to_permanent_grave?.id ?? (null as number | null),
  applicant_name: props.transfer.applicant_name || '',
  contact_no: props.transfer.contact_no || '',
  contact_email: props.transfer.contact_email || '',
  applicant_address: props.transfer.applicant_address || '',
  relationship_id: props.transfer.relationship?.id ?? (null as number | null),
});

// Niche search
const nicheSearch = ref(
  props.transfer.to_niche
    ? `${props.transfer.to_niche.niche_no} - ${props.transfer.to_niche.location || 'No location'}`
    : '',
);
const showNicheDropdown = ref(false);
const selectedNiche = ref<NicheOption | null>(props.transfer.to_niche ?? null);

const filteredNiches = computed(() => {
  if (!nicheSearch.value) return props.availableNiches;
  const search = nicheSearch.value.toLowerCase();
  return props.availableNiches.filter(
    (niche) =>
      niche.niche_no.toLowerCase().includes(search) ||
      (niche.owner_name && niche.owner_name.toLowerCase().includes(search)) ||
      (niche.location && niche.location.toLowerCase().includes(search)),
  );
});

const selectNiche = (niche: NicheOption) => {
  selectedNiche.value = niche;
  form.to_niche_id = niche.id;
  nicheSearch.value = `${niche.niche_no} - ${niche.location || 'No location'}`;
  showNicheDropdown.value = false;
};

const clearNicheSelection = () => {
  selectedNiche.value = null;
  form.to_niche_id = null;
  nicheSearch.value = '';
  showNicheDropdown.value = false;
};

// Permanent grave search
const permanentGraveSearch = ref(
  props.transfer.to_permanent_grave
    ? `${props.transfer.to_permanent_grave.grave_no} - ${props.transfer.to_permanent_grave.section}, Row ${props.transfer.to_permanent_grave.row_no}`
    : '',
);
const showPermanentGraveDropdown = ref(false);
const selectedPermanentGrave = ref<PermanentGraveOption | null>(props.transfer.to_permanent_grave ?? null);

const filteredPermanentGraves = computed(() => {
  if (!permanentGraveSearch.value) return props.availablePermanentGraves;
  const search = permanentGraveSearch.value.toLowerCase();
  return props.availablePermanentGraves.filter(
    (grave) =>
      grave.grave_no.toLowerCase().includes(search) ||
      grave.section.toLowerCase().includes(search) ||
      (grave.owner_name && grave.owner_name.toLowerCase().includes(search)),
  );
});

const selectPermanentGrave = (grave: PermanentGraveOption) => {
  selectedPermanentGrave.value = grave;
  form.to_permanent_grave_id = grave.id;
  permanentGraveSearch.value = `${grave.grave_no} - ${grave.section}, Row ${grave.row_no}`;
  showPermanentGraveDropdown.value = false;
};

const clearPermanentGraveSelection = () => {
  selectedPermanentGrave.value = null;
  form.to_permanent_grave_id = null;
  permanentGraveSearch.value = '';
  showPermanentGraveDropdown.value = false;
};

const transferTypeLabel: Record<string, string> = {
  niche: 'Transfer to Niche',
  permanent_grave: 'Transfer to Permanent Grave',
  removal: 'Remove Bones',
};

const statusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-blue-100 text-blue-800',
  rejected: 'bg-red-100 text-red-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-gray-100 text-gray-800',
};

onMounted(() => {
  document.addEventListener('click', (event) => {
    const target = event.target as HTMLElement;
    if (!target.closest('[data-niche-dropdown]')) showNicheDropdown.value = false;
    if (!target.closest('[data-permanent-grave-dropdown]')) showPermanentGraveDropdown.value = false;
  });
});

const submit = () => {
  form.put(route('graveyard.remains-transfers.update', props.transfer.id));
};
</script>

<template>
  <Head :title="`Edit Transfer #${transfer.transfer_reference}`" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <Button variant="outline" size="sm" as-child>
                <Link :href="route('graveyard.remains-transfers.index')">
                  <ArrowLeft class="h-4 w-4" />
                </Link>
              </Button>
              <div>
                <h3 class="text-2xl font-bold text-blue-700">Edit Transfer #{{ transfer.transfer_reference }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Update transfer request details</p>
              </div>
            </div>
            <Badge :class="statusColors[transfer.status]" class="capitalize">{{ transfer.status }}</Badge>
          </div>
        </div>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="space-y-6 px-4 py-5 sm:p-6">
            <!-- Booking Info (read-only) -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <MapPin class="h-5 w-5" />
                  <span>Temporary Grave Booking</span>
                </CardTitle>
                <CardDescription>Source booking details (read-only)</CardDescription>
              </CardHeader>
              <CardContent>
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                  <div class="flex items-start space-x-3">
                    <CheckCircle class="mt-0.5 h-5 w-5 text-blue-600" />
                    <div>
                      <h4 class="font-medium text-blue-900">
                        {{ transfer.from_booking.dead_first_name }} {{ transfer.from_booking.dead_last_name }}
                      </h4>
                      <p class="text-sm text-blue-700">
                        From: {{ transfer.from_temporary_grave.grave_no }} ({{ transfer.from_temporary_grave.section }}, Row
                        {{ transfer.from_temporary_grave.row_no }})
                      </p>
                      <p class="text-sm text-blue-600">Buried: {{ formatDateForDisplay(transfer.from_booking.buried_on) }}</p>
                      <p class="mt-1 text-xs text-blue-500">Booking ref: #{{ transfer.from_booking.booking_reference }}</p>
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
                <CardDescription>Transfer type is fixed; destination can be updated</CardDescription>
              </CardHeader>
              <CardContent class="space-y-4">
                <!-- Transfer type badge (read-only) -->
                <div>
                  <Label class="text-sm font-medium text-gray-500">Transfer Type</Label>
                  <div class="mt-1">
                    <Badge class="bg-blue-100 text-blue-800">{{ transferTypeLabel[transfer.transfer_type] }}</Badge>
                  </div>
                </div>

                <!-- Niche search -->
                <div v-if="transfer.transfer_type === 'niche'">
                  <Label for="to_niche_id">Destination Niche *</Label>
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
                    <Button type="button" variant="outline" size="sm" @click="clearNicheSelection">Clear Selection</Button>
                  </div>
                  <div v-if="form.errors.to_niche_id" class="mt-1 text-sm text-red-600">{{ form.errors.to_niche_id }}</div>

                  <div v-if="selectedNiche" class="mt-3 rounded-lg border border-green-200 bg-green-50 p-3">
                    <div class="flex items-start space-x-3">
                      <CheckCircle class="mt-0.5 h-5 w-5 text-green-600" />
                      <div>
                        <h4 class="font-medium text-green-900">Niche {{ selectedNiche.niche_no }}</h4>
                        <p class="text-sm text-green-700">
                          Location: {{ selectedNiche.location || `${selectedNiche.section}, Row ${selectedNiche.row_no}` }}
                        </p>
                        <p v-if="selectedNiche.owner_name" class="text-sm text-green-600">Owner: {{ selectedNiche.owner_name }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Permanent grave search -->
                <div v-if="transfer.transfer_type === 'permanent_grave'">
                  <Label for="to_permanent_grave_id">Destination Permanent Grave *</Label>
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
                        <div class="font-medium text-gray-900">{{ grave.grave_no }}</div>
                        <div class="text-sm text-gray-600">{{ grave.section }}, Row {{ grave.row_no }}</div>
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
                    <Button type="button" variant="outline" size="sm" @click="clearPermanentGraveSelection">Clear Selection</Button>
                  </div>
                  <div v-if="form.errors.to_permanent_grave_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.to_permanent_grave_id }}
                  </div>

                  <div v-if="selectedPermanentGrave" class="mt-3 rounded-lg border border-green-200 bg-green-50 p-3">
                    <div class="flex items-start space-x-3">
                      <CheckCircle class="mt-0.5 h-5 w-5 text-green-600" />
                      <div>
                        <h4 class="font-medium text-green-900">{{ selectedPermanentGrave.grave_no }}</h4>
                        <p class="text-sm text-green-700">{{ selectedPermanentGrave.section }}, Row {{ selectedPermanentGrave.row_no }}</p>
                        <p v-if="selectedPermanentGrave.owner_name" class="text-sm text-green-600">Owner: {{ selectedPermanentGrave.owner_name }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Removal info banner -->
                <div v-if="transfer.transfer_type === 'removal'" class="rounded-lg border border-orange-200 bg-orange-50 p-4">
                  <div class="flex items-start space-x-3">
                    <MapPin class="mt-0.5 h-5 w-5 text-orange-600" />
                    <div>
                      <h4 class="font-medium text-orange-900">No destination required</h4>
                      <p class="mt-1 text-sm text-orange-700">
                        The remains will be removed from the temporary grave with no on-site destination.
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
                  <DateInput id="proposed_transfer_date" v-model="form.proposed_transfer_date" class="mt-1 w-full" />
                  <div v-if="form.errors.proposed_transfer_date" class="mt-1 text-sm text-red-600">
                    {{ form.errors.proposed_transfer_date }}
                  </div>
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
                <CardDescription>Details of the person requesting the transfer</CardDescription>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label for="applicant_name">Applicant Name{{ transfer.transfer_type !== 'removal' ? ' *' : '' }}</Label>
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
                    <Label for="contact_no">Contact Number{{ transfer.transfer_type !== 'removal' ? ' *' : '' }}</Label>
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
                    <Label for="relationship_id">Relationship to Deceased{{ transfer.transfer_type !== 'removal' ? ' *' : '' }}</Label>
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
            <div class="flex items-center justify-between pt-2">
              <Button variant="outline" as-child>
                <Link :href="route('graveyard.remains-transfers.index')">Cancel</Link>
              </Button>
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
