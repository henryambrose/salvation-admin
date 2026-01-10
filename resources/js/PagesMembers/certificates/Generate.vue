<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { DateInput } from '@/components/ui/date-input';
import { formatDateForDisplay } from '@/lib/utils';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { AlertCircle, FileText, Search, User, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useCertificateState } from '@/composables/useCertificateState';
import { useToast } from '@/composables/useToast';

interface Member {
  id: number;
  first_name: string;
  middle_name?: string;
  last_name: string;
  member_no: string;
  family_no: string;
  date_of_birth?: string;
  baptism_date?: string;
  confirmation_date?: string;
  marriage_date?: string;
  deathrecord_id?: number;
  baptism_reg_no?: string;
  confirmation_reg_no?: string;
  marriage_reg_no?: string;
  community?: { name: string };
  parish?: { name: string };
}

const props = defineProps<{
  member?: Member;
  certificateTypes: Array<{ value: number; label: string; code: string }>;
  canGenerateCertificates: boolean;
  availableTypes?: string[];
  selectedType?: number;
  existingCertificates?: Array<{
    id: number;
    certificate_type_id: number;
    certificate_type: string; // accessor for backward compatibility
    certificate_number: string;
    issued_date: string;
    template?: { name: string };
  }>;
  requiredAdditionalData?: Record<string, any>;
}>();

// Reactive existing certificates data
const existingCertificates = ref<Array<{
  id: number;
  certificate_type_id: number;
  certificate_type: string; // accessor for backward compatibility
  certificate_number: string;
  issued_date: string;
  template?: { name: string };
}>>(props.existingCertificates || []);

const { getState } = useCertificateState();

// Form setup
const form = useForm({
  member_id: props.member?.id || '',
  certificate_type_id: props.selectedType || '',
  issued_date: new Date().toISOString().split('T')[0],
  additional_data: {} as Record<string, any>,
  force_duplicate: false,
});

// Check for existing certificates of selected type
const existingCertificateOfType = computed(() => {
  if (!form.certificate_type_id || !existingCertificates.value) return null;
  // Find the selected certificate type to get its code
  const selectedType = props.certificateTypes.find(type => type.value === form.certificate_type_id);
  if (!selectedType) return null;
  return existingCertificates.value.find(cert => cert.certificate_type === selectedType.code);
});

const showDuplicateWarning = computed(() => {
  return !!existingCertificateOfType.value;
});

// Reset force duplicate when certificate type changes
watch(() => form.certificate_type_id, () => {
  form.force_duplicate = false;
});

// Member search functionality
const memberSearchQuery = ref('');
const memberSearchResults = ref<Member[]>([]);
const isSearchingMembers = ref(false);
const showMemberSearch = ref(!props.member);
const selectedMember = ref<Member | null>(props.member || null);

// Search members function
const searchMembers = async () => {
  if (memberSearchQuery.value.length < 2) {
    memberSearchResults.value = [];
    return;
  }

  isSearchingMembers.value = true;
  try {
    const response = await fetch(route('certificates.search-members') + '?query=' + encodeURIComponent(memberSearchQuery.value));
    const data = await response.json();
    memberSearchResults.value = data;
  } catch (error) {
    console.error('Error searching members:', error);
    memberSearchResults.value = [];
  } finally {
    isSearchingMembers.value = false;
  }
};

// Certificate type specific fields
const additionalFields = ref<Record<string, any>>({});

