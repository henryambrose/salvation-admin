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
  parishes: any[];
}>();

// Track selected member's date of birth and marriage info
const selectedMemberDateOfBirth = ref<string | null>(null);
const selectedMemberMarriageDate = ref<string | null>(null);
const selectedMemberMarriageRegNo = ref<string | null>(null);

const form = useForm({
  member_id: undefined as number | undefined,
  baptism_date: '',
  baptism_reg_no: '',
  place_of_baptism: '',
  baptism_parish_id: undefined as number | undefined,
  place_of_birth: '',
  nationality: '',
  father_name: '',
  father_residence: '',
  father_profession: '',
  mother_name: '',
  godfather_name: '',
  godfather_residence: '',
  godmother_name: '',
  godmother_residence: '',
  minister_name: '',
  baptism_remarks: '',
});

// Format date to DD/MM/YYYY for display
const formatDateForDisplay = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

// Computed property for displaying member's date of birth
const displayDateOfBirth = computed(() => {
  if (!selectedMemberDateOfBirth.value) return 'Not set';
  return formatDateForDisplay(selectedMemberDateOfBirth.value);
});

// Handle member selection from SearchDropdown
function handleMemberSelect(member: any) {
  // Auto-populate baptism fields from member data
  form.baptism_date = member.baptism_date || '';
  form.baptism_reg_no = member.baptism_reg_no || '';
  form.baptism_parish_id = member.baptism_parish_id ?? undefined;
  selectedMemberDateOfBirth.value = member.date_of_birth ?? null;
  selectedMemberMarriageDate.value = member.marriage_date ?? null;
  selectedMemberMarriageRegNo.value = member.marriage_reg_no ?? null;
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
            <Label>Member *</Label>
            <SearchDropdown
              v-model="form.member_id"
              :options="[]"
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
