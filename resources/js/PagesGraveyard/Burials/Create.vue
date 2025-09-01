<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref, watch } from 'vue';
import { ArrowLeft, Save, Calculator, User, MapPin, FileText, DollarSign, Calendar } from 'lucide-vue-next';

const props = defineProps({
  availablePermanentGraves: Array,
  availableTemporaryGraves: Array,
  serviceTypes: Object,
  genders: Array,
  parishes: Array,
  paymentMethods: Array,
  members: Array,
});

const form = useForm({
  // Grave selection
  grave_type: 'permanent',
  permanent_grave_id: '',
  temporary_grave_id: '',
  
  // Deceased person details
  dead_first_name: '',
  dead_last_name: '',
  date_of_birth: '',
  age: '',
  months: '',
  days: '',
  died_on: '',
  buried_on: '',
  gender_id: '',
  cause_of_death: '',
  nationality: 'Indian',
  remarks: '',
  
  // Parish and religious details
  parish_id: '',
  minister: '',
  
  // Relationship and applicant details
  relationship: 'non_member',
  applicant_type: 'non_member',
  member_id: '',
  applicant_member_id: '',
  applicant_name: '',
  contact_no: '',
  
  // BMC and administrative details
  permit_no: '',
  
  // Financial details
  total_amount: 0,
  payment_method_id: '',
  payment_remarks: '',
  payment_status: 'pending',
});

const selectedMember = ref(null);
const selectedApplicantMember = ref(null);
const memberSearch = ref('');
const applicantMemberSearch = ref('');

// Computed properties for filtered members
const filteredMembers = computed(() => {
  if (!memberSearch.value) return props.members?.slice(0, 20) || [];
  return props.members?.filter(member => 
    `${member.first_name} ${member.last_name}`.toLowerCase().includes(memberSearch.value.toLowerCase()) ||
    member.family_no?.toString().includes(memberSearch.value)
  ).slice(0, 20) || [];
});

const filteredApplicantMembers = computed(() => {
  if (!applicantMemberSearch.value) return props.members?.slice(0, 20) || [];
  return props.members?.filter(member => 
    `${member.first_name} ${member.last_name}`.toLowerCase().includes(applicantMemberSearch.value.toLowerCase()) ||
    member.family_no?.toString().includes(memberSearch.value)
  ).slice(0, 20) || [];
});

// Available graves based on type
const availableGraves = computed(() => {
  if (form.grave_type === 'permanent') {
    return props.availablePermanentGraves || [];
  } else {
    return props.availableTemporaryGraves || [];
  }
});

// Calculate age when date of birth or death date changes
function calculateAgeFromDates() {
  if (!form.date_of_birth || !form.died_on) return;
  
  const dob = new Date(form.date_of_birth);
  const deathDate = new Date(form.died_on);
  
  if (dob >= deathDate) {
    form.age = '';
    form.months = '';
    form.days = '';
    return;
  }
  
  let years = deathDate.getFullYear() - dob.getFullYear();
  let months = deathDate.getMonth() - dob.getMonth();
  let days = deathDate.getDate() - dob.getDate();
  
  if (days < 0) {
    months--;
    const prevMonth = new Date(deathDate.getFullYear(), deathDate.getMonth(), 0);
    days += prevMonth.getDate();
  }
  
  if (months < 0) {
    years--;
    months += 12;
  }
  
  form.age = years.toString();
  form.months = months.toString();
  form.days = days.toString();
}

// Calculate DOB when age is manually entered
function calculateDobFromAge() {
  if (!form.died_on || (!form.age && !form.months && !form.days)) return;
  
  const deathDate = new Date(form.died_on);
  const age = parseInt(form.age) || 0;
  const months = parseInt(form.months) || 0;
  const days = parseInt(form.days) || 0;
  
  const calculatedDob = new Date(deathDate);
  calculatedDob.setFullYear(calculatedDob.getFullYear() - age);
  calculatedDob.setMonth(calculatedDob.getMonth() - months);
  calculatedDob.setDate(calculatedDob.getDate() - days);
  
  form.date_of_birth = calculatedDob.toISOString().split('T')[0];
}