// Watch for certificate type changes to show relevant fields
watch(
  () => form.certificate_type_id,
  (newTypeId) => {
    const newType = props.certificateTypes.find(type => type.value === newTypeId)?.code;
    additionalFields.value = {};
    form.additional_data = {};

    // Initialize fields based on certificate type
    if (newType === 'baptism') {
      additionalFields.value = {
        baptism_date: selectedMember.value?.baptism_date || '',
        date_of_birth: selectedMember.value?.date_of_birth || '',
        place_of_birth: '',
        father_name: '',
        mother_name: '',
        father_residence: '',
        father_profession: '',
        nationality: '',
        godfather_name: '',
        godfather_residence: '',
        godmother_name: '',
        godmother_residence: '',
        place_of_baptism: '',
        minister_name: '',
        baptism_remarks: '',
      };
    } else if (newType === 'confirmation') {
      additionalFields.value = {
        confirmation_name: '',
        sponsor_name: '',
        bishop_name: '',
        priest_name: '',
      };
    } else if (newType === 'marriage') {
      additionalFields.value = {
        bridegroom_name: '',
        bridegroom_surname: '',
        bridegroom_dob: '',
        bridegroom_nationality: '',
        bridegroom_profession: '',
        bridegroom_residence: '',
        bridegroom_father_name: '',
        bridegroom_mother_name: '',
        bridegroom_status: 'Bachelor',
        bridegroom_if_widower_whose: '',
        bride_name: '',
        bride_surname: '',
        bride_dob: '',
        bride_nationality: '',
        bride_profession: '',
        bride_residence: '',
        bride_father_name: '',
        bride_mother_name: '',
        bride_status: 'Spinster',
        bride_if_widow_whose: '',
        first_witness_name: '',
        first_witness_residence: '',
        second_witness_name: '',
        second_witness_residence: '',
        minister_name: '',
        marriage_remarks: '',
      };
    } else if (newType === 'membership') {
      additionalFields.value = {
        join_date: selectedMember.value?.date_of_birth || '',
        community_name: selectedMember.value?.community?.name || '',
        family_number: selectedMember.value?.family_no || '',
      };
    } else if (newType === 'death') {
      additionalFields.value = {
        death_date: '',
        burial_date: '',
        deceased_name: '',
        deceased_surname: '',
        relationship: '',
        residence: '',
        age: '',
        nationality: '',
        cause_of_death: '',
        place_of_burial: '',
        minister_name: '',
        death_remarks: '',
      };
    }

    form.additional_data = { ...additionalFields.value };
  },
);

// Get current certificate type code
const currentCertificateTypeCode = computed(() => {
  if (!form.certificate_type_id) return null;
  const selectedType = props.certificateTypes.find(type => type.value === form.certificate_type_id);
  return selectedType?.code || null;
});

// Check if member has required data for certificate type
const memberValidation = computed(() => {
  if (!selectedMember.value || !form.certificate_type_id) return { valid: true, message: '' };

  const member = selectedMember.value;
  // Find the selected certificate type to get its code
  const selectedType = props.certificateTypes.find(type => type.value === form.certificate_type_id);
  if (!selectedType) return { valid: false, message: 'Invalid certificate type selected' };
  const type = selectedType.code;

  if (type === 'baptism' && !member.baptism_date) {
    return { valid: false, message: 'Member does not have a baptism date recorded.' };
  }
  if (type === 'confirmation' && !member.confirmation_date) {
    return { valid: false, message: 'Member does not have a confirmation date recorded.' };
  }
  if (type === 'marriage' && !member.marriage_date) {
    return { valid: false, message: 'Member does not have a marriage date recorded.' };
  }
  if (type === 'death' && !member.deathrecord_id) {
    return { valid: false, message: 'Member does not have a death record.' };
  }

  return { valid: true, message: '' };
});

const breadcrumbs = [
  { title: 'Certificates', href: '/certificates' },
  { title: 'Generate Certificate', href: '/certificates/generate' },
];

async function selectMember(member: Member) {
  selectedMember.value = member;
  form.member_id = member.id;
  memberSearchQuery.value = '';
  memberSearchResults.value = [];
  showMemberSearch.value = false;

  // Reset certificate type when member changes
  form.certificate_type_id = '';

  // Fetch existing certificates for the selected member
  try {
    const response = await fetch(route('certificates.api.member.certificates', member.id));
    const certificates = await response.json();
    existingCertificates.value = certificates;
  } catch (error) {
    console.error('Error fetching member certificates:', error);
    existingCertificates.value = [];
  }
}

function clearMemberSelection() {
  selectedMember.value = null;
  form.member_id = '';
  memberSearchQuery.value = '';
  memberSearchResults.value = [];
  showMemberSearch.value = true;
  form.certificate_type_id = '';
  existingCertificates.value = [];
}

