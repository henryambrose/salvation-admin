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

// Calculate age from date of birth
const calculateAge = (dateOfBirth: string | null | undefined) => {
  if (!dateOfBirth) return undefined;
  const birthDate = new Date(dateOfBirth);
  const currentDate = new Date();
  const age = currentDate.getFullYear() - birthDate.getFullYear();
  const monthDiff = currentDate.getMonth() - birthDate.getMonth();

  if (monthDiff < 0 || (monthDiff === 0 && currentDate.getDate() < birthDate.getDate())) {
    return age - 1;
  }
  return age;
};

// Get spouse relationship text
const getSpouseRelationship = () => {
  if (!props.member?.spouse) return '';

  // Check gender: if member is male (1), they are husband; if female (2), they are wife
  const isMale = props.member.gender_id === 1;
  const spouseName = `${props.member.spouse.first_name ?? ''} ${props.member.spouse.last_name ?? ''}`.trim();

  return isMale ? `h/o ${spouseName}` : `w/o ${spouseName}`;
};

const form = useForm({
  member_id: props.member?.id ?? undefined,
  death_date: props.member?.deathRecord?.death_date ?? '',
  burial_date: props.member?.deathRecord?.burial_date ?? '',
  burial_reg_no: props.member?.deathRecord?.burial_reg_no ?? '',
  burial_parish_id: props.member?.death_parish_id ?? undefined,
  deceased_name: props.member?.first_name ?? '',
  deceased_surname: props.member?.last_name ?? '',
  relationship: getSpouseRelationship(),
  residence: getMemberAddress(props.member),
  age: calculateAge(props.member?.date_of_birth),
  nationality: '',
  cause_of_death: props.member?.deathRecord?.cause_of_death ?? '',
  place_of_burial: props.member?.deathRecord?.place_of_burial ?? '',
  minister_name: props.member?.deathRecord?.minister_name ?? '',
  death_remarks: props.member?.deathRecord?.death_remarks ?? '',
});

// Computed property for pre-selected member option
const memberOptions = computed(() => {
  if (!props.member) return [];
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
      spouse_name: props.member.spouse ? `${props.member.spouse.first_name ?? ''} ${props.member.spouse.last_name ?? ''}`.trim() : null,
    },
  ];
});

// Handle member selection from SearchDropdown
function handleMemberSelect(member: any) {
  // Auto-populate fields from member data
  form.deceased_name = member.first_name || '';
  form.deceased_surname = member.last_name || '';
  form.nationality = member.nationality || 'Indian';
  form.residence = member.address || '';

  // Calculate age from date of birth if available
  if (member.date_of_birth) {
    const birthDate = new Date(member.date_of_birth);
    const currentDate = new Date();
    const age = currentDate.getFullYear() - birthDate.getFullYear();
    const monthDiff = currentDate.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && currentDate.getDate() < birthDate.getDate())) {
      form.age = age - 1;
    } else {
      form.age = age;
    }
  }

  // Auto-populate relationship with spouse if available
  if (member.spouse_name) {
    const isMale = member.gender_id === 1;
    form.relationship = isMale ? `h/o ${member.spouse_name}` : `w/o ${member.spouse_name}`;
  }
}

function submit() {
  // Date inputs already provide YYYY-MM-DD format, so no conversion needed
  form.post('/death-records', {
    preserveScroll: true,
    onSuccess: () => router.visit('/death-records'),
  });
}
</script>

<template>
  <AppLayout title="Create Death Record">
    <Head title="Create Death Record" />

    <div class="p-6">
      <h1 class="mb-6 text-2xl font-bold">Create Death Record</h1>

      <form @submit.prevent="submit" class="max-w-4xl space-y-6 rounded-lg bg-white p-6 shadow">
        <div class="grid gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <Label>Search Member</Label>
            <SearchDropdown
              v-model="form.member_id"
              :options="memberOptions"
              fetch-url="/member/search?status=deceased"
              placeholder="Search deceased member by name..."
              @select="handleMemberSelect"
            />
          </div>
          <div>
            <Label>Death Date</Label>
            <Input v-model="form.death_date" type="date" />
          </div>
          <div>
            <Label>Burial Date</Label>
            <Input v-model="form.burial_date" type="date" />
          </div>
          <div>
            <Label>Burial Reg No</Label>
            <Input v-model="form.burial_reg_no" />
          </div>
          <div>
            <Label>Deceased Name</Label>
            <Input v-model="form.deceased_name" />
          </div>
          <div>
            <Label>Deceased Surname</Label>
            <Input v-model="form.deceased_surname" />
          </div>
          <div>
            <Label>Relationship (e.g., w/o, h/o)</Label>
            <Input v-model="form.relationship" placeholder="w/o (wife of), h/o (husband of)" />
          </div>
          <div>
            <Label>Age</Label>
            <Input v-model.number="form.age" type="number" min="0" max="150" />
          </div>
          <div>
            <Label>Nationality</Label>
            <Input v-model="form.nationality" />
          </div>
          <div>
            <Label>Cause of Death</Label>
            <Input v-model="form.cause_of_death" />
          </div>
          <div class="md:col-span-2">
            <Label>Residence</Label>
            <Textarea v-model="form.residence" />
          </div>
          <div>
            <Label>Place of Burial</Label>
            <Input v-model="form.place_of_burial" />
          </div>
          <div>
            <Label>Minister Name</Label>
            <Input v-model="form.minister_name" />
          </div>
          <div class="md:col-span-2">
            <Label>Remarks</Label>
            <Textarea v-model="form.death_remarks" />
          </div>
        </div>

        <div class="flex gap-4">
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : 'Save Death Record' }}
          </Button>
          <Button type="button" variant="outline" @click="router.visit('/death-records')"> Cancel </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