// Watchers for age calculation
watch([() => form.date_of_birth, () => form.died_on], calculateAgeFromDates);
watch([() => form.age, () => form.months, () => form.days], calculateDobFromAge);

// Watch grave type to clear selection
watch(() => form.grave_type, () => {
  form.permanent_grave_id = '';
  form.temporary_grave_id = '';
});

// Watch relationship to clear member selection
watch(() => form.relationship, () => {
  if (form.relationship === 'non_member') {
    form.member_id = '';
    selectedMember.value = null;
  }
});

// Watch applicant type to clear applicant data
watch(() => form.applicant_type, () => {
  if (form.applicant_type === 'member') {
    form.applicant_name = '';
  } else {
    form.applicant_member_id = '';
    selectedApplicantMember.value = null;
  }
});

function selectMember(member: any) {
  selectedMember.value = member;
  form.member_id = member.id;
  memberSearch.value = `${member.first_name} ${member.last_name} (${member.family_no})`;
}

function selectApplicantMember(member: any) {
  selectedApplicantMember.value = member;
  form.applicant_member_id = member.id;
  applicantMemberSearch.value = `${member.first_name} ${member.last_name} (${member.family_no})`;
}

function formatGraveOption(grave: any) {
  return `Section ${grave.section} - Row ${grave.row_no} - Grave ${grave.grave_no}`;
}

function submit() {
  form.post('/graveyard/burials', {
    onSuccess: () => {
      router.visit('/graveyard/burials');
    },
  });
}

function goBack() {
  router.visit('/graveyard/burials');
}

