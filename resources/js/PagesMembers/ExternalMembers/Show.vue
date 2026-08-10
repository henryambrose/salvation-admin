<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="External Member Details" />

    <div class="mx-auto max-w-3xl">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">External Member Details</h1>
          <p class="mt-1 text-sm text-gray-600">View information about this external family member.</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('external-members.edit', externalMember.id)"
            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
          >
            Edit
          </Link>
          <Link
            :href="route('external-members.index')"
            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-blue-900"
          >
            Back to List
          </Link>
        </div>
      </div>

      <!-- Member Information -->
      <div class="overflow-hidden bg-[#ffffff] shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <div class="flex items-center">
            <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-full bg-orange-100">
              <span class="text-lg font-medium text-orange-600">
                {{ getInitials(externalMember.first_name, externalMember.last_name) }}
              </span>
            </div>
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">{{ externalMember.first_name }} {{ externalMember.last_name }}</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">External Family Member</p>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-200">
          <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Full Name</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ externalMember.first_name }} {{ externalMember.last_name }}</dd>
            </div>

            <div class="bg-[#ffffff] px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Family Number</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                {{ externalMember.family_no }}
              </dd>
            </div>

            <div v-if="externalMember.address" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Address</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                {{ externalMember.address }}
              </dd>
            </div>

            <div v-if="externalMember.relationship" class="bg-[#ffffff] px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Relationship</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                {{ externalMember.relationship.name }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Family Relationships -->
      <div class="mt-8 overflow-hidden bg-[#ffffff] shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Family Relationships</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Family connections for this external member.</p>
        </div>

        <div class="border-t border-gray-200">
          <dl>
            <div v-if="externalMember.father" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Father</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                {{ externalMember.father.full_name }}
                <span
                  class="ml-2 rounded-full px-2 py-1 text-xs"
                  :class="externalMember.father.member_type === 'external' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                >
                  {{ externalMember.father.member_type === 'external' ? 'External' : 'Internal' }}
                </span>
              </dd>
            </div>

            <div v-if="externalMember.mother" class="bg-[#ffffff] px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Mother</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                {{ externalMember.mother.full_name }}
                <span
                  class="ml-2 rounded-full px-2 py-1 text-xs"
                  :class="externalMember.mother.member_type === 'external' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                >
                  {{ externalMember.mother.member_type === 'external' ? 'External' : 'Internal' }}
                </span>
              </dd>
            </div>

            <div v-if="externalMember.spouse" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Spouse</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                {{ externalMember.spouse.full_name }}
                <span
                  class="ml-2 rounded-full px-2 py-1 text-xs"
                  :class="externalMember.spouse.member_type === 'external' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                >
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

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  externalMember: Object,
});

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'External Members', href: '/external-members' },
  { title: props.externalMember.first_name, href: `/external-members/${props.externalMember.id}` },
];

const getInitials = (firstName, lastName) => {
  const first = firstName ? firstName.charAt(0).toUpperCase() : '';
  const last = lastName ? lastName.charAt(0).toUpperCase() : '';
  return first + last;
};
</script>
