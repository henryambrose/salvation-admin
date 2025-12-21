<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import axios from 'axios';
import { AlertCircle, ArrowLeft, ArrowRight, CheckCircle, Eye, FileText, Search, User, Users } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';

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
  community?: { name: string };
}

interface CertificateTemplate {
  id: number;
  name: string;
  type: string;
  description?: string;
  is_default: boolean;
}

interface CertificateType {
  value: string;
  label: string;
}

const props = defineProps<{
  preSelectedMemberId?: number | null;
  preSelectedType?: string | null;
}>();

const emit = defineEmits<{
  generated: [certificate: any];
  cancel: [];
}>();

// Wizard steps
const currentStep = ref(0);
const steps = [
  { title: 'Select Member', description: 'Choose the member for certificate generation' },
  { title: 'Certificate Type', description: 'Select the type of certificate to generate' },
  { title: 'Choose Template', description: 'Pick a template for the certificate' },
  { title: 'Review & Generate', description: 'Review details and generate certificate' },
];

// Step 1: Member Selection
const memberSearch = ref('');
const searchResults = ref<Member[]>([]);
const selectedMember = ref<Member | null>(null);
const isSearching = ref(false);

// Step 2: Certificate Type Selection
const selectedType = ref('');
const availableTypes = ref<string[]>([]);
const certificateTypes: CertificateType[] = [
  { value: 'baptism', label: 'Baptism Certificate' },
  { value: 'confirmation', label: 'Confirmation Certificate' },
  { value: 'marriage', label: 'Marriage Certificate' },
  { value: 'membership', label: 'Membership Certificate' },
  { value: 'death', label: 'Death Certificate' },
];

// Step 3: Template Selection
const selectedTemplate = ref<CertificateTemplate | null>(null);
const availableTemplates = ref<CertificateTemplate[]>([]);

// Step 4: Generation
const notes = ref('');
const isGenerating = ref(false);
const previewData = ref<any>(null);

// Computed properties
const canProceedStep1 = computed(() => selectedMember.value !== null);
const canProceedStep2 = computed(() => selectedType.value !== '');
const canProceedStep3 = computed(() => selectedTemplate.value !== null);

const filteredCertificateTypes = computed(() => {
  if (!availableTypes.value.length) return [];
  return certificateTypes.filter((type) => availableTypes.value.includes(type.value));
});

const filteredTemplates = computed(() => {
  if (!selectedType.value) return [];
  return availableTemplates.value.filter((template) => template.type === selectedType.value);
});

// Watch for member search
let searchTimeout: number;
watch(memberSearch, (newValue) => {
  if (newValue.length < 2) {
    searchResults.value = [];
    return;
  }

  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(async () => {
    await searchMembers();
  }, 300);
});

// Watch for member selection to load available types
watch(selectedMember, async (member) => {
  if (member) {
    await loadAvailableTypes();
  }
});

// Watch for type selection to load templates
watch(selectedType, async (type) => {
  if (type) {
    await loadTemplates();
  }
});

// Methods
const searchMembers = async () => {
  if (memberSearch.value.length < 2) return;

  isSearching.value = true;
  try {
    const response = await axios.get(route('certificates.search-members'), {
      params: { query: memberSearch.value },
    });
    searchResults.value = response.data;
  } catch (error) {
    console.error('Error searching members:', error);
  } finally {
    isSearching.value = false;
  }
};

const selectMember = (member: Member) => {
  selectedMember.value = member;
  memberSearch.value = `${member.first_name} ${member.last_name} (${member.member_no})`;
  searchResults.value = [];
};

const loadAvailableTypes = async () => {
  if (!selectedMember.value) return;

  try {
    const response = await axios.get(`/certificates/api/members/${selectedMember.value.id}/available-types`);
    availableTypes.value = response.data;
  } catch (error) {
    console.error('Error loading available types:', error);
    // Fallback: assume all types are available
    availableTypes.value = ['baptism', 'confirmation', 'marriage', 'membership', 'death'];
  }
};

const loadTemplates = async () => {
  if (!selectedType.value) return;

  try {
    const response = await axios.get('/certificates/api/templates', {
      params: { type: selectedType.value },
    });
    availableTemplates.value = response.data;

    // Auto-select default template if available
    const defaultTemplate = availableTemplates.value.find((t) => t.is_default);
    if (defaultTemplate) {
      selectedTemplate.value = defaultTemplate;
    }
  } catch (error) {
    console.error('Error loading templates:', error);
  }
};