const breadcrumbs = [
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Burials', href: '/graveyard/burials' },
  { title: 'Create', href: '/graveyard/burials/create' }
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="New Burial Booking" />
    
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Button @click="goBack" variant="outline" class="rounded-full p-2">
            <ArrowLeft class="w-4 h-4" />
          </Button>
          <h1 class="text-2xl font-bold text-blue-700">New Burial Booking</h1>
        </div>
        </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Grave Selection -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
          <h2 class="mb-4 text-xl font-semibold text-blue-700 flex items-center gap-2">
            <MapPin class="w-5 h-5" />
            Grave Selection
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
              <Label for="grave_type">Grave Type *</Label>
              <select 
                v-model="form.grave_type" 
                id="grave_type"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                required
              >
                <option value="permanent">Permanent Grave</option>
                <option value="temporary">Temporary Grave</option>
              </select>
              <div v-if="form.errors.grave_type" class="mt-1 text-sm text-red-500">{{ form.errors.grave_type }}</div>
        </div>

        <div>
              <Label :for="form.grave_type === 'permanent' ? 'permanent_grave_id' : 'temporary_grave_id'">
                Available {{ form.grave_type === 'permanent' ? 'Permanent' : 'Temporary' }} Graves *
              </Label>
              <select 
                v-if="form.grave_type === 'permanent'"
                v-model="form.permanent_grave_id" 
                id="permanent_grave_id"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                required
              >
                <option value="">Select a grave</option>
                <option v-for="grave in availableGraves" :key="grave.id" :value="grave.id">
                  {{ formatGraveOption(grave) }}
                </option>
              </select>
              <select 
                v-else
                v-model="form.temporary_grave_id" 
                id="temporary_grave_id"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                required
              >
                <option value="">Select a grave</option>
                <option v-for="grave in availableGraves" :key="grave.id" :value="grave.id">
                  {{ formatGraveOption(grave) }}
                </option>
              </select>
              <div v-if="form.errors.permanent_grave_id || form.errors.temporary_grave_id" class="mt-1 text-sm text-red-500">
                {{ form.errors.permanent_grave_id || form.errors.temporary_grave_id }}
              </div>
            </div>
          </div>
        </div>

        <!-- Deceased Person Details -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
          <h2 class="mb-4 text-xl font-semibold text-blue-700 flex items-center gap-2">
            <User class="w-5 h-5" />
            Deceased Person Details
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label for="dead_first_name">First Name *</Label>
              <Input v-model="form.dead_first_name" id="dead_first_name" type="text" required />
              <div v-if="form.errors.dead_first_name" class="mt-1 text-sm text-red-500">{{ form.errors.dead_first_name }}</div>
            </div>
            
            <div>
              <Label for="dead_last_name">Last Name *</Label>
              <Input v-model="form.dead_last_name" id="dead_last_name" type="text" required />
              <div v-if="form.errors.dead_last_name" class="mt-1 text-sm text-red-500">{{ form.errors.dead_last_name }}</div>
            </div>
            
            <div>
              <Label for="gender_id">Gender *</Label>
              <select 
                v-model="form.gender_id" 
                id="gender_id"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                required
              >
                <option value="">Select Gender</option>
                <option v-for="gender in genders" :key="gender.id" :value="gender.id">
                  {{ gender.name }}
                </option>
              </select>
              <div v-if="form.errors.gender_id" class="mt-1 text-sm text-red-500">{{ form.errors.gender_id }}</div>
            </div>
            
            <div>
              <Label for="nationality">Nationality</Label>
              <Input v-model="form.nationality" id="nationality" type="text" />
              <div v-if="form.errors.nationality" class="mt-1 text-sm text-red-500">{{ form.errors.nationality }}</div>
            </div>
            
            <div>
              <Label for="date_of_birth">Date of Birth</Label>
              <Input v-model="form.date_of_birth" id="date_of_birth" type="date" />
              <div v-if="form.errors.date_of_birth" class="mt-1 text-sm text-red-500">{{ form.errors.date_of_birth }}</div>
            </div>
            
            <div class="flex gap-2">
              <div class="flex-1">
                <Label for="age">Age (Years)</Label>
                <Input v-model="form.age" id="age" type="number" min="0" />
              </div>
              <div class="flex-1">
                <Label for="months">Months</Label>
                <Input v-model="form.months" id="months" type="number" min="0" max="11" />
              </div>
              <div class="flex-1">
                <Label for="days">Days</Label>
                <Input v-model="form.days" id="days" type="number" min="0" max="30" />
              </div>
            </div>
            
            <div>
              <Label for="died_on">Date of Death *</Label>
              <Input v-model="form.died_on" id="died_on" type="date" required />
              <div v-if="form.errors.died_on" class="mt-1 text-sm text-red-500">{{ form.errors.died_on }}</div>
            </div>
            
        <div>
              <Label for="buried_on">Burial Date *</Label>
              <Input v-model="form.buried_on" id="buried_on" type="date" required />
              <div v-if="form.errors.buried_on" class="mt-1 text-sm text-red-500">{{ form.errors.buried_on }}</div>
            </div>
            
            <div class="md:col-span-2">
              <Label for="cause_of_death">Cause of Death</Label>
              <Textarea v-model="form.cause_of_death" id="cause_of_death" rows="2" />
              <div v-if="form.errors.cause_of_death" class="mt-1 text-sm text-red-500">{{ form.errors.cause_of_death }}</div>
            </div>
          </div>
        </div>

        <!-- Parish and Religious Details -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
          <h2 class="mb-4 text-xl font-semibold text-blue-700 flex items-center gap-2">
            <Calendar class="w-5 h-5" />
            Parish & Religious Details
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label for="parish_id">Parish</Label>
              <select 
                v-model="form.parish_id" 
                id="parish_id"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
              >
                <option value="">Select Parish</option>
                <option v-for="parish in parishes" :key="parish.id" :value="parish.id">
                  {{ parish.name }}
                </option>
              </select>
              <div v-if="form.errors.parish_id" class="mt-1 text-sm text-red-500">{{ form.errors.parish_id }}</div>
            </div>
            
        <div>
              <Label for="minister">Minister/Priest</Label>
              <Input v-model="form.minister" id="minister" type="text" placeholder="Fr. John Doe" />
              <div v-if="form.errors.minister" class="mt-1 text-sm text-red-500">{{ form.errors.minister }}</div>
            </div>
          </div>
        </div>

        <!-- Relationship and Applicant Details -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
          <h2 class="mb-4 text-xl font-semibold text-blue-700 flex items-center gap-2">
            <User class="w-5 h-5" />
            Relationship & Applicant Details
          </h2>
          
          <div class="space-y-4">
            <!-- Deceased Relationship -->
            <div>
              <Label>Deceased Relationship with Church *</Label>
              <div class="flex gap-4 mt-2">
                <label class="flex items-center gap-2">
                  <input v-model="form.relationship" type="radio" value="member" class="text-blue-600" />
                  <span>Member</span>
                </label>
                <label class="flex items-center gap-2">
                  <input v-model="form.relationship" type="radio" value="non_member" class="text-blue-600" />
                  <span>Non-Member</span>
                </label>
              </div>
              <div v-if="form.errors.relationship" class="mt-1 text-sm text-red-500">{{ form.errors.relationship }}</div>
            </div>

            <!-- Member Selection for Deceased -->
            <div v-if="form.relationship === 'member'">
              <Label for="member_search">Search Deceased Member</Label>
              <div class="relative">
                <Input 
                  v-model="memberSearch" 
                  id="member_search"
                  type="text" 
                  placeholder="Type name or family number..."
                  @input="() => { if (!memberSearch) { form.member_id = ''; selectedMember = null; } }"
                />
                <div v-if="memberSearch && !selectedMember && filteredMembers.length > 0" 
                     class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-y-auto">
                  <div v-for="member in filteredMembers" :key="member.id" 
                       @click="selectMember(member)"
                       class="px-3 py-2 hover:bg-blue-50 cursor-pointer">
                    {{ member.first_name }} {{ member.last_name }} ({{ member.family_no }})
                  </div>
                </div>
              </div>
              <div v-if="form.errors.member_id" class="mt-1 text-sm text-red-500">{{ form.errors.member_id }}</div>
            </div>

            <!-- Applicant Type -->
            <div>
              <Label>Applicant Type *</Label>
              <div class="flex gap-4 mt-2">
                <label class="flex items-center gap-2">
                  <input v-model="form.applicant_type" type="radio" value="member" class="text-blue-600" />
                  <span>Member</span>
                </label>
                <label class="flex items-center gap-2">
                  <input v-model="form.applicant_type" type="radio" value="non_member" class="text-blue-600" />
                  <span>Non-Member</span>
                </label>
              </div>
              <div v-if="form.errors.applicant_type" class="mt-1 text-sm text-red-500">{{ form.errors.applicant_type }}</div>
            </div>

            <!-- Applicant Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Member Applicant -->
              <div v-if="form.applicant_type === 'member'">
                <Label for="applicant_member_search">Search Applicant Member</Label>
                <div class="relative">
                  <Input 
                    v-model="applicantMemberSearch" 
                    id="applicant_member_search"
                    type="text" 
                    placeholder="Type name or family number..."
                    @input="() => { if (!applicantMemberSearch) { form.applicant_member_id = ''; selectedApplicantMember = null; } }"
                  />
                  <div v-if="applicantMemberSearch && !selectedApplicantMember && filteredApplicantMembers.length > 0" 
                       class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-y-auto">
                    <div v-for="member in filteredApplicantMembers" :key="member.id" 
                         @click="selectApplicantMember(member)"
                         class="px-3 py-2 hover:bg-blue-50 cursor-pointer">
                      {{ member.first_name }} {{ member.last_name }} ({{ member.family_no }})
                    </div>
                  </div>
                </div>
                <div v-if="form.errors.applicant_member_id" class="mt-1 text-sm text-red-500">{{ form.errors.applicant_member_id }}</div>
              </div>

              <!-- Non-Member Applicant -->
              <div v-if="form.applicant_type === 'non_member'">
                <Label for="applicant_name">Applicant Name</Label>
                <Input v-model="form.applicant_name" id="applicant_name" type="text" />
                <div v-if="form.errors.applicant_name" class="mt-1 text-sm text-red-500">{{ form.errors.applicant_name }}</div>
        </div>

        <div>
                <Label for="contact_no">Contact Number</Label>
                <Input v-model="form.contact_no" id="contact_no" type="tel" placeholder="+91 9876543210" />
                <div v-if="form.errors.contact_no" class="mt-1 text-sm text-red-500">{{ form.errors.contact_no }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Administrative Details -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
          <h2 class="mb-4 text-xl font-semibold text-blue-700 flex items-center gap-2">
            <FileText class="w-5 h-5" />
            Administrative Details
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
              <Label for="permit_no">BMC Permit Number</Label>
              <Input v-model="form.permit_no" id="permit_no" type="text" placeholder="BMC/2025/001" />
              <div v-if="form.errors.permit_no" class="mt-1 text-sm text-red-500">{{ form.errors.permit_no }}</div>
            </div>
          </div>
          
          <div class="mt-4">
            <Label for="remarks">Remarks</Label>
            <Textarea v-model="form.remarks" id="remarks" rows="3" placeholder="Any additional notes or remarks..." />
            <div v-if="form.errors.remarks" class="mt-1 text-sm text-red-500">{{ form.errors.remarks }}</div>
          </div>
        </div>

        <!-- Payment Details -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
          <h2 class="mb-4 text-xl font-semibold text-blue-700 flex items-center gap-2">
            <DollarSign class="w-5 h-5" />
            Payment Details
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <Label for="total_amount">Total Amount (₹)</Label>
              <Input v-model="form.total_amount" id="total_amount" type="number" step="0.01" min="0" />
              <div v-if="form.errors.total_amount" class="mt-1 text-sm text-red-500">{{ form.errors.total_amount }}</div>
            </div>
            
            <div>
              <Label for="payment_method_id">Payment Method *</Label>
              <select 
                v-model="form.payment_method_id" 
                id="payment_method_id"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
                required
              >
                <option value="">Select Payment Method</option>
                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                  {{ method.name }}
                </option>
              </select>
              <div v-if="form.errors.payment_method_id" class="mt-1 text-sm text-red-500">{{ form.errors.payment_method_id }}</div>
            </div>
            
        <div>
              <Label for="payment_status">Payment Status</Label>
              <select 
                v-model="form.payment_status" 
                id="payment_status"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200"
              >
                <option value="pending">Pending</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
              </select>
              <div v-if="form.errors.payment_status" class="mt-1 text-sm text-red-500">{{ form.errors.payment_status }}</div>
        </div>
      </div>

          <div class="mt-4">
            <Label for="payment_remarks">Payment Remarks</Label>
            <Textarea v-model="form.payment_remarks" id="payment_remarks" rows="2" placeholder="Payment notes, installment details, etc..." />
            <div v-if="form.errors.payment_remarks" class="mt-1 text-sm text-red-500">{{ form.errors.payment_remarks }}</div>
          </div>
      </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 pt-6">
          <Button @click="goBack" type="button" variant="outline" class="rounded-full px-6 py-2">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing" class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2 flex items-center gap-2">
            <Save class="w-4 h-4" />
            {{ form.processing ? 'Creating...' : 'Create Burial Booking' }}
          </Button>
      </div>
      </form>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Custom styles for better form presentation */
.grid {
  align-items: start;
}
</style>