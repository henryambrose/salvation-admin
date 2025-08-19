<script setup lang="ts">
import FamilyNumberSearchDropdown from '@/components/FamilyNumberSearchDropdown.vue';
import FormBody from '@/components/FormBody.vue';
import FormHeader from '@/components/FormHeader.vue';
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { SearchDropdown } from '@/components/ui/searchDropdown';
import SelectInput from '@/components/ui/select/SelectInput.vue';
import TextareaInput from '@/components/ui/textarea/TextareaInput.vue';
import ValidationErrorModal from '@/components/ValidationErrorModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ExternalMember } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

interface Props {
  externalMember: ExternalMember;
  genders: Array<{ id: number; name: string }>;
  relationships: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'External Members', href: '/external-members' },
  { title: 'Edit', href: '#' },
];

// Update the form initialization to use proper default values
const form = useForm({
  first_name: props.externalMember.first_name,
  last_name: props.externalMember.last_name || '',
  gender_id: props.externalMember.gender_id || undefined,
  family_no: props.externalMember.family_no,
  father_id: props.externalMember.father_id,
  mother_id: props.externalMember.mother_id,
  spouse_id: props.externalMember.spouse_id,
  father_source: props.externalMember.father_source || 'Member',
  mother_source: props.externalMember.mother_source || 'Member',
  spouse_source: props.externalMember.spouse_source || 'Member',
  address: props.externalMember.address || '',
  relationship_id: props.externalMember.relationship_id,
});

// Reactive variables to store family members
const familyMembers = ref<Array<{ id: number; name: string }>>([]);
const externalFamilyMembers = ref<Array<{ id: number; name: string }>>([]);

// Function to fetch family members
const fetchFamilyMembers = async () => {
  if (form.family_no) {
    try {
      const response = await fetch(`/member/family-details/${form.family_no}`, {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        credentials: 'same-origin',
      });

      const data = await response.json();
      console.log(data)
      // Filter members from the same family and exclude current member
      const currentMemberId = props.externalMember.id;
      familyMembers.value = data.members
        .filter((member: any) => member.id !== currentMemberId)
        .map((member: any) => ({
          id: member.id,
          name: member.first_name + ' ' + member.last_name,
        }));
    } catch (error) {
      console.error('Error fetching family members:', error);
      familyMembers.value = [];
    }
  } else {
    familyMembers.value = [];
  }
};

// Function to fetch external family members
const fetchExternalFamilyMembers = async () => {
  if (form.family_no) {
    try {
      const response = await fetch(`/external-member/family-details/${form.family_no}`, {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        credentials: 'same-origin',
      });

      const data = await response.json();
      // Filter members from the same family and exclude current member
      const currentMemberId = props.externalMember.id;
      externalFamilyMembers.value = data.members
        .filter((member: any) => member.id !== currentMemberId)
        .map((member: any) => ({
          id: member.id,
          name: member.first_name + ' ' + member.last_name,
        }));
    } catch (error) {
      console.error('Error fetching external family members:', error);
      externalFamilyMembers.value = [];
    }
  } else {
    externalFamilyMembers.value = [];
  }
};

// Watch for family_no changes to refetch family members
watch(
  () => form.family_no,
  () => {
    fetchFamilyMembers();
    fetchExternalFamilyMembers();
  },
);

// Update the watchers to handle the new logic
watch(
  () => form.father_source,
  (newValue) => {
    if (newValue === 'External') {
      fetchExternalFamilyMembers();
    } else {
      fetchFamilyMembers();
    }
    // Clear the ID when switching source types to avoid confusion
    if (form.father_id) {
      form.father_id = undefined;
    }
  },
);

watch(
  () => form.mother_source,
  (newValue) => {
    if (newValue === 'External') {
      fetchExternalFamilyMembers();
    } else {
      fetchFamilyMembers();
    }
    // Clear the ID when switching source types to avoid confusion
    if (form.mother_id) {
      form.mother_id = undefined;
    }
  },
);

watch(
  () => form.spouse_source,
  (newValue) => {
    if (newValue === 'External') {
      fetchExternalFamilyMembers();
    } else {
      fetchFamilyMembers();
    }
    // Clear the ID when switching source types to avoid confusion
    if (form.spouse_id) {
      form.spouse_id = undefined;
    }
  },
);

