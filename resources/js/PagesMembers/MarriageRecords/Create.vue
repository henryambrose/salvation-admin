<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SearchDropdown from '@/components/ui/searchDropdown/SearchDropdown.vue';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
  parishes: any[];
  member?: any | null;
}>();

// Get member's address
const getMemberAddress = (member: any) => {
  if (!member) return '';
  const parts = [member.permanent_add1, member.permanent_add2, member.permanent_add3].filter(Boolean);
  return parts.join(', ');
};

// Determine if member is bride or groom based on gender
const isMale = props.member?.gender_id === 1; // Assuming 1 is male, adjust if needed

const form = useForm({
  marriage_date: props.member?.marriageRecord?.marriage_date ?? props.member?.marriage_date ?? '',
  marriage_reg_no: props.member?.marriageRecord?.marriage_reg_no ?? props.member?.marriage_reg_no ?? '',
  marriage_parish_id: props.member?.marriage_parish_id ?? undefined,
  parish_of_marriage: props.member?.marriage_parish ?? '',
  bridegroom_member_id: isMale ? props.member?.id : (props.member?.spouse?.id ?? undefined),
  bridegroom_name: isMale ? (props.member?.first_name ?? '') : (props.member?.spouse?.first_name ?? ''),
  bridegroom_surname: isMale ? (props.member?.last_name ?? '') : (props.member?.spouse?.last_name ?? ''),
  bridegroom_dob: isMale ? (props.member?.date_of_birth ?? '') : (props.member?.spouse?.date_of_birth ?? ''),
  bridegroom_nationality: '',
  bridegroom_profession: isMale ? (props.member?.company_name ?? '') : (props.member?.spouse?.company_name ?? ''),
  bridegroom_residence: isMale ? getMemberAddress(props.member) : getMemberAddress(props.member?.spouse),
  bridegroom_father_name:
    isMale && props.member?.father ? `${props.member.father.first_name ?? ''} ${props.member.father.last_name ?? ''}`.trim() : '',
  bridegroom_mother_name:
    isMale && props.member?.mother ? `${props.member.mother.first_name ?? ''} ${props.member.mother.last_name ?? ''}`.trim() : '',
  bridegroom_status: '',
  bridegroom_if_widower_whose: '',
  bride_member_id: !isMale ? props.member?.id : (props.member?.spouse?.id ?? undefined),
  bride_name: !isMale ? (props.member?.first_name ?? '') : (props.member?.spouse?.first_name ?? ''),
  bride_surname: !isMale ? (props.member?.last_name ?? '') : (props.member?.spouse?.last_name ?? ''),
  bride_dob: !isMale ? (props.member?.date_of_birth ?? '') : (props.member?.spouse?.date_of_birth ?? ''),
  bride_nationality: '',
  bride_profession: !isMale ? (props.member?.company_name ?? '') : (props.member?.spouse?.company_name ?? ''),
  bride_residence: !isMale ? getMemberAddress(props.member) : getMemberAddress(props.member?.spouse),
  bride_father_name: !isMale && props.member?.father ? `${props.member.father.first_name ?? ''} ${props.member.father.last_name ?? ''}`.trim() : '',
  bride_mother_name: !isMale && props.member?.mother ? `${props.member.mother.first_name ?? ''} ${props.member.mother.last_name ?? ''}`.trim() : '',
  bride_status: '',
  bride_if_widow_whose: '',
  first_witness_name: '',
  first_witness_residence: '',
  second_witness_name: '',
  second_witness_residence: '',
  minister_name: '',
  marriage_remarks: '',
  year_of_marriage: '',
});

// Computed property for pre-selected bridegroom member option
const bridegroomMemberOptions = computed(() => {
  if (!isMale || !props.member) return [];
  const communityName = props.member.community?.name ?? 'N/A';
  const memberNo = props.member.member_no ?? 'N/A';

  // Get address
  const address = [props.member.permanent_add1, props.member.permanent_add2, props.member.permanent_add3].filter(Boolean).join(', ');

  return [
    {
      id: props.member.id,
      name: `${props.member.first_name ?? ''} ${props.member.last_name ?? ''} - ${communityName} - ${memberNo}`.trim(),
      first_name: props.member.first_name,
      middle_name: props.member.middle_name,
      last_name: props.member.last_name,
      member_no: props.member.member_no,
      date_of_birth: props.member.date_of_birth,
      gender_id: props.member.gender_id,
      address: address,
      nationality: 'Indian',
      marriage_date: props.member.marriageRecord?.marriage_date ?? props.member.marriage_date,
      marriage_reg_no: props.member.marriageRecord?.marriage_reg_no ?? props.member.marriage_reg_no,
      marriage_parish_id: props.member.marriage_parish_id,
      father_name: props.member.father ? `${props.member.father.first_name ?? ''} ${props.member.father.last_name ?? ''}`.trim() : null,
      father_profession: props.member.father?.company_name,
      mother_name: props.member.mother ? `${props.member.mother.first_name ?? ''} ${props.member.mother.last_name ?? ''}`.trim() : null,
    },
  ];
});

