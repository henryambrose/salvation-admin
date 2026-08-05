<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { DateInput } from '@/components/ui/date-input';
import SearchDropdown from '@/components/ui/searchDropdown/SearchDropdown.vue';
import { formatDateForDisplay as formatDateDisplay } from '@/lib/utils';

const props = defineProps<{
  parishes: any[];
  member?: any | null;
}>();

// Track selected member's date of birth and marriage info
const selectedMemberDateOfBirth = ref<string | null>(null);
const selectedMemberMarriageDate = ref<string | null>(null);
const selectedMemberMarriageRegNo = ref<string>('');

// Get father's and mother's address
const getFatherAddress = () => {
  if (!props.member?.father) return '';
  const father = props.member.father;
  const parts = [
    father.permanent_add1,
    father.permanent_add2,
    father.permanent_add3,
    father.permanent_town?.name,
    father.permanent_city?.name,
    father.permanent_state?.name,
    father.permanent_country?.name,
    father.permanent_pincode,
  ].filter(Boolean);
  return parts.join(', ');
};

const form = useForm({
  member_id: props.member?.id ?? undefined,
  baptized_name: props.member?.first_name ?? '',
  baptized_surname: props.member?.last_name ?? '',
  baptism_date: props.member?.baptismRecord?.baptism_date ?? props.member?.baptism_date ?? '',
  baptism_reg_no: props.member?.baptismRecord?.baptism_reg_no ?? props.member?.baptism_reg_no ?? '',
  place_of_baptism: props.member?.baptismRecord?.place_of_baptism ?? '',
  baptism_parish_id: props.member?.baptism_parish_id ?? undefined,
  place_of_birth: props.member?.baptismRecord?.place_of_birth ?? '',
  nationality: props.member?.baptismRecord?.nationality ?? '',
  father_name: props.member?.father ? `${props.member.father.first_name ?? ''} ${props.member.father.last_name ?? ''}`.trim() : '',
  father_residence: getFatherAddress(),
  father_profession: props.member?.father?.company_name ?? '',
  mother_name: props.member?.mother ? `${props.member.mother.first_name ?? ''} ${props.member.mother.last_name ?? ''}`.trim() : '',
  godfather_name: '',
  godfather_residence: '',
  godmother_name: '',
  godmother_residence: '',
  minister_name: '',
  baptism_remarks: '',
  birth_date_text: '',
  baptism_reg_year: '',
  confirmation_date: '',
  confirmation: '',
});

// Initialize member data on mount
onMounted(() => {
  if (props.member) {
    selectedMemberDateOfBirth.value = props.member.date_of_birth ?? null;
    selectedMemberMarriageDate.value = props.member.marriageRecord?.marriage_date ?? props.member.marriage_date ?? null;
    selectedMemberMarriageRegNo.value = props.member.marriageRecord?.marriage_reg_no ?? props.member.marriage_reg_no ?? '';
  }
});

// Format date to DD/MM/YYYY for display
const formatDateForDisplay = (dateString: string) => {
  if (!dateString) return '';
  return formatDateDisplay(dateString);
};

// Computed property for displaying member's date of birth
const displayDateOfBirth = computed(() => {
  if (!selectedMemberDateOfBirth.value) return 'Not set';
  return formatDateForDisplay(selectedMemberDateOfBirth.value);
});

