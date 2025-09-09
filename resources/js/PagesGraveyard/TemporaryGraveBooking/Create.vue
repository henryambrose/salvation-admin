<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, MapPin, Phone, User, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface TemporaryGrave {
  id: number;
  grave_no: string;
  section: string;
  row_no: string;
  is_available: boolean;
}

interface Gender {
  id: number;
  name: string;
}

interface Parish {
  id: number;
  name: string;
}

interface Relationship {
  id: number;
  name: string;
}

interface Member {
  id: number;
  name: string;
  full_name: string;
  member_no: string;
  family_no: string;
  community: {
    name: string;
  };
  current_add1: string;
  contact_no_1: string;
  gender: string;
}

interface Props {
  availableGraves: TemporaryGrave[];
  genders: Gender[];
  parishes: Parish[];
  relationships: Relationship[];
}

const props = defineProps<Props>();

const form = useForm({
  temporary_grave_id: null as number | null,
  deceased_person_type: '' as '' | 'member' | 'external',
  deceased_member_id: null as number | null,
  dead_first_name: '' as string | undefined,
  dead_last_name: '' as string | undefined,
  date_of_birth: '',
  age: null as number | null,
  months: null as number | null,
  days: null as number | null,
  died_on: '',
  buried_on: '',
  gender_id: '',
  cause_of_death: '',
  nationality: 'Indian',
  parish_id: '16',
  minister: '',
  applicant_type: '' as '' | 'member' | 'external',
  applicant_name: '',
  contact_no: '',
  contact_email: '',
  relationship_id: '',
  permit_no: '',
  special_requirements: '',
});

// Get selected grave details
const selectedGrave = computed(() => {
  if (!form.temporary_grave_id) return null;
  return props.availableGraves.find((grave) => grave.id === form.temporary_grave_id);
});

// Member search functionality
const memberSearchQuery = ref('');
const memberSearchResults = ref<Member[]>([]);
const selectedMember = ref<Member | null>(null);
const isSearchingMembers = ref(false);

// Search members function
const searchMembers = async () => {
  if (memberSearchQuery.value.length < 2) {
    memberSearchResults.value = [];
    return;
  }

  isSearchingMembers.value = true;
  try {
    const response = await fetch(
      route('graveyard.temporary-grave-bookings.search-members') + '?query=' + encodeURIComponent(memberSearchQuery.value)
    );
    const data = await response.json();
    memberSearchResults.value = data;
  } catch (error) {
    console.error('Error searching members:', error);
    memberSearchResults.value = [];
  } finally {
    isSearchingMembers.value = false;
  }
};

// Select member function
const selectMember = (member: Member) => {
  selectedMember.value = member;
  form.deceased_member_id = member.id;
  memberSearchQuery.value = '';
  memberSearchResults.value = [];
};

// Clear selected member
const clearSelectedMember = () => {
  selectedMember.value = null;
  form.deceased_member_id = null;
};

const submit = () => {
  form
    .transform((data) => {
      const transformedData = {
        ...data,
        gender_id: data.gender_id ? parseInt(data.gender_id) : null,
        parish_id: data.parish_id ? parseInt(data.parish_id) : null,
        relationship_id: data.relationship_id ? parseInt(data.relationship_id) : null,
        deceased_member_id: data.deceased_member_id || null,
      };

      // Only include external deceased person fields when person type is external
      if (data.deceased_person_type !== 'external') {
        transformedData.dead_first_name = undefined;
        transformedData.dead_last_name = undefined;
      }

      return transformedData;
    })
    .post(route('graveyard.temporary-grave-bookings.store'));
};
</script>

