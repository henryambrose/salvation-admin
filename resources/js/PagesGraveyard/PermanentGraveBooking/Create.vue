<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, CheckCircle, MapPin, Phone, Search, User, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface ServiceType {
  id: number;
  name: string;
  cost: number;
  description?: string;
}

interface ValidMember {
  id: number;
  full_name: string;
  relationship: string;
  is_deceased: boolean;
}

interface PermanentGrave {
  id: number;
  grave_no: string;
  section: string;
  row_no: string;
  owner_name: string;
  last_burial_date?: string;
  is_eligible: boolean;
  eligibility_message: string;
  valid_members: ValidMember[];
  has_valid_members: boolean;
  available_members: ValidMember[];
}

interface Props {
  serviceTypes: ServiceType[];
}

const props = defineProps<Props>();

const form = useForm({
  permanent_grave_id: null as number | null,
  valid_member_id: null as number | null,
  died_on: '',
  buried_on: '',
  cause_of_death: '',
  minister: '',
  applicant_type: 'non_member' as 'member' | 'non_member',
  applicant_name: '',
  contact_no: '',
  contact_email: '',
  permit_no: '',
  selected_services: [] as number[],
  special_requirements: '',
});

// Grave search
const graveSearchType = ref<'owner_name' | 'grave_no'>('owner_name');
const graveSearchTerm = ref('');
const searchResults = ref<PermanentGrave[]>([]);
const isSearching = ref(false);
const selectedGrave = ref<PermanentGrave | null>(null);

// Service selection
const selectedServiceIds = ref<number[]>([]);

const searchGraves = async () => {
  if (graveSearchTerm.value.length < 2) return;

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
        search_type: graveSearchType.value,
      }),
    });

    const data = await response.json();
    searchResults.value = data.graves;
  } catch (error) {
    console.error('Search failed:', error);
  } finally {
    isSearching.value = false;
  }
};

const selectGrave = (grave: PermanentGrave) => {
  selectedGrave.value = grave;
  form.permanent_grave_id = grave.id;
  form.valid_member_id = null; // Reset member selection
  searchResults.value = [];
  graveSearchTerm.value = '';
};

const clearGraveSelection = () => {
  selectedGrave.value = null;
  form.permanent_grave_id = null;
  form.valid_member_id = null;
};

// Calculate total cost
const totalCost = computed(() => {
  return selectedServiceIds.value.reduce((total, serviceId) => {
    const service = props.serviceTypes.find((s) => s.id === serviceId);
    return total + (service?.cost || 0);
  }, 0);
});

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
  })
    .format(amount)
    .replace('₹', '₹ ');
};

const formatDate = (date: string) => {
  return date ? new Date(date).toLocaleDateString('en-IN') : '';
};

// Watch for service selection changes
watch(
  selectedServiceIds,
  (newIds) => {
    form.selected_services = newIds;
  },
  { deep: true },
);

const submit = () => {
  form.post(route('graveyard.permanent-grave-bookings.store'));
};
</script>

