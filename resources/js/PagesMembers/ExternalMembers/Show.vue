<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="External Member Details" />
    
    <div class="max-w-3xl mx-auto">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">External Member Details</h1>
          <p class="mt-1 text-sm text-gray-600">
            View information about this external family member.
          </p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('external-members.edit', externalMember.id)"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Edit
          </Link>
          <Link
            :href="route('external-members.index')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            Back to List
          </Link>
        </div>
      </div>

      <!-- Member Information -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <div class="flex items-center">
            <div class="h-12 w-12 rounded-full bg-orange-100 flex items-center justify-center mr-4">
              <span class="text-orange-600 font-medium text-lg">
                {{ getInitials(externalMember.first_name, externalMember.last_name) }}
              </span>
            </div>
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ externalMember.first_name }} {{ externalMember.last_name }}
              </h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                External Family Member
              </p>
            </div>
          </div>
        </div>
        
        <div class="border-t border-gray-200">
          <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Full Name</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ externalMember.first_name }} {{ externalMember.last_name }}
              </dd>
            </div>
            
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Family Number</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ externalMember.family_no }}
              </dd>
            </div>
            
            <div v-if="externalMember.address" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Address</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ externalMember.address }}
              </dd>
            </div>
            
            <div v-if="externalMember.relationship" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Relationship</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ externalMember.relationship.name }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Family Relationships -->
      <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Family Relationships</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Family connections for this external member.
          </p>
        </div>
        
        <div class="border-t border-gray-200">
          <dl>
            <div v-if="externalMember.father" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Father</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ externalMember.father.full_name }}
                <span class="ml-2 px-2 py-1 text-xs rounded-full" 
                      :class="externalMember.father.member_type === 'external' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'">
                  {{ externalMember.father.member_type === 'external' ? 'External' : 'Internal' }}
                </span>
              </dd>
            </div>
            
            <div v-if="externalMember.mother" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Mother</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ externalMember.mother.full_name }}
                <span class="ml-2 px-2 py-1 text-xs rounded-full" 
                      :class="externalMember.mother.member_type === 'external' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'">
                  {{ externalMember.mother.member_type === 'external' ? 'External' : 'Internal' }}
                </span>
              </dd>
            </div>
            
            <div v-if="externalMember.spouse" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Spouse</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ externalMember.spouse.full_name }}
                <span class="ml-2 px-2 py-1 text-xs rounded-full" 
                      :class="externalMember.spouse.member_type === 'external' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'">
                  {{ externalMember.spouse.member_type === 'external' ? 'External' : 'Internal' }}
                </span>
              </dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
  externalMember: Object,
})

const breadcrumbs = [
  { name: 'Dashboard', href: '/dashboard' },
  { name: 'External Members', href: '/external-members' },
  { name: externalMember.first_name, href: `/external-members/${externalMember.id}` },
]

const getInitials = (firstName, lastName) => {
  const first = firstName ? firstName.charAt(0).toUpperCase() : ''
  const last = lastName ? lastName.charAt(0).toUpperCase() : ''
  return first + last
}
</script>