<template>
  <Head title="Book Temporary Grave" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center space-x-3">
                  <Button variant="outline" size="sm" as-child>
                    <Link :href="route('graveyard.temporary-grave-bookings.index')">
                      <ArrowLeft class="h-4 w-4" />
                    </Link>
                  </Button>
                  <div>
                    <h3 class="text-base leading-6 font-semibold text-gray-900">Book Temporary Grave</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Create a new temporary grave booking</p>
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
                  <span>Select Temporary Grave</span>
                </CardTitle>
                <CardDescription> Choose an available temporary grave </CardDescription>
              </CardHeader>
              <CardContent>
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                  <!-- Main Selection Area -->
                  <div class="space-y-4 lg:col-span-2">
                    <!-- Suggested Grave -->
                    <div v-if="availableGraves.length > 0" class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                      <h4 class="mb-2 font-medium text-blue-900">Recommended Available Grave</h4>
                      <div class="flex items-center justify-between rounded-lg border border-blue-300 bg-white p-3">
                        <div>
                          <span class="font-semibold">{{ availableGraves[0].grave_no }}</span>
                          <span class="ml-2 text-gray-600">Section {{ availableGraves[0].section }}, Row {{ availableGraves[0].row_no }}</span>
                        </div>
                        <Button
                          type="button"
                          size="sm"
                          :variant="form.temporary_grave_id === availableGraves[0].id ? 'default' : 'outline'"
                          @click="form.temporary_grave_id = availableGraves[0].id"
                        >
                          {{ form.temporary_grave_id === availableGraves[0].id ? 'Selected' : 'Select This Grave' }}
                        </Button>
                      </div>
                    </div>

                    <!-- Selected Grave Display -->
                    <div v-if="selectedGrave" class="rounded-lg border border-green-200 bg-green-50 p-4">
                      <div class="flex items-center justify-between">
                        <div>
                          <h4 class="font-medium text-green-900">Selected Grave</h4>
                          <p class="text-green-700">
                            <span class="font-semibold">{{ selectedGrave.grave_no }}</span>
                            - Section {{ selectedGrave.section }}, Row {{ selectedGrave.row_no }}
                          </p>
                        </div>
                        <Button type="button" size="sm" variant="outline" @click="form.temporary_grave_id = null"> Change </Button>
                      </div>
                    </div>

                    <!-- Validation Error -->
                    <div v-if="form.errors.temporary_grave_id" class="text-sm text-red-600">
                      {{ form.errors.temporary_grave_id }}
                    </div>
                  </div>

                  <!-- Available Graves Sidebar -->
                  <div class="lg:col-span-1">
                    <div class="max-h-96 overflow-y-auto rounded-lg border p-4">
                      <h4 class="mb-3 font-medium text-gray-900">All Available Graves ({{ availableGraves.length }})</h4>
                      <div class="space-y-2">
                        <div
                          v-for="grave in availableGraves"
                          :key="grave.id"
                          :class="[
                            'cursor-pointer rounded-lg border p-3 transition-colors',
                            form.temporary_grave_id === grave.id
                              ? 'border-blue-300 bg-blue-100 text-blue-900'
                              : 'border-gray-200 bg-gray-50 hover:bg-gray-100',
                          ]"
                          @click="form.temporary_grave_id = grave.id"
                        >
                          <div class="text-sm font-medium">{{ grave.grave_no }}</div>
                          <div class="text-xs text-gray-600">Sec {{ grave.section }}, Row {{ grave.row_no }}</div>
                        </div>
                      </div>

                      <div v-if="availableGraves.length === 0" class="py-4 text-center text-gray-500">
                        <MapPin class="mx-auto mb-2 h-8 w-8 text-gray-400" />
                        <p class="text-sm">No graves available</p>
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Deceased Person Details -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <User class="h-5 w-5" />
                  <span>Deceased Person Details</span>
                </CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <!-- Person Type Selection -->
                <div>
                  <Label class="text-base font-medium">Person Type *</Label>
                  <div class="mt-2 flex items-center space-x-6">
                    <label class="flex items-center">
                      <input
                        v-model="form.deceased_person_type"
                        type="radio"
                        value="member"
                        class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                      <span class="ml-2 text-sm font-medium text-gray-700">Member</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        v-model="form.deceased_person_type"
                        type="radio"
                        value="external"
                        class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                      <span class="ml-2 text-sm font-medium text-gray-700">External</span>
                    </label>
                  </div>
                  <div v-if="form.errors.deceased_person_type" class="mt-1 text-sm text-red-600">
                    {{ form.errors.deceased_person_type }}
                  </div>
                </div>

                <!-- Member Search (for deceased members) -->
                <div v-if="form.deceased_person_type === 'member'" class="space-y-4">
                  <div>
                    <Label>Search Member *</Label>
                    <div class="relative mt-1">
                      <input
                        v-model="memberSearchQuery"
                        type="text"
                        placeholder="Search by name, family number, or phone..."
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        :class="form.errors.deceased_member_id && 'border-red-500'"
                        @input="searchMembers"
                      />

                      <!-- Search Results Dropdown -->
                      <div
                        v-if="memberSearchResults.length > 0 && memberSearchQuery"
                        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-300 bg-white shadow-lg"
                      >
                        <div
                          v-for="member in memberSearchResults"
                          :key="member.id"
                          @click="selectMember(member)"
                          class="cursor-pointer border-b border-gray-100 px-4 py-3 last:border-b-0 hover:bg-gray-50"
                        >
                          <div class="font-medium text-gray-900">{{ member.name }}</div>
                          <div class="text-sm text-gray-500">
                            Community No: {{ member.community?.name?.split('-')[0]?.trim() || 'N/A' }} | 
                            Family: {{ member.family_no || 'N/A' }} | 
                            Member No: {{ member.member_no || 'N/A' }}
                          </div>
                          <div class="text-sm text-gray-500">
                            Address: {{ member.current_add1 || 'N/A' }} | 
                            Contact: {{ member.contact_no_1 || 'N/A' }}
                          </div>
                        </div>
                      </div>

                      <!-- Loading State -->
                      <div v-if="isSearchingMembers" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <div class="h-4 w-4 animate-spin rounded-full border-2 border-blue-500 border-t-transparent"></div>
                      </div>
                    </div>

                    <!-- Search Helper Text -->
                    <p class="mt-1 text-xs text-gray-500">Start typing to search (minimum 2 characters)</p>
                    
                    <div v-if="form.errors.deceased_member_id" class="mt-1 text-sm text-red-600">
                      {{ form.errors.deceased_member_id }}
                    </div>
                  </div>

                  <!-- Selected Member Display -->
                  <div v-if="selectedMember" class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-start justify-between">
                      <div>
                        <h4 class="font-medium text-blue-900">Selected Member</h4>
                        <div class="mt-2 space-y-1">
                          <div class="font-medium text-blue-800">{{ selectedMember.name }}</div>
                          <div class="text-sm text-blue-700">
                            Community No: {{ selectedMember.community?.name?.split('-')[0]?.trim() || 'N/A' }} | 
                            Family: {{ selectedMember.family_no || 'N/A' }}
                          </div>
                          <div class="text-sm text-blue-700">
                            Member No: {{ selectedMember.member_no || 'N/A' }}
                          </div>
                          <div class="text-sm text-blue-700">
                            Address: {{ selectedMember.current_add1 || 'N/A' }}
                          </div>
                          <div class="text-sm text-blue-700">
                            Contact: {{ selectedMember.contact_no_1 || 'N/A' }}
                          </div>
                        </div>
                      </div>
                      <button @click="clearSelectedMember" type="button" class="text-blue-600 hover:text-blue-800">
                        <X class="h-4 w-4" />
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Manual Entry for External -->
                <div v-if="form.deceased_person_type === 'external'" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label for="dead_first_name">First Name *</Label>
                    <Input
                      id="dead_first_name"
                      v-model="form.dead_first_name"
                      :class="form.errors.dead_first_name && 'border-red-500'"
                      class="mt-1"
                    />
                    <div v-if="form.errors.dead_first_name" class="mt-1 text-sm text-red-600">
                      {{ form.errors.dead_first_name }}
                    </div>
                  </div>

                  <div>
                    <Label for="dead_last_name">Last Name *</Label>
                    <Input id="dead_last_name" v-model="form.dead_last_name" :class="form.errors.dead_last_name && 'border-red-500'" class="mt-1" />
                    <div v-if="form.errors.dead_last_name" class="mt-1 text-sm text-red-600">
                      {{ form.errors.dead_last_name }}
                    </div>
                  </div>

                  <div>
                    <Label for="date_of_birth">Date of Birth</Label>
                    <Input id="date_of_birth" v-model="form.date_of_birth" type="date" class="mt-1" />
                  </div>

                  <div>
                    <Label for="gender_id">Gender *</Label>
                    <select
                      v-model="form.gender_id"
                      class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                      :class="form.errors.gender_id && 'border-red-500'"
                    >
                      <option value="">Select Gender</option>
                      <option v-for="gender in genders" :key="gender.id" :value="gender.id.toString()">
                        {{ gender.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.gender_id" class="mt-1 text-sm text-red-600">
                      {{ form.errors.gender_id }}
                    </div>
                  </div>
                </div>

                <!-- Age Details -->
                <div>
                  <Label class="text-base font-medium">Age at Death</Label>
                  <div class="mt-2 grid grid-cols-3 gap-4">
                    <div>
                      <Label for="age">Years</Label>
                      <Input
                        id="age"
                        :model-value="form.age ?? ''"
                        @input="form.age = $event.target.value ? Number($event.target.value) : null"
                        type="number"
                        min="0"
                        max="150"
                        class="mt-1"
                      />
                    </div>
                    <div>
                      <Label for="months">Months</Label>
                      <Input
                        id="months"
                        :model-value="form.months ?? ''"
                        @input="form.months = $event.target.value ? Number($event.target.value) : null"
                        type="number"
                        min="0"
                        max="11"
                        class="mt-1"
                      />
                    </div>
                    <div>
                      <Label for="days">Days</Label>
                      <Input
                        id="days"
                        :model-value="form.days ?? ''"
                        @input="form.days = $event.target.value ? Number($event.target.value) : null"
                        type="number"
                        min="0"
                        max="30"
                        class="mt-1"
                      />
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <Label for="nationality">Nationality</Label>
                    <Input id="nationality" v-model="form.nationality" class="mt-1" />
                  </div>

                  <div>
                    <Label for="parish_id">Parish</Label>
                    <select
                      v-model="form.parish_id"
                      class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="">Select Parish</option>
                      <option v-for="parish in parishes" :key="parish.id" :value="parish.id.toString()">
                        {{ parish.name }}
                      </option>
                    </select>
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
                  <select
                    v-model="form.applicant_type"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    :class="form.errors.applicant_type && 'border-red-500'"
                  >
                    <option value="">Select applicant type</option>
                    <option value="member">Member</option>
                    <option value="external">External</option>
                  </select>
                  <div v-if="form.errors.applicant_type" class="mt-1 text-sm text-red-600">
                    {{ form.errors.applicant_type }}
                  </div>
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
                    <Label for="relationship_id">Relationship to Deceased</Label>
                    <select
                      v-model="form.relationship_id"
                      class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                      <option value="">Select Relationship</option>
                      <option v-for="relationship in relationships" :key="relationship.id" :value="relationship.id.toString()">
                        {{ relationship.name }}
                      </option>
                    </select>
                  </div>

                  <div class="sm:col-span-2">
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
                <Link :href="route('graveyard.temporary-grave-bookings.index')"> Cancel </Link>
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