// Computed property for pre-selected member option
const memberOptions = computed(() => {
  if (!props.member) return [];
  const communityName = props.member.community?.name ?? 'N/A';
  const memberNo = props.member.member_no ?? 'N/A';
  
  // Get address
  const address = [
    props.member.permanent_add1,
    props.member.permanent_add2,
    props.member.permanent_add3,
  ].filter(Boolean).join(', ');
  
  return [{
    id: props.member.id,
    name: `${props.member.first_name ?? ''} ${props.member.last_name ?? ''} - ${communityName} - ${memberNo}`.trim(),
    first_name: props.member.first_name,
    middle_name: props.member.middle_name,
    last_name: props.member.last_name,
    member_no: props.member.member_no,
    family_no: props.member.family_no,
    date_of_birth: props.member.date_of_birth,
    gender_id: props.member.gender_id,
    address: address,
    nationality: 'Indian',
    baptism_date: props.member.baptismRecord?.baptism_date ?? props.member.baptism_date,
    baptism_reg_no: props.member.baptismRecord?.baptism_reg_no ?? props.member.baptism_reg_no,
    baptism_parish_id: props.member.baptism_parish_id,
    place_of_baptism: props.member.baptismRecord?.place_of_baptism,
    place_of_birth: props.member.baptismRecord?.place_of_birth,
    marriage_date: props.member.marriageRecord?.marriage_date ?? props.member.marriage_date,
    marriage_reg_no: props.member.marriageRecord?.marriage_reg_no ?? props.member.marriage_reg_no,
    marriage_parish_id: props.member.marriage_parish_id,
    father_name: props.member.father ? `${props.member.father.first_name ?? ''} ${props.member.father.last_name ?? ''}`.trim() : null,
    father_profession: props.member.father?.company_name,
    mother_name: props.member.mother ? `${props.member.mother.first_name ?? ''} ${props.member.mother.last_name ?? ''}`.trim() : null,
    spouse_name: props.member.spouse ? `${props.member.spouse.first_name ?? ''} ${props.member.spouse.last_name ?? ''}`.trim() : null,
  }];
});

// Handle member selection from SearchDropdown
function handleMemberSelect(member: any) {
  // Auto-populate baptism fields from member data
  form.baptized_name = member.first_name || '';
  form.baptized_surname = member.last_name || '';
  form.baptism_date = member.baptism_date || '';
  form.baptism_reg_no = member.baptism_reg_no || '';
  form.baptism_parish_id = member.baptism_parish_id ?? undefined;
  form.place_of_baptism = member.place_of_baptism || '';
  form.place_of_birth = member.place_of_birth || '';
  form.nationality = member.nationality || 'Indian';
  
  // Populate father info
  form.father_name = member.father_name || '';
  form.father_profession = member.father_profession || '';
  form.father_residence = member.address || '';
  
  // Populate mother info
  form.mother_name = member.mother_name || '';
  
  // Track member's date of birth and marriage info for display
  selectedMemberDateOfBirth.value = member.date_of_birth ?? null;
  selectedMemberMarriageDate.value = member.marriage_date ?? null;
  selectedMemberMarriageRegNo.value = member.marriage_reg_no ?? '';
}

// Computed property to check if form can be submitted
const canSubmit = computed(() => {
  return form.baptism_date && !form.processing;
});

function submit() {
  if (!canSubmit.value) return;

  form.post('/baptism-records', {
    preserveScroll: true,
    onSuccess: () => router.visit('/baptism-records'),
  });
}
</script>

