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
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- First Name -->
          <div>
            <Label for="first_name">First Name *</Label>
            <Input id="first_name" v-model="form.first_name" :error="form.errors.first_name" required />
          </div>
          <!-- Last Name -->
          <div>
            <Label for="last_name">Last Name</Label>
            <Input id="last_name" v-model="form.last_name" :error="form.errors.last_name" />
          </div>
          <!-- Gender -->
          <div>
            <Label for="gender_id">Gender <span class="text-red-500">*</span></Label>
            <SelectInput
              id="gender_id"
              v-model="form.gender_id"
              :options="genderOptions"
              :error="form.errors.gender_id"
              placeholder="Select gender"
              required
            />
            <InputError :message="form.errors.gender_id" class="mt-2" />
          </div>
          <!-- Family Number -->
          <div>
            <Label for="family_no">Family Number *</Label>
            <FamilyNumberSearchDropdown v-model="form.family_no" :error="form.errors.family_no" required />
          </div>
          <!-- Father -->
          <div>
            <Label for="father_id">Father</Label>
            <MemberTypeSearchDropdown 
              v-model="form.father_id" 
              v-model:sourceType="form.father_source"
              :error="form.errors.father_id" 
              placeholder="Search for father"
              :existing-data="externalMember.father_data || undefined"
            />
          </div>
          <!-- Mother -->
          <div>
            <Label for="mother_id">Mother</Label>
            <MemberTypeSearchDropdown 
              v-model="form.mother_id" 
              v-model:sourceType="form.mother_source"
              :error="form.errors.mother_id" 
              placeholder="Search for mother"
              :existing-data="externalMember.mother_data || undefined"
            />
          </div>
          <!-- Spouse -->
          <div>
            <Label for="spouse_id">Spouse</Label>
            <MemberTypeSearchDropdown 
              v-model="form.spouse_id" 
              v-model:sourceType="form.spouse_source"
              :error="form.errors.spouse_id" 
              placeholder="Search for spouse"
              :existing-data="externalMember.spouse_data || undefined"
            />
          </div>
          <!-- Address -->
          <div class="md:col-span-2">
            <Label for="address">Address</Label>
            <TextareaInput 
              name="address"
              id="address" 
              v-model="form.address" 
              :error="form.errors.address" 
              :rows="3" 
              placeholder="Enter address" 
            />
          </div>
          <div class="grid gap-2">
            <Label for="relationship_id">
              Relationship <span class="text-red-500">*</span>
              <span class="text-sm text-gray-500 font-normal">(with the head of the family)</span>
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
        <div class="flex justify-end gap-4 mt-6">
          <Button type="button" variant="outline" @click="cancel">Cancel</Button>
          <Button type="submit" :disabled="form.processing">Update External Member</Button>
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
  form.put(`/external-members/${props.externalMember.id}`, {
    onError: () => {
      showValidationModal.value = true
    }
  })
}

const cancel = () => {
  router.visit('/external-members')
}
</script>
