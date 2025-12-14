<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import SearchDropdown from '@/components/ui/searchDropdown/SearchDropdown.vue';

const props = defineProps<{
  deathRecord: any;
  parishes: any[];
}>();

// Format date from YYYY-MM-DD to DD/MM/YYYY for display
const formatDateForDisplay = (dateString: string | null | undefined): string => {
  if (!dateString) return '';
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return '';
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

// Format date from DD/MM/YYYY to YYYY-MM-DD for database
const formatDateForDatabase = (dateString: string): string => {
  if (!dateString) return '';
  const parts = dateString.split('/');
  if (parts.length !== 3) return '';
  const [day, month, year] = parts;
  return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
};

const form = useForm({
  member_id: props.deathRecord.member_id,
  death_date: props.deathRecord.death_date || '', // Keep YYYY-MM-DD format for type="date" inputs
  burial_date: props.deathRecord.burial_date || '', // Keep YYYY-MM-DD format for type="date" inputs
  burial_reg_no: props.deathRecord.burial_reg_no || '',
  burial_parish_id: props.deathRecord.burial_parish_id,
  deceased_name: props.deathRecord.deceased_name || '',
  deceased_surname: props.deathRecord.deceased_surname || '',
  relationship: props.deathRecord.relationship || '',
  residence: props.deathRecord.residence || '',
  age: props.deathRecord.age,
  nationality: props.deathRecord.nationality || '',
  cause_of_death: props.deathRecord.cause_of_death || '',
  place_of_burial: props.deathRecord.place_of_burial || '',
  minister_name: props.deathRecord.minister_name || '',
  death_remarks: props.deathRecord.death_remarks || '',
});

// Handle member selection from SearchDropdown
function handleMemberSelect(member: any) {
  // Auto-populate fields from member data
  form.deceased_name = member.first_name || '';
  form.deceased_surname = member.last_name || '';
  form.nationality = member.nationality || '';
  form.residence = member.current_address || '';

  // Calculate age from date of birth if available
  if (member.date_of_birth) {
    const birthDate = new Date(member.date_of_birth);
    const currentDate = new Date();
    const age = currentDate.getFullYear() - birthDate.getFullYear();
    form.age = age;
  }
}

function submit() {
  // Date inputs already provide YYYY-MM-DD format, so no conversion needed
  form.put(`/death-records/${props.deathRecord.id}`, {
    preserveScroll: true,
    onSuccess: () => router.visit('/death-records'),
  });
}
</script>

<template>
  <AppLayout title="Edit Death Record">
    <Head title="Edit Death Record" />

    <div class="p-6">
      <h1 class="mb-6 text-2xl font-bold">Edit Death Record</h1>

      <form @submit.prevent="submit" class="max-w-4xl space-y-6 rounded-lg bg-white p-6 shadow">
        <div class="grid gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <Label>Search Member</Label>
            <SearchDropdown
              v-model="form.member_id"
              :options="[]"
              fetch-url="/member/search?status=deceased"
              placeholder="Search deceased member by name..."
              @select="handleMemberSelect"
            />
          </div>
          <div>
            <Label>Death Date</Label>
            <Input
              v-model="form.death_date"
              type="date"
            />
          </div>
          <div>
            <Label>Burial Date</Label>
            <Input
              v-model="form.burial_date"
              type="date"
            />
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
            {{ form.processing ? 'Saving...' : 'Update Death Record' }}
          </Button>
          <Button type="button" variant="outline" @click="router.visit('/death-records')">
            Cancel
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