// Computed property for pre-selected bride member option
const brideMemberOptions = computed(() => {
  if (isMale || !props.member) return [];
  const communityName = props.member.community?.name ?? 'N/A';
  const memberNo = props.member.member_no ?? 'N/A';

  // Get address
  const address = [props.member.permanent_add1, props.member.permanent_add2, props.member.permanent_add3].filter(Boolean).join(', ');

  return [
    {
      id: props.member.id,
      name: `${props.member.first_name ?? ''} ${props.member.last_name ?? ''} - ${communityName} - ${memberNo}`.trim(),
      first_name: props.member.first_name,
      middle_name: props.member.middle_name,
      last_name: props.member.last_name,
      member_no: props.member.member_no,
      date_of_birth: props.member.date_of_birth,
      gender_id: props.member.gender_id,
      address: address,
      nationality: 'Indian',
      marriage_date: props.member.marriageRecord?.marriage_date ?? props.member.marriage_date,
      marriage_reg_no: props.member.marriageRecord?.marriage_reg_no ?? props.member.marriage_reg_no,
      marriage_parish_id: props.member.marriage_parish_id,
      father_name: props.member.father ? `${props.member.father.first_name ?? ''} ${props.member.father.last_name ?? ''}`.trim() : null,
      father_profession: props.member.father?.company_name,
      mother_name: props.member.mother ? `${props.member.mother.first_name ?? ''} ${props.member.mother.last_name ?? ''}`.trim() : null,
    },
  ];
});

// Handle bridegroom member selection
function handleBridegroomSelect(member: any) {
  form.bridegroom_name = member.first_name || '';
  form.bridegroom_surname = member.last_name || '';
  form.bridegroom_dob = member.date_of_birth || '';
  form.bridegroom_nationality = member.nationality || 'Indian';
  form.bridegroom_residence = member.address || '';
  form.bridegroom_father_name = member.father_name || '';
  form.bridegroom_mother_name = member.mother_name || '';
  form.bridegroom_profession = member.father_profession || '';

  // If member has marriage data, populate marriage details
  if (member.marriage_date) {
    form.marriage_date = member.marriage_date;
  }
  if (member.marriage_reg_no) {
    form.marriage_reg_no = member.marriage_reg_no;
  }
  if (member.marriage_parish_id) {
    form.marriage_parish_id = member.marriage_parish_id;
  }
}

// Handle bride member selection
function handleBrideSelect(member: any) {
  form.bride_name = member.first_name || '';
  form.bride_surname = member.last_name || '';
  form.bride_dob = member.date_of_birth || '';
  form.bride_nationality = member.nationality || 'Indian';
  form.bride_residence = member.address || '';
  form.bride_father_name = member.father_name || '';
  form.bride_mother_name = member.mother_name || '';
  form.bride_profession = member.father_profession || '';

  // If member has marriage data, populate marriage details
  if (member.marriage_date) {
    form.marriage_date = member.marriage_date;
  }
  if (member.marriage_reg_no) {
    form.marriage_reg_no = member.marriage_reg_no;
  }
  if (member.marriage_parish_id) {
    form.marriage_parish_id = member.marriage_parish_id;
  }
}

function submit() {
  // Date inputs already provide YYYY-MM-DD format, so no conversion needed
  form.post('/marriage-records', {
    preserveScroll: true,
    onSuccess: () => router.visit('/marriage-records'),
  });
}
</script>