function previewCertificate() {
  if (!memberValidation.value.valid) return;

  // Create a form and submit it to open PDF in new window
  const formData = new FormData();
  const data = {
    ...form.data(),
    additional_data: additionalFields.value,
  };

  Object.entries(data).forEach(([key, value]) => {
    if (key === 'additional_data') {
      formData.append(key, JSON.stringify(value));
    } else {
      formData.append(key, String(value));
    }
  });

  // Add CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (csrfToken) {
    formData.append('_token', csrfToken);
  }

  fetch('/certificates/preview', {
    method: 'POST',
    body: formData,
    headers: {
      Accept: 'application/pdf, application/json',
    },
  })
    .then(async (response) => {
      if (response.ok && response.headers.get('content-type')?.includes('application/pdf')) {
        return response.blob();
      } else if (response.headers.get('content-type')?.includes('application/json')) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Failed to generate preview');
      } else {
        throw new Error('Failed to generate preview');
      }
    })
    .then((blob) => {
      // Create a URL for the PDF blob and open in new window
      const url = window.URL.createObjectURL(blob);
      window.open(url, '_blank');

      // Clean up the URL after a delay
      setTimeout(() => window.URL.revokeObjectURL(url), 1000);
    })
    .catch((error) => {
      console.error('Preview error:', error);
      const { error: showError } = useToast();
      showError('Failed to generate preview: ' + error.message);
    });
}