<template>
  <AppLayout title="Create Baptism Record">
    <Head title="Create Baptism Record" />

    <div class="p-6">
      <h1 class="mb-6 text-2xl font-bold">Create Baptism Record</h1>

      <form @submit.prevent="submit" class="max-w-4xl space-y-6 rounded-lg bg-white p-6 shadow">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <Label>Member</Label>
            <SearchDropdown
              v-model="form.member_id"
              :options="memberOptions"
              fetch-url="/member/search"
              placeholder="Search member by name..."
              @select="handleMemberSelect"
            />
            <p class="mt-1 text-xs text-muted-foreground">
              Optional - leave empty for non-members
            </p>
          </div>
          <div>
            <Label>Date of Birth</Label>
            <DateInput
              v-model="selectedMemberDateOfBirth"
              placeholder="DD/MM/YYYY"
            />
            <p class="mt-1 text-xs text-muted-foreground">
              From member record (can be edited)
            </p>
          </div>
          <div>
            <Label>Baptized Name</Label>
            <Input
              v-model="form.baptized_name"
              placeholder="Enter baptized name"
            />
            <p class="mt-1 text-xs text-muted-foreground">
              Optional - for non-members or different from legal name
            </p>
          </div>
          <div>
            <Label>Baptized Surname</Label>
            <Input
              v-model="form.baptized_surname"
              placeholder="Enter baptized surname"
            />
            <p class="mt-1 text-xs text-muted-foreground">
              Optional - for non-members or different from legal name
            </p>
          </div>
          <div>
            <Label>Baptism Date *</Label>
            <DateInput
              v-model="form.baptism_date"
              placeholder="DD/MM/YYYY"
            />
            <p v-if="!form.baptism_date" class="mt-1 text-sm text-muted-foreground">
              Auto-filled from member data (can be edited)
            </p>
          </div>
          <div>
            <Label>Birth Date Text</Label>
            <Input
              v-model="form.birth_date_text"
              placeholder="Enter birth date text"
            />
          </div>
          <div>
            <Label>Reg Year</Label>
            <Input
              v-model="form.baptism_reg_year"
              placeholder="Enter registration year"
            />
          </div>
          <div>
            <Label>Marriage Date</Label>
            <DateInput
              v-model="selectedMemberMarriageDate"
              placeholder="DD/MM/YYYY"
            />
            <p class="mt-1 text-xs text-muted-foreground">
              From member record (can be edited)
            </p>
          </div>
          <div>
            <Label>Marriage Reg No</Label>
            <Input
              v-model="selectedMemberMarriageRegNo"
              placeholder="Enter marriage registration number"
            />
            <p class="mt-1 text-xs text-muted-foreground">
              From member record (can be edited)
            </p>
          </div>
          <div>
            <Label>Baptism Reg No</Label>
            <Input
              v-model="form.baptism_reg_no"
              placeholder="Enter baptism registration number"
            />
          </div>
          <div>
            <Label>Place of Baptism</Label>
            <Input v-model="form.place_of_baptism" />
          </div>
          <div>
            <Label>Place of Birth</Label>
            <Input v-model="form.place_of_birth" />
          </div>
          <div>
            <Label>Nationality</Label>
            <Input v-model="form.nationality" />
          </div>
          <div>
            <Label>Father Name</Label>
            <Input v-model="form.father_name" />
          </div>
          <div>
            <Label>Father Profession</Label>
            <Input v-model="form.father_profession" />
          </div>
          <div class="md:col-span-2">
            <Label>Father Residence</Label>
            <Textarea v-model="form.father_residence" />
          </div>
          <div>
            <Label>Mother Name</Label>
            <Input v-model="form.mother_name" />
          </div>
          <div>
            <Label>Godfather Name</Label>
            <Input v-model="form.godfather_name" />
          </div>
          <div class="md:col-span-2">
            <Label>Godfather Residence</Label>
            <Textarea v-model="form.godfather_residence" />
          </div>
          <div>
            <Label>Godmother Name</Label>
            <Input v-model="form.godmother_name" />
          </div>
          <div class="md:col-span-2">
            <Label>Godmother Residence</Label>
            <Textarea v-model="form.godmother_residence" />
          </div>
          <!-- <div>
            <Label>Confirmation Date</Label>
            <DateInput
              v-model="form.confirmation_date"
              placeholder="DD/MM/YYYY"
            />
          </div> -->
          <div>
            <Label>Confirmed By</Label>
            <Input
              v-model="form.confirmation"
              placeholder="Enter confirmed by"
            />
          </div>
          <div>
            <Label>Minister Name</Label>
            <Input v-model="form.minister_name" />
          </div>
          <div class="md:col-span-2">
            <Label>Remarks</Label>
            <Textarea v-model="form.baptism_remarks" />
          </div>
        </div>

        <div class="space-y-2">
          <div v-if="!form.baptism_date" class="text-sm text-amber-600">
            Please select a member with a baptism date to save the record
          </div>
          <div class="flex gap-4">
            <Button type="submit" :disabled="!canSubmit">
              {{ form.processing ? 'Saving...' : 'Save Baptism Record' }}
            </Button>
            <Button type="button" variant="outline" @click="router.visit('/baptism-records')">
              Cancel
            </Button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