<template>
  <Head title="Book Permanent Grave" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
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
          <form @submit.prevent="submit" class="space-y-6 px-4 py-5 sm:p-6">
            <!-- Grave Selection -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <MapPin class="h-5 w-5" />
                  <span>Select Permanent Grave</span>
                </CardTitle>
                <CardDescription> Search and select an eligible permanent grave </CardDescription>
              </CardHeader>
              <CardContent class="space-y-4">
                <!-- Selected Grave Display -->
                <div v-if="selectedGrave" class="rounded-lg border border-green-200 bg-green-50 p-4">
                  <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                      <CheckCircle class="mt-0.5 h-5 w-5 text-green-600" />
                      <div>
                        <h4 class="font-medium text-green-900">
                          {{ selectedGrave.grave_no }}
                        </h4>
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

                <!-- Grave Search -->
                <div v-else class="space-y-4">
                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                      <Label>Search by</Label>
                      <Select v-model="graveSearchType">
                        <SelectTrigger class="mt-1">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="owner_name">Owner Name</SelectItem>
                          <SelectItem value="grave_no">Grave Number</SelectItem>
                        </SelectContent>
                      </Select>
                    </div>
                    <div class="sm:col-span-2">
                      <Label>Search term</Label>
                      <div class="mt-1 flex space-x-2">
                        <div class="relative flex-1">
                          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <Search class="h-5 w-5 text-gray-400" />
                          </div>
                          <Input
                            v-model="graveSearchTerm"
                            :placeholder="graveSearchType === 'owner_name' ? 'Enter owner name...' : 'Enter grave number...'"
                            class="pl-10"
                            @keyup.enter="searchGraves"
                          />
                        </div>
                        <Button type="button" @click="searchGraves" :disabled="graveSearchTerm.length < 2 || isSearching">
                          {{ isSearching ? 'Searching...' : 'Search' }}
                        </Button>
                      </div>
                    </div>
                  </div>

                  <!-- Search Results -->
                  <div v-if="searchResults.length > 0" class="space-y-2">
                    <Label>Search Results</Label>
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
                          <div>
                            <h4 class="font-medium">{{ grave.grave_no }}</h4>
                            <p class="text-sm text-gray-600">{{ grave.section }}, Row {{ grave.row_no }} • Owner: {{ grave.owner_name }}</p>
                            <div class="mt-1">
                              <Badge :class="grave.is_eligible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                {{ grave.eligibility_message }}
                              </Badge>
                            </div>
                            <div v-if="grave.has_valid_members" class="mt-2">
                              <p class="flex items-center text-xs text-gray-500">
                                <Users class="mr-1 h-3 w-3" />
                                {{ grave.available_members.length }} available members
                              </p>
                            </div>
                          </div>
                          <Button v-if="grave.is_eligible" variant="outline" size="sm" @click.stop="selectGrave(grave)"> Select </Button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="form.errors.permanent_grave_id" class="text-sm text-red-600">
                  {{ form.errors.permanent_grave_id }}
                </div>
              </CardContent>
            </Card>

            <!-- Valid Member Selection -->
            <Card v-if="selectedGrave && selectedGrave.has_valid_members">
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <User class="h-5 w-5" />
                  <span>Select Deceased Member</span>
                </CardTitle>
                <CardDescription> Choose the valid member who has passed away </CardDescription>
              </CardHeader>
              <CardContent>
                <div class="space-y-3">
                  <div
                    v-for="member in selectedGrave.available_members"
                    :key="member.id"
                    :class="[
                      'cursor-pointer rounded-lg border p-3 transition-colors',
                      form.valid_member_id === member.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50',
                    ]"
                    @click="form.valid_member_id = member.id"
                  >
                    <div class="flex items-center justify-between">
                      <div>
                        <h4 class="font-medium">{{ member.full_name }}</h4>
                        <p class="text-sm text-gray-600">{{ member.relationship }}</p>
                      </div>
                      <div v-if="form.valid_member_id === member.id" class="text-blue-600">
                        <CheckCircle class="h-5 w-5" />
                      </div>
                    </div>
                  </div>
                </div>
                <div v-if="form.errors.valid_member_id" class="mt-2 text-sm text-red-600">
                  {{ form.errors.valid_member_id }}
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
                  <Input id="died_on" v-model="form.died_on" type="date" :class="form.errors.died_on && 'border-red-500'" class="mt-1" />
                  <div v-if="form.errors.died_on" class="mt-1 text-sm text-red-600">
                    {{ form.errors.died_on }}
                  </div>
                </div>

                <div>
                  <Label for="buried_on">Date of Burial *</Label>
                  <Input id="buried_on" v-model="form.buried_on" type="date" :class="form.errors.buried_on && 'border-red-500'" class="mt-1" />
                  <div v-if="form.errors.buried_on" class="mt-1 text-sm text-red-600">
                    {{ form.errors.buried_on }}
                  </div>
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
                  <Select v-model="form.applicant_type">
                    <SelectTrigger class="mt-1">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="member">Church Member</SelectItem>
                      <SelectItem value="non_member">Non-Member</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label for="applicant_name">Applicant Name *</Label>
                    <Input id="applicant_name" v-model="form.applicant_name" :class="form.errors.applicant_name && 'border-red-500'" class="mt-1" />
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

            <!-- Services Selection -->
            <Card>
              <CardHeader>
                <CardTitle>Additional Services</CardTitle>
                <CardDescription> Select any additional services required for the burial </CardDescription>
              </CardHeader>
              <CardContent>
                <div class="space-y-3">
                  <div v-for="service in serviceTypes" :key="service.id" class="flex items-start space-x-3 rounded-lg border p-3">
                    <Checkbox
                      :id="`service-${service.id}`"
                      :checked="selectedServiceIds.includes(service.id)"
                      @update:checked="
                        (checked: boolean) => {
                          if (checked) {
                            selectedServiceIds.push(service.id);
                          } else {
                            const index = selectedServiceIds.indexOf(service.id);
                            if (index > -1) selectedServiceIds.splice(index, 1);
                          }
                        }
                      "
                    />
                    <div class="flex-1">
                      <Label :for="`service-${service.id}`" class="cursor-pointer">
                        <div class="flex items-start justify-between">
                          <div>
                            <span class="font-medium">{{ service.name }}</span>
                            <p v-if="service.description" class="mt-1 text-sm text-gray-600">
                              {{ service.description }}
                            </p>
                          </div>
                          <span class="font-medium text-green-600">
                            {{ formatCurrency(service.cost) }}
                          </span>
                        </div>
                      </Label>
                    </div>
                  </div>
                </div>

                <!-- Total Cost Display -->
                <div v-if="totalCost > 0" class="mt-4 border-t pt-4">
                  <div class="flex items-center justify-between">
                    <span class="text-lg font-medium">Total Cost:</span>
                    <span class="text-xl font-bold text-green-600">
                      {{ formatCurrency(totalCost) }}
                    </span>
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
                    rows="3"
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
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