<template>
  <AppLayout title="Create Marriage Record">
    <Head title="Create Marriage Record" />

    <div class="p-6">
      <h1 class="mb-6 text-2xl font-bold">Create Marriage Record</h1>

      <div
        v-if="Object.keys(form.errors).length > 0"
        class="border-destructive/50 bg-destructive/10 text-destructive mb-6 max-w-4xl rounded-lg border p-4 text-sm"
      >
        <p class="font-semibold">Please fix the following before saving:</p>
        <ul class="mt-1 list-inside list-disc">
          <li v-for="(message, field) in form.errors" :key="field">{{ message }}</li>
        </ul>
      </div>

      <form @submit.prevent="submit" class="max-w-4xl space-y-6 rounded-lg bg-white p-6 shadow">
        <!-- Marriage Details -->
        <div class="border-b pb-4">
          <h2 class="mb-4 text-lg font-semibold">Marriage Details</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <Label>Marriage Date</Label>
              <Input v-model="form.marriage_date" type="date" />
              <p v-if="form.errors.marriage_date" class="text-destructive mt-1 text-sm">{{ form.errors.marriage_date }}</p>
            </div>
            <div>
              <Label>Marriage Reg No</Label>
              <Input v-model="form.marriage_reg_no" />
              <p v-if="form.errors.marriage_reg_no" class="text-destructive mt-1 text-sm">{{ form.errors.marriage_reg_no }}</p>
            </div>
            <div>
              <Label>Year of Marriage</Label>
              <Input v-model="form.year_of_marriage" placeholder="Enter year of marriage" />
              <p v-if="form.errors.year_of_marriage" class="text-destructive mt-1 text-sm">{{ form.errors.year_of_marriage }}</p>
            </div>
            <div class="md:col-span-2">
              <Label>Place of Marriage</Label>
              <Input v-model="form.parish_of_marriage" />
              <p v-if="form.errors.parish_of_marriage" class="text-destructive mt-1 text-sm">{{ form.errors.parish_of_marriage }}</p>
            </div>
          </div>
        </div>

        <!-- Bridegroom Information -->
        <div class="border-b pb-4">
          <h2 class="mb-4 text-lg font-semibold">Bridegroom Information</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
              <Label>Search Bridegroom Member</Label>
              <SearchDropdown
                v-model="form.bridegroom_member_id"
                :options="bridegroomMemberOptions"
                fetch-url="/member/search"
                placeholder="Search bridegroom by name..."
                @select="handleBridegroomSelect"
              />
              <p class="text-muted-foreground mt-1 text-xs">Optional - leave empty for non-members</p>
              <p v-if="form.errors.bridegroom_member_id" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_member_id }}</p>
            </div>
            <div>
              <Label>Name</Label>
              <Input v-model="form.bridegroom_name" />
              <p v-if="form.errors.bridegroom_name" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_name }}</p>
            </div>
            <div>
              <Label>Surname</Label>
              <Input v-model="form.bridegroom_surname" />
              <p v-if="form.errors.bridegroom_surname" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_surname }}</p>
            </div>
            <div>
              <Label>Date of Birth</Label>
              <Input v-model="form.bridegroom_dob" type="date" />
              <p v-if="form.errors.bridegroom_dob" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_dob }}</p>
            </div>
            <div>
              <Label>Nationality</Label>
              <Input v-model="form.bridegroom_nationality" />
              <p v-if="form.errors.bridegroom_nationality" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_nationality }}</p>
            </div>
            <div>
              <Label>Profession</Label>
              <Input v-model="form.bridegroom_profession" />
              <p v-if="form.errors.bridegroom_profession" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_profession }}</p>
            </div>
            <div class="md:col-span-2">
              <Label>Residence</Label>
              <Textarea v-model="form.bridegroom_residence" />
              <p v-if="form.errors.bridegroom_residence" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_residence }}</p>
            </div>
            <div>
              <Label>Father's Name</Label>
              <Input v-model="form.bridegroom_father_name" />
              <p v-if="form.errors.bridegroom_father_name" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_father_name }}</p>
            </div>
            <div>
              <Label>Mother's Name</Label>
              <Input v-model="form.bridegroom_mother_name" />
              <p v-if="form.errors.bridegroom_mother_name" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_mother_name }}</p>
            </div>
            <div>
              <Label>Status (Bachelor/Widower)</Label>
              <Input v-model="form.bridegroom_status" placeholder="Bachelor or Widower" />
              <p v-if="form.errors.bridegroom_status" class="text-destructive mt-1 text-sm">{{ form.errors.bridegroom_status }}</p>
            </div>
            <div>
              <Label>If Widower, Whose?</Label>
              <Input v-model="form.bridegroom_if_widower_whose" />
              <p v-if="form.errors.bridegroom_if_widower_whose" class="text-destructive mt-1 text-sm">
                {{ form.errors.bridegroom_if_widower_whose }}
              </p>
            </div>
          </div>
        </div>

        <!-- Bride Information -->
        <div class="border-b pb-4">
          <h2 class="mb-4 text-lg font-semibold">Bride Information</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
              <Label>Search Bride Member</Label>
              <SearchDropdown
                v-model="form.bride_member_id"
                :options="brideMemberOptions"
                fetch-url="/member/search"
                placeholder="Search bride by name..."
                @select="handleBrideSelect"
              />
              <p class="text-muted-foreground mt-1 text-xs">Optional - leave empty for non-members</p>
              <p v-if="form.errors.bride_member_id" class="text-destructive mt-1 text-sm">{{ form.errors.bride_member_id }}</p>
            </div>
            <div>
              <Label>Name</Label>
              <Input v-model="form.bride_name" />
              <p v-if="form.errors.bride_name" class="text-destructive mt-1 text-sm">{{ form.errors.bride_name }}</p>
            </div>
            <div>
              <Label>Surname</Label>
              <Input v-model="form.bride_surname" />
              <p v-if="form.errors.bride_surname" class="text-destructive mt-1 text-sm">{{ form.errors.bride_surname }}</p>
            </div>
            <div>
              <Label>Date of Birth</Label>
              <Input v-model="form.bride_dob" type="date" />
              <p v-if="form.errors.bride_dob" class="text-destructive mt-1 text-sm">{{ form.errors.bride_dob }}</p>
            </div>
            <div>
              <Label>Nationality</Label>
              <Input v-model="form.bride_nationality" />
              <p v-if="form.errors.bride_nationality" class="text-destructive mt-1 text-sm">{{ form.errors.bride_nationality }}</p>
            </div>
            <div>
              <Label>Profession</Label>
              <Input v-model="form.bride_profession" />
              <p v-if="form.errors.bride_profession" class="text-destructive mt-1 text-sm">{{ form.errors.bride_profession }}</p>
            </div>
            <div class="md:col-span-2">
              <Label>Residence</Label>
              <Textarea v-model="form.bride_residence" />
              <p v-if="form.errors.bride_residence" class="text-destructive mt-1 text-sm">{{ form.errors.bride_residence }}</p>
            </div>
            <div>
              <Label>Father's Name</Label>
              <Input v-model="form.bride_father_name" />
              <p v-if="form.errors.bride_father_name" class="text-destructive mt-1 text-sm">{{ form.errors.bride_father_name }}</p>
            </div>
            <div>
              <Label>Mother's Name</Label>
              <Input v-model="form.bride_mother_name" />
              <p v-if="form.errors.bride_mother_name" class="text-destructive mt-1 text-sm">{{ form.errors.bride_mother_name }}</p>
            </div>
            <div>
              <Label>Status (Spinster/Widow)</Label>
              <Input v-model="form.bride_status" placeholder="Spinster or Widow" />
              <p v-if="form.errors.bride_status" class="text-destructive mt-1 text-sm">{{ form.errors.bride_status }}</p>
            </div>
            <div>
              <Label>If Widow, Whose?</Label>
              <Input v-model="form.bride_if_widow_whose" />
              <p v-if="form.errors.bride_if_widow_whose" class="text-destructive mt-1 text-sm">{{ form.errors.bride_if_widow_whose }}</p>
            </div>
          </div>
        </div>

        <!-- Witnesses -->
        <div class="border-b pb-4">
          <h2 class="mb-4 text-lg font-semibold">Witnesses</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <Label>First Witness Name</Label>
              <Input v-model="form.first_witness_name" />
              <p v-if="form.errors.first_witness_name" class="text-destructive mt-1 text-sm">{{ form.errors.first_witness_name }}</p>
            </div>
            <div class="md:col-span-2">
              <Label>First Witness Residence</Label>
              <Textarea v-model="form.first_witness_residence" />
              <p v-if="form.errors.first_witness_residence" class="text-destructive mt-1 text-sm">
                {{ form.errors.first_witness_residence }}
              </p>
            </div>
            <div>
              <Label>Second Witness Name</Label>
              <Input v-model="form.second_witness_name" />
              <p v-if="form.errors.second_witness_name" class="text-destructive mt-1 text-sm">{{ form.errors.second_witness_name }}</p>
            </div>
            <div class="md:col-span-2">
              <Label>Second Witness Residence</Label>
              <Textarea v-model="form.second_witness_residence" />
              <p v-if="form.errors.second_witness_residence" class="text-destructive mt-1 text-sm">
                {{ form.errors.second_witness_residence }}
              </p>
            </div>
          </div>
        </div>

        <!-- Minister & Remarks -->
        <div>
          <h2 class="mb-4 text-lg font-semibold">Additional Information</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <Label>Minister Name</Label>
              <Input v-model="form.minister_name" />
              <p v-if="form.errors.minister_name" class="text-destructive mt-1 text-sm">{{ form.errors.minister_name }}</p>
            </div>
            <div class="md:col-span-2">
              <Label>Remarks</Label>
              <Textarea v-model="form.marriage_remarks" />
              <p v-if="form.errors.marriage_remarks" class="text-destructive mt-1 text-sm">{{ form.errors.marriage_remarks }}</p>
            </div>
          </div>
        </div>

        <div class="flex gap-4">
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : 'Save Marriage Record' }}
          </Button>
          <Button type="button" variant="outline" @click="router.visit('/marriage-records')"> Cancel </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
