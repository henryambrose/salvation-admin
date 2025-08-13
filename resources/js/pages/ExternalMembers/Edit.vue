<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Edit External Member" />
    
    <FormHeader title="Edit External Member" :breadcrumbs="breadcrumbs">
      <template #actions>
        <Button variant="outline" @click="router.visit('/external-members')">
          External List
        </Button>
      </template>
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
                :error="form.errors.family_no" 
                required 
                class="mt-1"
              />
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
                :error="form.errors.father_id" 
                placeholder="Search for father..."
                :existing-data="externalMember.father_data || undefined"
                class="mt-1"
              />
            </div>

            <div class="grid gap-2">
              <Label>Mother</Label>
              <MemberTypeSearchDropdown 
                v-model="form.mother_id" 
                v-model:sourceType="form.mother_source"
                :error="form.errors.mother_id" 
                placeholder="Search for father..."
                :existing-data="externalMember.mother_data || undefined"
                class="mt-1"
              />
            </div>

            <div class="grid gap-2">
              <Label>Spouse</Label>
              <MemberTypeSearchDropdown 
                v-model="form.spouse_id" 
                v-model:sourceType="form.spouse_source"
                :error="form.errors.spouse_id" 
                placeholder="Search for spouse..."
                :existing-data="externalMember.spouse_data || undefined"
                class="mt-1"
              />
            </div>
          </div>
        </div>

        <!-- Action Bar -->
        <div class="sticky bottom-0 left-0 right-0 z-10 flex items-center gap-4 bg-gray-50 p-4 rounded-b-2xl shadow-inner">
          <Button :disabled="form.processing" class="bg-blue-600 text-white px-6 py-2 rounded-full shadow hover:bg-blue-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Update External Member
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
            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Updated.</p>
          </Transition>
        </div>
      </form>
    </FormBody>
    
    <ValidationErrorModal 
      v-model="showValidationModal" 
      :errors="validationErrors" 
    />
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm, router, Head } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue' 
import AppLayout from '@/layouts/AppLayout.vue'
import FormHeader from '@/components/FormHeader.vue'
import FormBody from '@/components/FormBody.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import SelectInput from '@/components/ui/select/SelectInput.vue'
import TextareaInput from '@/components/ui/textarea/TextareaInput.vue'
import FamilyNumberSearchDropdown from '@/components/FamilyNumberSearchDropdown.vue'
import MemberTypeSearchDropdown from '@/components/MemberTypeSearchDropdown.vue'
import ValidationErrorModal from '@/components/ValidationErrorModal.vue'
import { ExternalMember } from '@/types';

interface Props {
  externalMember: ExternalMember;
  genders: Array<{ id: number; name: string }>
  relationships: Array<{ id: number; name: string }>
}

const props = defineProps<Props>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'External Members', href: '/external-members' },
  { title: 'Edit', href: '#' }
]

const form = useForm({
  first_name: props.externalMember.first_name,
  last_name: props.externalMember.last_name || '',
  gender_id: props.externalMember.gender_id || undefined, // Handle null values
  family_no: props.externalMember.family_no,
  father_id: props.externalMember.father_id,
  mother_id: props.externalMember.mother_id,
  spouse_id: props.externalMember.spouse_id,
  father_source: props.externalMember.father_source,
  mother_source: props.externalMember.mother_source,
  spouse_source: props.externalMember.spouse_source,
  address: props.externalMember.address || '',
  relationship_id: props.externalMember.relationship_id
})

const genderOptions = computed(() => 
  props.genders.map(gender => ({
    id: gender.id,
    name: gender.name
  }))
)

const showValidationModal = ref(false)
const validationErrors = computed(() => 
  Object.entries(form.errors).map(([field, message]) => ({ field, message }))
)

const submit = () => {
  // Use form.put for updates, not router.put
  form.put(`/external-members/${props.externalMember.id}`, {
    preserveScroll: true,
    onError: () => {
      showValidationModal.value = true
    }
  })
}

const cancel = () => {
  router.visit('/external-members')
}
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