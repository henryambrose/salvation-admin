<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { DateInput } from '@/components/ui/date-input';
import SearchDropdown from '@/components/ui/searchDropdown/SearchDropdown.vue';

const props = defineProps<{
  baptismRecord: any;
  parishes: any[];
}>();

// Debug: Log props on mount
console.log('Edit Baptism Record - Props:', {
  baptismRecord: props.baptismRecord,
  member: props.baptismRecord?.member,
  member_dob: props.baptismRecord?.member?.date_of_birth,
  baptism_date: props.baptismRecord?.baptism_date,
});

// Format date to YYYY-MM-DD for date input
const formatDate = (date: any) => {
  if (!date) return '';
  // If it's already a string, handle it
  if (typeof date === 'string') {
    // Handle both 'YYYY-MM-DD' and 'YYYY-MM-DD HH:mm:ss' formats
    return date.split(' ')[0];
  }
  return '';
};

// Format date to DD/MM/YYYY for display
const formatDateForDisplay = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  console.log(day,month,year);
  return `${day}/${month}/${year}`;
};

// Track selected member's marriage info for cross-reference display
const selectedMemberMarriageDate = ref<string | null>(
  formatDate(props.baptismRecord.member?.marriage_date) || null
);
const selectedMemberMarriageRegNo = ref<string | null>(
  props.baptismRecord.member?.marriage_reg_no || null
);

// Initialize form with baptism_date from the record OR from member data
const initialBaptismDate = formatDate(props.baptismRecord.baptism_date)
  || formatDate(props.baptismRecord.member?.baptism_date)
  || '';

const form = useForm({
  member_id: props.baptismRecord.member_id,
  baptized_name: props.baptismRecord.baptized_name || '',
  baptized_surname: props.baptismRecord.baptized_surname || '',
  baptism_date: initialBaptismDate,
  baptism_reg_no: props.baptismRecord.baptism_reg_no || props.baptismRecord.member?.baptism_reg_no || '',
  place_of_baptism: props.baptismRecord.place_of_baptism || '',
  baptism_parish_id: props.baptismRecord.baptism_parish_id || props.baptismRecord.member?.baptism_parish_id,
  place_of_birth: props.baptismRecord.place_of_birth || '',
  nationality: props.baptismRecord.nationality || '',
  father_name: props.baptismRecord.father_name || '',
  father_residence: props.baptismRecord.father_residence || '',
  father_profession: props.baptismRecord.father_profession || '',
  mother_name: props.baptismRecord.mother_name || '',
  godfather_name: props.baptismRecord.godfather_name || '',
  godfather_residence: props.baptismRecord.godfather_residence || '',
  godmother_name: props.baptismRecord.godmother_name || '',
  godmother_residence: props.baptismRecord.godmother_residence || '',
  minister_name: props.baptismRecord.minister_name || '',
  baptism_remarks: props.baptismRecord.baptism_remarks || '',
});

// Format the member data for SearchDropdown
const memberOptions = computed(() => {
  if (props.baptismRecord.member) {
    const member = props.baptismRecord.member;
    const communityName = member.community?.name || '';
    return [{
      id: member.id,
      name: `${member.first_name} ${member.last_name} (${member.member_no}) - ${communityName}`,
      baptism_date: member.baptism_date,
      baptism_reg_no: member.baptism_reg_no,
      baptism_parish_id: member.baptism_parish_id,
      date_of_birth: member.date_of_birth,
    }];
  }
  return [];
});

// Computed property for displaying member's date of birth
const displayDateOfBirth = computed(() => {
  const dob = props.baptismRecord.member?.date_of_birth;
  console.log('displayDateOfBirth computed:', {
    raw_dob: dob,
    formatted: dob ? formatDateForDisplay(dob) : 'Not set'
  });
  if (!dob) return 'Not set';
  return formatDateForDisplay(dob);
});

// Handle member selection from SearchDropdown
function handleMemberSelect(member: any) {
  // Auto-populate baptism fields from member data
  form.baptism_date = member.baptism_date || '';
  form.baptism_reg_no = member.baptism_reg_no || '';
  form.baptism_parish_id = member.baptism_parish_id || null;
}

// Computed property to check if form can be submitted
const canSubmit = computed(() => {
  return form.baptism_date && !form.processing;
});

function submit() {
  if (!canSubmit.value) return;

  form.put(`/baptism-records/${props.baptismRecord.id}`, {
    preserveScroll: true,
    onSuccess: () => router.visit('/baptism-records'),
  });
}
</script>

<template>
  <AppLayout title="Edit Baptism Record">
    <Head title="Edit Baptism Record" />

    <div class="p-6">
      <h1 class="mb-6 text-2xl font-bold">Edit Baptism Record</h1>

      <form @submit.prevent="submit" class="max-w-4xl space-y-6 rounded-lg bg-white p-6 shadow">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <Label>Member *</Label>
            <SearchDropdown
              v-model="form.member_id"
              :options="memberOptions"
              fetch-url="/member/search"
              placeholder="Search member by name..."
              :required="true"
              @select="handleMemberSelect"
            />
          </div>
          <div>
            <Label>Date of Birth</Label>
            <input
              :value="displayDateOfBirth"
              type="text"
              readonly
              class="flex h-10 w-full rounded-md border border-input bg-muted px-3 py-2 text-sm ring-offset-background cursor-not-allowed"
              placeholder="DD/MM/YYYY"
            />
            <p class="mt-1 text-xs text-muted-foreground">
              From member record
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
              {{ form.processing ? 'Saving...' : 'Update Baptism Record' }}
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