// Fetch family members on mount
onMounted(() => {
  fetchFamilyMembers();
  fetchExternalFamilyMembers();
});

const genderOptions = computed(() =>
  props.genders.map((gender) => ({
    id: gender.id,
    name: gender.name,
  })),
);

const showValidationModal = ref(false);
const validationErrors = computed(() => Object.entries(form.errors).map(([field, message]) => ({ field, message })));

const submit = () => {
  // Use form.put for updates, not router.put
  form.put(`/external-members/${props.externalMember.id}`, {
    preserveScroll: true,
    onError: () => {
      showValidationModal.value = true;
    },
  });
};

const cancel = () => {
  router.visit('/external-members');
};
</script>
<style>
.highlight-field {
  animation: highlight-fade 2s;
  background-color: #fef08a !important; /* Tailwind yellow-200 */
  border-color: #f59e0b !important; /* Tailwind amber-500 */
}
@keyframes highlight-fade {
  0% {
    background-color: #fde047; /* Tailwind yellow-300 */
    border-color: #f59e0b; /* Tailwind amber-500 */
  }
  100% {
    background-color: inherit;
    border-color: inherit;
  }
}
</style>
<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Edit External Member" />

    <FormHeader title="Edit External Member" :breadcrumbs="breadcrumbs">
      <template #actions>
        <Button variant="outline" @click="router.visit('/external-members')"> External List </Button>
      </template>
    </FormHeader>

    <FormBody>
      <form @submit.prevent="submit" class="space-y-8">
        <!-- Personal Information Section -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Personal Information</h3>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="first_name">First Name <span class="text-red-500">*</span></Label>
              <Input
                id="first_name"
                v-model="form.first_name"
                :error="form.errors.first_name"
                required
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                placeholder="First name"
              />
            </div>

            <div class="grid gap-2">
              <Label for="last_name">Last Name</Label>
              <Input
                id="last_name"
                v-model="form.last_name"
                :error="form.errors.last_name"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                placeholder="Last name"
              />
            </div>

            <div class="grid gap-2">
              <Label for="gender_id">Gender <span class="text-red-500">*</span></Label>
              <SelectInput
                id="gender_id"
                v-model="form.gender_id"
                :options="genderOptions"
                :error="form.errors.gender_id"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                placeholder="Select gender"
                required
              />
            </div>
          </div>

          <!-- Address field - increase height -->
          <div class="col-span-2">
            <Label for="address" class="text-sm font-medium text-gray-700"> Address <span class="text-red-500">*</span> </Label>
            <TextareaInput
              id="address"
              name="address"
              v-model="form.address"
              :error="form.errors.address"
              placeholder="Enter address"
              class="min-h-[120px] resize-none"
            />
            <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">
              {{ form.errors.address }}
            </p>
          </div>
        </div>

        <!-- Family Information Section -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">Family Information</h3>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <Label for="family_no">Family Number <span class="text-red-500">*</span></Label>
              <FamilyNumberSearchDropdown v-model="form.family_no" :error="form.errors.family_no" required class="mt-1" />
            </div>

            <div class="grid gap-2">
              <Label for="relationship_id">
                Relationship <span class="text-red-500">*</span>
                <span class="text-xs font-normal text-gray-500">(with the head of the family)</span>
              </Label>
              <SelectInput
                id="relationship_id"
                v-model="form.relationship_id"
                :options="relationships"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                placeholder="Select relationship"
                required
              />
            </div>
          </div>
        </div>

        <!-- Family Tree Relationships Section - Updated to match Member.vue -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white p-6 shadow">
          <h3 class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 py-2 pl-3 text-lg font-bold text-blue-700">
            Family Tree Relationships
          </h3>
          <p class="mb-4 text-sm text-gray-600">
            Define family relationships for building the family tree. Select whether each relationship is with a member or external person.
          </p>

          <!-- Spouse Relationship -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h4 class="text-md mb-3 font-semibold text-gray-800">Spouse</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="grid gap-2">
                <Label>Source Type</Label>
                <div class="flex gap-4">
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.spouse_source"
                      value="Member"
                      class="text-blue-600 focus:ring-blue-500"
                      :checked="form.spouse_source === 'Member'"
                    />
                    <span class="text-sm">Member</span>
                  </label>
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.spouse_source"
                      value="External"
                      class="text-blue-600 focus:ring-blue-500"
                      :checked="form.spouse_source === 'External'"
                    />
                    <span class="text-sm">External</span>
                  </label>
                </div>
              </div>
              <div class="grid gap-2">
                <Label for="spouse_id">Spouse</Label>
                <SearchDropdown
                  :model-value="form.spouse_id || undefined"
                  @update:model-value="(value) => (form.spouse_id = Number(value))"
                  :options="form.spouse_source === 'Member' ? familyMembers : externalFamilyMembers"
                  class="mt-1 block w-full rounded-full"
                  :placeholder="form.spouse_source === 'Member' ? 'Search for spouse (member)...' : 'Search for spouse (external)...'"
                />
                <InputError class="mt-2" :message="form.errors.spouse_id" />
              </div>
            </div>
          </div>

          <!-- Father Relationship -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h4 class="text-md mb-3 font-semibold text-gray-800">Father</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="grid gap-2">
                <Label>Source Type</Label>
                <div class="flex gap-4">
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.father_source"
                      value="Member"
                      class="text-blue-600 focus:ring-blue-500"
                      :checked="form.father_source === 'Member'"
                    />
                    <span class="text-sm">Member</span>
                  </label>
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.father_source"
                      value="External"
                      class="text-blue-600 focus:ring-blue-500"
                      :checked="form.father_source === 'External'"
                    />
                    <span class="text-sm">External</span>
                  </label>
                </div>
              </div>
              <div class="grid gap-2">
                <Label for="father_id">Father</Label>
                <SearchDropdown
                  :model-value="form.father_id || undefined"
                  @update:model-value="(value) => (form.father_id = Number(value))"
                  :options="form.father_source === 'Member' ? familyMembers : externalFamilyMembers"
                  class="mt-1 block w-full rounded-full"
                  :placeholder="form.father_source === 'Member' ? 'Search for father (member)...' : 'Search for father (external)...'"
                />
                <InputError class="mt-2" :message="form.errors.father_id" />
              </div>
            </div>
          </div>

          <!-- Mother Relationship -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h4 class="text-md mb-3 font-semibold text-gray-800">Mother</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="grid gap-2">
                <Label>Source Type</Label>
                <div class="flex gap-4">
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.mother_source"
                      value="Member"
                      class="text-blue-600 focus:ring-blue-500"
                      :checked="form.mother_source === 'Member'"
                    />
                    <span class="text-sm">Member</span>
                  </label>
                  <label class="flex cursor-pointer items-center gap-2">
                    <input
                      type="radio"
                      v-model="form.mother_source"
                      value="External"
                      class="text-blue-600 focus:ring-blue-500"
                      :checked="form.mother_source === 'External'"
                    />
                    <span class="text-sm">External</span>
                  </label>
                </div>
              </div>
              <div class="grid gap-2">
                <Label for="mother_id">Mother</Label>
                <SearchDropdown
                  :model-value="form.mother_id || undefined"
                  @update:model-value="(value) => (form.mother_id = Number(value))"
                  :options="form.mother_source === 'Member' ? familyMembers : externalFamilyMembers"
                  class="mt-1 block w-full rounded-full"
                  :placeholder="form.mother_source === 'Member' ? 'Search for mother (member)...' : 'Search for mother (external)...'"
                />
                <InputError class="mt-2" :message="form.errors.mother_id" />
              </div>
            </div>
          </div>
        </div>

        <!-- Action Bar -->
        <div class="sticky right-0 bottom-0 left-0 z-10 flex items-center gap-4 rounded-b-2xl bg-gray-50 p-4 shadow-inner">
          <Button
            :disabled="form.processing"
            class="flex items-center gap-2 rounded-full bg-blue-600 px-6 py-2 text-white shadow transition hover:bg-blue-700"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Update External Member
          </Button>
          <Button type="button" @click="cancel" variant="outline" class="flex items-center gap-2 rounded-full px-6 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel
          </Button>
          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Updated.</p>
          </Transition>
        </div>
      </form>
    </FormBody>

    <ValidationErrorModal v-model="showValidationModal" :errors="validationErrors" />
  </AppLayout>
</template>
