<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { AlertCircle, FileText, Search, User, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useCertificateState } from '@/composables/useCertificateState';

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
  death_date?: string;
  baptism_reg_no?: string;
  confirmation_reg_no?: string;
  marriage_reg_no?: string;
  deaths_reg_no?: string;
  community?: { name: string };
  parish?: { name: string };
}

interface CertificateTemplate {
  id: number;
  name: string;
  description?: string;
  certificate_type_id: number;
  certificate_type?: {
    id: number;
    name: string;
    code: string;
  };
}

const props = defineProps<{
  member?: Member;
  templates: CertificateTemplate[];
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
  template_id: '',
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
        godfather_name: '',
        godmother_name: '',
        priest_name: '',
        witnesses: '',
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
        spouse_name: '',
        witness1_name: '',
        witness2_name: '',
        priest_name: '',
        marriage_type: 'Catholic Marriage',
      };
    } else if (newType === 'membership') {
      additionalFields.value = {
        join_date: selectedMember.value?.date_of_birth || '',
        community_name: selectedMember.value?.community?.name || '',
        family_number: selectedMember.value?.family_no || '',
      };
    } else if (newType === 'death') {
      additionalFields.value = {
        burial_date: '',
        burial_place: '',
        priest_name: '',
        last_rites_given: 'Yes',
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

// Available templates for selected certificate type
const availableTemplates = computed(() => {
  if (!form.certificate_type_id) return [];

  // Keep console logs for debugging
  console.log('Debug - Selected certificate type ID:', form.certificate_type_id);
  console.log('Debug - All templates:', props.templates);

  const filtered = props.templates.filter((template) => template.certificate_type_id === form.certificate_type_id);
  console.log('Debug - Filtered templates:', filtered);
  return filtered;
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
  if (type === 'death' && !member.death_date) {
    return { valid: false, message: 'Member does not have a death date recorded.' };
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
  form.template_id = '';

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
  form.template_id = '';
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
      alert('Failed to generate preview: ' + error.message);
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
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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

              <!-- Template Selection -->
              <div v-if="form.certificate_type_id">
                <Label for="template_id">Template</Label>
                <select
                  v-model="form.template_id"
                  id="template_id"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                  <option value="">Default Template</option>
                  <option v-for="template in availableTemplates" :key="template.id" :value="template.id.toString()">
                    {{ template.name }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Issued Date -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <Label for="issued_date">Issued Date *</Label>
                <Input v-model="form.issued_date" type="date" id="issued_date" required />
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
                  <Label for="godfather_name">Godfather Name</Label>
                  <Input v-model="additionalFields.godfather_name" type="text" id="godfather_name" />
                </div>
                <div>
                  <Label for="godmother_name">Godmother Name</Label>
                  <Input v-model="additionalFields.godmother_name" type="text" id="godmother_name" />
                </div>
                <div>
                  <Label for="priest_name">Priest Name</Label>
                  <Input v-model="additionalFields.priest_name" type="text" id="priest_name" />
                </div>
                <div>
                  <Label for="witnesses">Witnesses</Label>
                  <Textarea v-model="additionalFields.witnesses" id="witnesses" :rows="2" />
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
                <div>
                  <Label for="spouse_name">Spouse Name *</Label>
                  <Input v-model="additionalFields.spouse_name" type="text" id="spouse_name" required />
                </div>
                <div>
                  <Label for="witness1_name">First Witness</Label>
                  <Input v-model="additionalFields.witness1_name" type="text" id="witness1_name" />
                </div>
                <div>
                  <Label for="witness2_name">Second Witness</Label>
                  <Input v-model="additionalFields.witness2_name" type="text" id="witness2_name" />
                </div>
                <div>
                  <Label for="priest_name">Priest Name</Label>
                  <Input v-model="additionalFields.priest_name" type="text" id="priest_name" />
                </div>
                <div class="md:col-span-2">
                  <Label for="marriage_type">Marriage Type</Label>
                  <select
                    v-model="additionalFields.marriage_type"
                    id="marriage_type"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  >
                    <option value="Catholic Marriage">Catholic Marriage</option>
                    <option value="Mixed Marriage">Mixed Marriage</option>
                    <option value="Convalidation">Convalidation</option>
                  </select>
                </div>
              </template>

              <!-- Death Fields -->
              <template v-if="currentCertificateTypeCode === 'death'">
                <div>
                  <Label for="burial_date">Burial Date</Label>
                  <Input v-model="additionalFields.burial_date" type="date" id="burial_date" :max="new Date().toISOString().split('T')[0]" />
                </div>
                <div>
                  <Label for="burial_place">Burial Place</Label>
                  <Input v-model="additionalFields.burial_place" type="text" id="burial_place" />
                </div>
                <div>
                  <Label for="priest_name">Priest Name</Label>
                  <Input v-model="additionalFields.priest_name" type="text" id="priest_name" />
                </div>
                <div>
                  <Label for="last_rites_given">Last Rites Given</Label>
                  <select
                    v-model="additionalFields.last_rites_given"
                    id="last_rites_given"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  >
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="Unknown">Unknown</option>
                  </select>
                </div>
              </template>

              <!-- Membership Fields -->
              <template v-if="currentCertificateTypeCode === 'membership'">
                <div>
                  <Label for="join_date">Join Date</Label>
                  <Input v-model="additionalFields.join_date" type="date" id="join_date" />
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