function generateCertificate() {
  if (!memberValidation.value.valid) return;

  form.additional_data = additionalFields.value;

  form.post('/certificates', {
    onSuccess: (page) => {
      // Return to certificates list with saved filters
      const savedFilters = getState();
      router.get('/certificates', savedFilters, {
        preserveState: true,
        preserveScroll: true,
      });
    },
  });
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Generate Certificate" />

    <div class="mx-auto max-w-4xl">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Generate Certificate</h1>
        <p class="mt-1 text-gray-600">Create a new certificate for a member</p>
      </div>

      <form @submit.prevent="generateCertificate" class="space-y-6">
        <!-- Member Selection -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <User class="h-5 w-5" />
              Select Member
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div v-if="!selectedMember || showMemberSearch" class="space-y-4">
              <div class="relative">
                <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                <Input
                  v-model="memberSearchQuery"
                  type="text"
                  placeholder="Search by name, member number, or family number..."
                  class="pl-10"
                  @input="searchMembers"
                />
                <div v-if="isSearchingMembers" class="absolute top-1/2 right-3 -translate-y-1/2 transform">
                  <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-blue-600"></div>
                </div>
              </div>

              <!-- Search Results -->
              <div v-if="memberSearchResults.length > 0 && memberSearchQuery" class="max-h-60 overflow-y-auto rounded-lg border">
                <div
                  v-for="member in memberSearchResults"
                  :key="member.id"
                  @click="selectMember(member)"
                  class="cursor-pointer border-b p-3 last:border-b-0 hover:bg-gray-50"
                >
                  <div class="flex items-start justify-between">
                    <div>
                      <p class="font-medium text-gray-900">{{ member.first_name }} {{ member.middle_name }} {{ member.last_name }}</p>
                      <p class="text-sm text-gray-500">Member #{{ member.member_no }} | Family #{{ member.family_no }}</p>
                      <p v-if="member.community" class="text-sm text-gray-500">
                        {{ member.community.name }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div
                v-else-if="memberSearchQuery.length >= 2 && memberSearchResults.length === 0 && !isSearchingMembers"
                class="py-4 text-center text-gray-500"
              >
                No members found matching "{{ memberSearchQuery }}"
              </div>

              <div v-else-if="memberSearchQuery.length > 0 && memberSearchQuery.length < 2" class="py-4 text-center text-gray-500">
                Type at least 2 characters to search...
              </div>
            </div>

            <!-- Selected Member Display -->
            <div v-if="selectedMember && !showMemberSearch" class="rounded-lg bg-blue-50 p-4">
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="font-medium text-gray-900">
                    {{ selectedMember.first_name }} {{ selectedMember.middle_name }} {{ selectedMember.last_name }}
                  </h3>
                  <p class="text-sm text-gray-600">Member #{{ selectedMember.member_no }} | Family #{{ selectedMember.family_no }}</p>
                  <p v-if="selectedMember.community" class="text-sm text-gray-600">
                    {{ selectedMember.community.name }}
                  </p>
                  <div class="mt-2 text-xs text-gray-500">
                    <span v-if="selectedMember.baptism_date" class="mr-4"
                      >Baptized: {{ new Date(selectedMember.baptism_date).toLocaleDateString() }}</span
                    >
                    <span v-if="selectedMember.confirmation_date" class="mr-4"
                      >Confirmed: {{ new Date(selectedMember.confirmation_date).toLocaleDateString() }}</span
                    >
                    <span v-if="selectedMember.marriage_date" class="mr-4"
                      >Married: {{ new Date(selectedMember.marriage_date).toLocaleDateString() }}</span
                    >
                  </div>
                </div>
                <Button type="button" variant="outline" size="sm" @click="clearMemberSelection"> Change Member </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Certificate Type Selection -->
        <Card v-if="selectedMember">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <FileText class="h-5 w-5" />
              Certificate Details
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <!-- Certificate Type -->
            <div>
              <Label for="certificate_type">Certificate Type *</Label>
              <select
                v-model="form.certificate_type_id"
                id="certificate_type"
                required
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              >
                <option value="">Select certificate type</option>
                <option v-for="type in props.certificateTypes" :key="type.value" :value="type.value">
                  {{ type.label }}
                </option>
              </select>
              <p v-if="form.errors.certificate_type_id" class="mt-1 text-sm text-red-600">{{ form.errors.certificate_type_id }}</p>

              <!-- Duplicate Certificate Warning -->
              <div v-if="showDuplicateWarning" class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-md">
                <div class="flex items-start gap-2">
                  <AlertCircle class="h-5 w-5 text-amber-500 mt-0.5 flex-shrink-0" />
                  <div>
                    <p class="text-sm font-medium text-amber-800">Certificate Already Exists</p>
                    <p class="text-sm text-amber-700 mt-1">
                      This member already has a {{ props.certificateTypes.find(type => type.code === existingCertificateOfType?.certificate_type)?.label || existingCertificateOfType?.certificate_type }} certificate
                      ({{ existingCertificateOfType?.certificate_number }})
                      issued on {{ new Date(existingCertificateOfType?.issued_date || '').toLocaleDateString() }}.
                    </p>
                    <p class="text-sm text-amber-600 mt-1">
                      Generating a new certificate will create a duplicate. Are you sure you want to proceed?
                    </p>
                    <div class="mt-3">
                      <label class="flex items-center gap-2">
                        <input
                          v-model="form.force_duplicate"
                          type="checkbox"
                          class="rounded border-amber-300 text-amber-600 focus:ring-amber-500"
                        />
                        <span class="text-sm text-amber-800 font-medium">
                          Yes, force generate duplicate certificate
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Issued Date -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <Label for="issued_date">Issued Date *</Label>
                <DateInput v-model="form.issued_date" id="issued_date" class="w-full" />
                <p v-if="form.errors.issued_date" class="mt-1 text-sm text-red-600">{{ form.errors.issued_date }}</p>
              </div>
            </div>

            <!-- Validation Warning -->
            <div v-if="!memberValidation.valid" class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
              <div class="flex items-start gap-3">
                <AlertCircle class="mt-0.5 h-5 w-5 flex-shrink-0 text-yellow-600" />
                <div>
                  <h4 class="font-medium text-yellow-800">Missing Required Information</h4>
                  <p class="mt-1 text-sm text-yellow-700">{{ memberValidation.message }}</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Additional Information -->
        <Card v-if="form.certificate_type_id && Object.keys(additionalFields).length > 0">
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Users class="h-5 w-5" />
              Additional Information
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <!-- Baptism Fields -->
              <template v-if="currentCertificateTypeCode === 'baptism'">
                <div>
                  <Label for="baptism_date">Date of Baptism</Label>
                  <DateInput v-model="additionalFields.baptism_date" id="baptism_date" class="w-full" />
                </div>
                <div>
                  <Label for="date_of_birth">Date of Birth</Label>
                  <DateInput v-model="additionalFields.date_of_birth" id="date_of_birth" class="w-full" />
                </div>
                <div>
                  <Label for="place_of_birth">Place of Birth</Label>
                  <Input v-model="additionalFields.place_of_birth" type="text" id="place_of_birth" />
                </div>
                <div>
                  <Label for="father_name">Father's Name</Label>
                  <Input v-model="additionalFields.father_name" type="text" id="father_name" />
                </div>
                <div>
                  <Label for="mother_name">Mother's Name</Label>
                  <Input v-model="additionalFields.mother_name" type="text" id="mother_name" />
                </div>
                <div>
                  <Label for="father_residence">Father's Residence</Label>
                  <Input v-model="additionalFields.father_residence" type="text" id="father_residence" />
                </div>
                <div>
                  <Label for="father_profession">Father's Profession</Label>
                  <Input v-model="additionalFields.father_profession" type="text" id="father_profession" />
                </div>
                <div>
                  <Label for="nationality">Nationality</Label>
                  <Input v-model="additionalFields.nationality" type="text" id="nationality" />
                </div>
                <div>
                  <Label for="godfather_name">Godfather's Name</Label>
                  <Input v-model="additionalFields.godfather_name" type="text" id="godfather_name" />
                </div>
                <div>
                  <Label for="godfather_residence">Godfather's Residence</Label>
                  <Input v-model="additionalFields.godfather_residence" type="text" id="godfather_residence" />
                </div>
                <div>
                  <Label for="godmother_name">Godmother's Name</Label>
                  <Input v-model="additionalFields.godmother_name" type="text" id="godmother_name" />
                </div>
                <div>
                  <Label for="godmother_residence">Godmother's Residence</Label>
                  <Input v-model="additionalFields.godmother_residence" type="text" id="godmother_residence" />
                </div>
                <div>
                  <Label for="place_of_baptism">Place of Baptism</Label>
                  <Input v-model="additionalFields.place_of_baptism" type="text" id="place_of_baptism" />
                </div>
                <div>
                  <Label for="minister_name">Minister/Priest Name</Label>
                  <Input v-model="additionalFields.minister_name" type="text" id="minister_name" />
                </div>
                <div class="md:col-span-2">
                  <Label for="baptism_remarks">Remarks</Label>
                  <Textarea v-model="additionalFields.baptism_remarks" id="baptism_remarks" :rows="2" />
                </div>
              </template>

              <!-- Confirmation Fields -->
              <template v-if="currentCertificateTypeCode === 'confirmation'">
                <div>
                  <Label for="confirmation_name">Confirmation Name</Label>
                  <Input v-model="additionalFields.confirmation_name" type="text" id="confirmation_name" />
                </div>
                <div>
                  <Label for="sponsor_name">Sponsor Name</Label>
                  <Input v-model="additionalFields.sponsor_name" type="text" id="sponsor_name" />
                </div>
                <div>
                  <Label for="bishop_name">Bishop Name</Label>
                  <Input v-model="additionalFields.bishop_name" type="text" id="bishop_name" />
                </div>
                <div>
                  <Label for="priest_name">Priest Name</Label>
                  <Input v-model="additionalFields.priest_name" type="text" id="priest_name" />
                </div>
              </template>

              <!-- Marriage Fields -->
              <template v-if="currentCertificateTypeCode === 'marriage'">
                <!-- Bridegroom Details -->
                <div class="md:col-span-2">
                  <h4 class="font-semibold text-gray-900 mb-3">Bridegroom Details</h4>
                </div>
                <div>
                  <Label for="bridegroom_name">Bridegroom's Name</Label>
                  <Input v-model="additionalFields.bridegroom_name" type="text" id="bridegroom_name" />
                </div>
                <div>
                  <Label for="bridegroom_surname">Surname</Label>
                  <Input v-model="additionalFields.bridegroom_surname" type="text" id="bridegroom_surname" />
                </div>
                <div>
                  <Label for="bridegroom_dob">Date of Birth</Label>
                  <DateInput v-model="additionalFields.bridegroom_dob" id="bridegroom_dob" class="w-full" />
                </div>
                <div>
                  <Label for="bridegroom_nationality">Nationality</Label>
                  <Input v-model="additionalFields.bridegroom_nationality" type="text" id="bridegroom_nationality" />
                </div>
                <div>
                  <Label for="bridegroom_profession">Profession</Label>
                  <Input v-model="additionalFields.bridegroom_profession" type="text" id="bridegroom_profession" />
                </div>
                <div>
                  <Label for="bridegroom_residence">Residence</Label>
                  <Input v-model="additionalFields.bridegroom_residence" type="text" id="bridegroom_residence" />
                </div>
                <div>
                  <Label for="bridegroom_father_name">Father's Name</Label>
                  <Input v-model="additionalFields.bridegroom_father_name" type="text" id="bridegroom_father_name" />
                </div>
                <div>
                  <Label for="bridegroom_mother_name">Mother's Name</Label>
                  <Input v-model="additionalFields.bridegroom_mother_name" type="text" id="bridegroom_mother_name" />
                </div>
                <div>
                  <Label for="bridegroom_status">Status</Label>
                  <select
                    v-model="additionalFields.bridegroom_status"
                    id="bridegroom_status"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  >
                    <option value="Bachelor">Bachelor</option>
                    <option value="Widower">Widower</option>
                  </select>
                </div>
                <div>
                  <Label for="bridegroom_if_widower_whose">If Widower, Whose</Label>
                  <Input v-model="additionalFields.bridegroom_if_widower_whose" type="text" id="bridegroom_if_widower_whose" />
                </div>

                <!-- Bride Details -->
                <div class="md:col-span-2">
                  <h4 class="font-semibold text-gray-900 mb-3 mt-4">Bride Details</h4>
                </div>
                <div>
                  <Label for="bride_name">Bride's Name</Label>
                  <Input v-model="additionalFields.bride_name" type="text" id="bride_name" />
                </div>
                <div>
                  <Label for="bride_surname">Surname</Label>
                  <Input v-model="additionalFields.bride_surname" type="text" id="bride_surname" />
                </div>
                <div>
                  <Label for="bride_dob">Date of Birth</Label>
                  <DateInput v-model="additionalFields.bride_dob" id="bride_dob" class="w-full" />
                </div>
                <div>
                  <Label for="bride_nationality">Nationality</Label>
                  <Input v-model="additionalFields.bride_nationality" type="text" id="bride_nationality" />
                </div>
                <div>
                  <Label for="bride_profession">Profession</Label>
                  <Input v-model="additionalFields.bride_profession" type="text" id="bride_profession" />
                </div>
                <div>
                  <Label for="bride_residence">Residence</Label>
                  <Input v-model="additionalFields.bride_residence" type="text" id="bride_residence" />
                </div>
                <div>
                  <Label for="bride_father_name">Father's Name</Label>
                  <Input v-model="additionalFields.bride_father_name" type="text" id="bride_father_name" />
                </div>
                <div>
                  <Label for="bride_mother_name">Mother's Name</Label>
                  <Input v-model="additionalFields.bride_mother_name" type="text" id="bride_mother_name" />
                </div>
                <div>
                  <Label for="bride_status">Status</Label>
                  <select
                    v-model="additionalFields.bride_status"
                    id="bride_status"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  >
                    <option value="Spinster">Spinster</option>
                    <option value="Widow">Widow</option>
                  </select>
                </div>
                <div>
                  <Label for="bride_if_widow_whose">If Widow, Whose</Label>
                  <Input v-model="additionalFields.bride_if_widow_whose" type="text" id="bride_if_widow_whose" />
                </div>

                <!-- Witnesses and Minister -->
                <div class="md:col-span-2">
                  <h4 class="font-semibold text-gray-900 mb-3 mt-4">Witnesses & Minister</h4>
                </div>
                <div>
                  <Label for="first_witness_name">First Witness' Name</Label>
                  <Input v-model="additionalFields.first_witness_name" type="text" id="first_witness_name" />
                </div>
                <div>
                  <Label for="first_witness_residence">First Witness' Residence</Label>
                  <Input v-model="additionalFields.first_witness_residence" type="text" id="first_witness_residence" />
                </div>
                <div>
                  <Label for="second_witness_name">Second Witness' Name</Label>
                  <Input v-model="additionalFields.second_witness_name" type="text" id="second_witness_name" />
                </div>
                <div>
                  <Label for="second_witness_residence">Second Witness' Residence</Label>
                  <Input v-model="additionalFields.second_witness_residence" type="text" id="second_witness_residence" />
                </div>
                <div>
                  <Label for="minister_name">Minister/Priest Name</Label>
                  <Input v-model="additionalFields.minister_name" type="text" id="minister_name" />
                </div>
                <div class="md:col-span-2">
                  <Label for="marriage_remarks">Remarks</Label>
                  <Textarea v-model="additionalFields.marriage_remarks" id="marriage_remarks" :rows="2" />
                </div>
              </template>

              <!-- Death Fields -->
              <template v-if="currentCertificateTypeCode === 'death'">
                <div>
                  <Label for="death_date">Date of Death</Label>
                  <DateInput v-model="additionalFields.death_date" id="death_date" class="w-full" />
                </div>
                <div>
                  <Label for="burial_date">Date of Burial</Label>
                  <DateInput v-model="additionalFields.burial_date" id="burial_date" class="w-full" />
                </div>
                <div>
                  <Label for="deceased_name">Deceased Name</Label>
                  <Input v-model="additionalFields.deceased_name" type="text" id="deceased_name" />
                </div>
                <div>
                  <Label for="deceased_surname">Deceased Surname</Label>
                  <Input v-model="additionalFields.deceased_surname" type="text" id="deceased_surname" />
                </div>
                <div>
                  <Label for="relationship">Relationship</Label>
                  <Input v-model="additionalFields.relationship" type="text" id="relationship" placeholder="e.g., Husband of, Wife of, Son of" />
                </div>
                <div>
                  <Label for="residence">Residence</Label>
                  <Input v-model="additionalFields.residence" type="text" id="residence" />
                </div>
                <div>
                  <Label for="age">Age (Years)</Label>
                  <Input v-model="additionalFields.age" type="number" id="age" />
                </div>
                <div>
                  <Label for="nationality">Nationality</Label>
                  <Input v-model="additionalFields.nationality" type="text" id="nationality" />
                </div>
                <div>
                  <Label for="cause_of_death">Cause of Death</Label>
                  <Input v-model="additionalFields.cause_of_death" type="text" id="cause_of_death" />
                </div>
                <div>
                  <Label for="place_of_burial">Place of Burial</Label>
                  <Input v-model="additionalFields.place_of_burial" type="text" id="place_of_burial" />
                </div>
                <div>
                  <Label for="minister_name">Minister/Priest Name</Label>
                  <Input v-model="additionalFields.minister_name" type="text" id="minister_name" />
                </div>
                <div class="md:col-span-2">
                  <Label for="death_remarks">Remarks</Label>
                  <Textarea v-model="additionalFields.death_remarks" id="death_remarks" :rows="2" />
                </div>
              </template>

              <!-- Membership Fields -->
              <template v-if="currentCertificateTypeCode === 'membership'">
                <div>
                  <Label for="join_date">Join Date</Label>
                  <DateInput v-model="additionalFields.join_date" id="join_date" class="w-full" />
                </div>
                <div>
                  <Label for="community_name">Community Name</Label>
                  <Input v-model="additionalFields.community_name" type="text" id="community_name" />
                </div>
                <div>
                  <Label for="family_number">Family Number</Label>
                  <Input v-model="additionalFields.family_number" type="text" id="family_number" />
                </div>
              </template>
            </div>
          </CardContent>
        </Card>

        <!-- Actions -->
        <div class="flex justify-end gap-4">
          <Button type="button" variant="outline" @click="() => { const savedFilters = getState(); router.get('/certificates', savedFilters, { preserveState: true, preserveScroll: true }); }"> Cancel </Button>

          <Button
            type="button"
            variant="outline"
            @click="previewCertificate"
            :disabled="!form.certificate_type_id || !memberValidation.valid || form.processing"
          >
            Preview
          </Button>

          <Button
            type="submit"
            :disabled="!form.certificate_type_id || !memberValidation.valid || form.processing || (showDuplicateWarning && !form.force_duplicate)"
            class="bg-green-600 text-white hover:bg-green-700"
          >
            <span v-if="form.processing">Generating...</span>
            <span v-else>Generate Certificate</span>
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
