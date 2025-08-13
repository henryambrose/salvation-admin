<script setup lang="ts">
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import FormBody from '@/components/FormBody.vue';
import FormHeader from '@/components/FormHeader.vue';
import InputError from '@/components/InputError.vue';
import MemberTypeSearchDropdown from '@/components/MemberTypeSearchDropdown.vue';
import FamilyNumberSearchDropdown from '@/components/FamilyNumberSearchDropdown.vue';
import ValidationErrorModal from '@/components/ValidationErrorModal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SelectInput } from '@/components/ui/select';
import { TextareaInput } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Relationships, type BreadcrumbItem, type SharedData, type User } from '@/types';
import { List } from 'lucide-vue-next';
import { ref, watch, nextTick, onMounted, computed } from 'vue';

interface Props {
  relationships: Relationships;
  genders: Array<{ id: number; name: string }>;
  familyNo?: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'External Members',
    href: '/external-members',
  },
  {
    title: 'Create External Member',
    href: '/external-members/create',
  },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
  first_name: '',
  last_name: '',
  gender_id: '',
  address: '',
  family_no: props.familyNo || '',
  father_id: undefined,
  mother_id: undefined,
  spouse_id: undefined,
  relationship_id: '',
  father_source: '',
  mother_source: '',
  spouse_source: '',
});

const submit = () => {
  form.post(route('external-members.store'));
};

const cancel = () => {
  router.get('/external-members');
};

const showValidationErrors = ref(false);

watch(() => form.errors, (errors) => {
  if (Object.keys(errors).length > 0) {
    showValidationErrors.value = true;
  }
}, { deep: true });
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Create External Member" />

    <FormHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-700">External Members</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/external-members" class="flex items-center gap-2 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
            <component :is="List" />
            <span>External List</span>
          </Button>
        </div>
      </div>
    </FormHeader>

    <FormBody>
      <form @submit.prevent="submit" class="space-y-8">
        <!-- Personal Information Section -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Personal Information</h3>
          
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="first_name">First Name <span class="text-red-500">*</span></Label>
              <Input
                id="first_name"
                v-model="form.first_name"
                type="text"
                required
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                placeholder="First name"
              />
              <InputError :message="form.errors.first_name" class="mt-2" />
            </div>

            <div class="grid gap-2">
              <Label for="last_name">Last Name</Label>
              <Input
                id="last_name"
                v-model="form.last_name"
                type="text"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                placeholder="Last name"
              />
              <InputError :message="form.errors.last_name" class="mt-2" />
            </div>

            <div class="grid gap-2">
              <Label for="gender_id">Gender <span class="text-red-500">*</span></Label>
              <SelectInput
                id="gender_id"
                v-model="form.gender_id"
                :options="genders"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                placeholder="Select gender"
                required
              />
              <InputError :message="form.errors.gender_id" class="mt-2" />
            </div>
          </div>

          <!-- Address field - increase height -->
          <div class="col-span-2">
            <Label for="address" class="text-sm font-medium text-gray-700">
              Address <span class="text-red-500">*</span>
            </Label>
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
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Family Information</h3>
          
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <Label for="family_no">Family Number <span class="text-red-500">*</span></Label>
              <FamilyNumberSearchDropdown
                v-model="form.family_no"
                placeholder="Search for family number..."
                class="mt-1"
              />
              <InputError :message="form.errors.family_no" class="mt-2" />
            </div>

            <div class="grid gap-2">
              <Label for="relationship_id">
                Relationship <span class="text-red-500">*</span>
                <span class="text-xs text-gray-500 font-normal">(with the head of the family)</span>
              </Label>
              <SelectInput
                id="relationship_id"
                v-model="form.relationship_id"
                :options="relationships"
                class="mt-1 block w-full rounded-full border-gray-300 px-4 py-2 shadow focus:ring-2 focus:ring-blue-200"
                placeholder="Select relationship"
                required
              />
              <InputError :message="form.errors.relationship_id" class="mt-2" />
            </div>
          </div>
        </div>

        <!-- Family Relationships Section -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white shadow p-6">
          <h3 class="mb-4 text-lg font-bold text-blue-700 border-l-4 border-blue-500 pl-3 bg-blue-50 py-2 rounded">Family Relationships</h3>
          
          <div class="space-y-6">
            <div class="grid gap-2">
              <Label>Father</Label>
              <MemberTypeSearchDropdown
                v-model="form.father_id"
                v-model:sourceType="form.father_source"
                :memberType="form.father_source"
                :familyNo="form.family_no"
                placeholder="Search for father..."
                class="mt-1"
              />
              <InputError :message="form.errors.father_id" class="mt-2" />
            </div>

            <div class="grid gap-2">
              <Label>Mother</Label>
              <MemberTypeSearchDropdown
                v-model="form.mother_id"
                v-model:sourceType="form.mother_source"
                :familyNo="form.family_no"
                placeholder="Search for mother..."
                class="mt-1"
              />
              <InputError :message="form.errors.mother_id" class="mt-2" />
            </div>

            <div class="grid gap-2">
              <Label>Spouse</Label>
              <MemberTypeSearchDropdown
                v-model="form.spouse_id"
                 v-model:sourceType="form.spouse_source"
                :memberType="form.spouse_source"
                :familyNo="form.family_no"
                placeholder="Search for spouse..."
                class="mt-1"
              />
              <InputError :message="form.errors.spouse_id" class="mt-2" />
            </div>
          </div>
        </div>

        <!-- Action Bar -->
        <div class="sticky bottom-0 left-0 right-0 z-10 flex items-center gap-4 bg-gray-50 p-4 rounded-b-2xl shadow-inner">
          <Button :disabled="form.processing" class="bg-blue-600 text-white px-6 py-2 rounded-full shadow hover:bg-blue-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Create External Member
          </Button>
          <Button type="button" @click="cancel" variant="outline" class="rounded-full px-6 py-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            Cancel
          </Button>
          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
          </Transition>
        </div>
      </form>
    </FormBody>

    <!-- Validation Error Modal -->
    <ValidationErrorModal
      v-model="showValidationErrors"
      :errors="Object.entries(form.errors).map(([field, message]) => ({ field, message }))"
    />
  </AppLayout>
</template>

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