const nextStep = () => {
  if (currentStep.value < steps.length - 1) {
    currentStep.value++;
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

const generateCertificate = async () => {
  if (!selectedMember.value || !selectedType.value || !selectedTemplate.value) return;

  isGenerating.value = true;
  try {
    const response = await axios.post(route('certificates.store'), {
      member_id: selectedMember.value.id,
      certificate_type_id: selectedType.value,
      template_id: selectedTemplate.value.id,
      notes: notes.value,
    });

    emit('generated', response.data);
  } catch (error) {
    console.error('Error generating certificate:', error);
  } finally {
    isGenerating.value = false;
  }
};

const previewCertificate = async () => {
  if (!selectedMember.value || !selectedType.value || !selectedTemplate.value) return;

  try {
    const response = await axios.post(route('certificates.preview'), {
      member_id: selectedMember.value.id,
      certificate_type_id: selectedType.value,
      template_id: selectedTemplate.value.id,
      notes: notes.value,
    });

    // Open preview in new tab
    const blob = new Blob([atob(response.data.pdf)], { type: 'application/pdf' });
    const url = URL.createObjectURL(blob);
    window.open(url, '_blank');
  } catch (error) {
    console.error('Error previewing certificate:', error);
  }
};

const cancel = () => {
  emit('cancel');
};

// Handle pre-selection on mount
onMounted(async () => {
  // Pre-select member if member ID is provided
  if (props.preSelectedMemberId) {
    try {
      // Fetch member details
      const response = await axios.get(`/certificates/api/members/${props.preSelectedMemberId}`);
      const member = response.data;
      selectedMember.value = member;
      memberSearch.value = `${member.first_name} ${member.last_name} (${member.member_no})`;

      // Load available types for this member
      await loadAvailableTypes();
    } catch (error) {
      console.error('Error loading pre-selected member:', error);
    }
  }

  // Pre-select type if provided and available
  if (props.preSelectedType && availableTypes.value.includes(props.preSelectedType)) {
    selectedType.value = props.preSelectedType;
    await loadTemplates();
  }
});
</script>

<template>
  <div class="space-y-6">
    <!-- Progress indicator -->
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <template v-for="(step, index) in steps" :key="index">
          <div
            :class="[
              'flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium transition-colors',
              currentStep >= index ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600',
            ]"
          >
            <CheckCircle v-if="currentStep > index" class="h-4 w-4" />
            <span v-else>{{ index + 1 }}</span>
          </div>
          <div v-if="index < steps.length - 1" :class="['h-0.5 w-12 transition-colors', currentStep > index ? 'bg-blue-600' : 'bg-gray-200']" />
        </template>
      </div>
      <Button variant="outline" @click="cancel">Cancel</Button>
    </div>

    <!-- Step title and description -->
    <div class="text-center">
      <h3 class="text-lg font-semibold">{{ steps[currentStep].title }}</h3>
      <p class="text-sm text-gray-600">{{ steps[currentStep].description }}</p>
    </div>

    <!-- Step content -->
    <div class="min-h-[400px]">
      <!-- Step 1: Member Selection -->
      <div v-if="currentStep === 0" class="space-y-4">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Search class="h-4 w-4" />
              Search Member
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="relative">
              <Input v-model="memberSearch" placeholder="Search by name, member number, or family number..." class="pr-10" />
              <Search class="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
            </div>

            <!-- Search Results -->
            <div v-if="searchResults.length > 0" class="max-h-60 overflow-y-auto rounded-lg border">
              <div
                v-for="member in searchResults"
                :key="member.id"
                class="cursor-pointer border-b p-3 transition-colors last:border-b-0 hover:bg-gray-50"
                @click="selectMember(member)"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium">{{ member.first_name }} {{ member.middle_name }} {{ member.last_name }}</p>
                    <p class="text-sm text-gray-600">Member: {{ member.member_no }} | Family: {{ member.family_no }}</p>
                    <p v-if="member.community" class="text-xs text-gray-500">{{ member.community.name }}</p>
                  </div>
                  <User class="h-4 w-4 text-gray-400" />
                </div>
              </div>
            </div>

            <!-- Selected Member -->
            <div v-if="selectedMember" class="rounded-lg border bg-blue-50 p-4">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                  <User class="h-5 w-5 text-blue-600" />
                </div>
                <div>
                  <p class="font-medium text-blue-900">
                    {{ selectedMember.first_name }} {{ selectedMember.middle_name }} {{ selectedMember.last_name }}
                  </p>
                  <p class="text-sm text-blue-700">Member: {{ selectedMember.member_no }} | Family: {{ selectedMember.family_no }}</p>
                </div>
              </div>
            </div>

            <!-- No results message -->
            <div v-if="memberSearch.length >= 2 && searchResults.length === 0 && !isSearching" class="py-8 text-center text-gray-500">
              <Users class="mx-auto mb-2 h-8 w-8 text-gray-300" />
              <p>No members found matching your search.</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Step 2: Certificate Type Selection -->
      <div v-if="currentStep === 1" class="space-y-4">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <FileText class="h-4 w-4" />
              Select Certificate Type
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
              <div
                v-for="type in filteredCertificateTypes"
                :key="type.value"
                :class="[
                  'cursor-pointer rounded-lg border p-4 transition-all',
                  selectedType === type.value ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300',
                ]"
                @click="selectedType = type.value"
              >
                <div class="flex items-center justify-between">
                  <span class="font-medium">{{ type.label }}</span>
                  <div
                    :class="[
                      'h-4 w-4 rounded-full border-2 transition-colors',
                      selectedType === type.value ? 'border-blue-500 bg-blue-500' : 'border-gray-300',
                    ]"
                  >
                    <CheckCircle v-if="selectedType === type.value" class="h-3 w-3 text-white" />
                  </div>
                </div>
              </div>
            </div>

            <div v-if="filteredCertificateTypes.length === 0" class="py-8 text-center text-gray-500">
              <AlertCircle class="mx-auto mb-2 h-8 w-8 text-gray-300" />
              <p>No certificate types available for this member.</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Step 3: Template Selection -->
      <div v-if="currentStep === 2" class="space-y-4">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <FileText class="h-4 w-4" />
              Choose Template
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-1 gap-3">
              <div
                v-for="template in filteredTemplates"
                :key="template.id"
                :class="[
                  'cursor-pointer rounded-lg border p-4 transition-all',
                  selectedTemplate?.id === template.id ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 hover:border-gray-300',
                ]"
                @click="selectedTemplate = template"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium">{{ template.name }}</p>
                    <p v-if="template.description" class="text-sm text-gray-600">{{ template.description }}</p>
                    <Badge v-if="template.is_default" variant="secondary" class="mt-1">Default</Badge>
                  </div>
                  <div
                    :class="[
                      'h-4 w-4 rounded-full border-2 transition-colors',
                      selectedTemplate?.id === template.id ? 'border-blue-500 bg-blue-500' : 'border-gray-300',
                    ]"
                  >
                    <CheckCircle v-if="selectedTemplate?.id === template.id" class="h-3 w-3 text-white" />
                  </div>
                </div>
              </div>
            </div>

            <div v-if="filteredTemplates.length === 0" class="py-8 text-center text-gray-500">
              <FileText class="mx-auto mb-2 h-8 w-8 text-gray-300" />
              <p>No templates available for this certificate type.</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Step 4: Review & Generate -->
      <div v-if="currentStep === 3" class="space-y-4">
        <Card>
          <CardHeader>
            <CardTitle>Review Certificate Details</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <!-- Member Details -->
            <div class="rounded-lg bg-gray-50 p-4">
              <h4 class="mb-2 font-medium">Member Details</h4>
              <p><strong>Name:</strong> {{ selectedMember?.first_name }} {{ selectedMember?.middle_name }} {{ selectedMember?.last_name }}</p>
              <p><strong>Member Number:</strong> {{ selectedMember?.member_no }}</p>
              <p><strong>Family Number:</strong> {{ selectedMember?.family_no }}</p>
            </div>

            <!-- Certificate Details -->
            <div class="rounded-lg bg-gray-50 p-4">
              <h4 class="mb-2 font-medium">Certificate Details</h4>
              <p><strong>Type:</strong> {{ certificateTypes.find((t) => t.value === selectedType)?.label }}</p>
              <p><strong>Template:</strong> {{ selectedTemplate?.name }}</p>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
              <Label for="notes">Additional Notes (Optional)</Label>
              <Textarea id="notes" v-model="notes" placeholder="Enter any additional notes for this certificate..." :rows="3" />
            </div>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Navigation buttons -->
    <div class="flex justify-between">
      <Button v-if="currentStep > 0" variant="outline" @click="prevStep">
        <ArrowLeft class="mr-2 h-4 w-4" />
        Previous
      </Button>
      <div v-else></div>

      <div class="flex gap-2">
        <Button v-if="currentStep === 3" variant="outline" @click="previewCertificate">
          <Eye class="mr-2 h-4 w-4" />
          Preview
        </Button>

        <Button
          v-if="currentStep < 3"
          @click="nextStep"
          :disabled="(currentStep === 0 && !canProceedStep1) || (currentStep === 1 && !canProceedStep2) || (currentStep === 2 && !canProceedStep3)"
        >
          Next
          <ArrowRight class="ml-2 h-4 w-4" />
        </Button>

        <Button v-if="currentStep === 3" @click="generateCertificate" :disabled="isGenerating">
          {{ isGenerating ? 'Generating...' : 'Generate Certificate' }}
        </Button>
      </div>
    </div>
  </div>
</template>
