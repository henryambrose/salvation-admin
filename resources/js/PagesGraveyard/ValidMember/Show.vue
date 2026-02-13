<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="`Valid Member - ${validMember.full_name}`" />
    
    <div class="mx-auto max-w-3xl">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">{{ validMember.full_name }}</h1>
          <p class="mt-2 text-gray-600">Valid member details and grave information</p>
        </div>
        <div class="flex items-center gap-3">
          <Button
            v-if="canUpdateValidMember"
            @click="router.visit(`/graveyard/valid-members/${validMember.id}/edit`)"
            variant="outline"
            class="flex items-center gap-2"
          >
            <Pencil class="h-4 w-4" />
            Edit
          </Button>
          <Button
            @click="router.visit('/graveyard/valid-members')"
            variant="outline"
          >
            Back to List
          </Button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Member Information -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center gap-2">
            <User class="h-5 w-5 text-blue-600" />
            <h2 class="text-lg font-semibold text-gray-900">Member Information</h2>
          </div>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-500">Name</label>
              <div class="mt-1 text-base text-gray-900">{{ validMember.full_name }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-500">Type</label>
              <div class="mt-1">
                <span 
                  class="inline-flex rounded-full px-2 py-1 text-sm font-semibold"
                  :class="validMember.is_parish_member ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'"
                >
                  {{ validMember.is_parish_member ? 'Parish Member' : 'External Person' }}
                </span>
              </div>
            </div>

            <div v-if="validMember.member && validMember.is_parish_member">
              <label class="block text-sm font-medium text-gray-500">Family Number</label>
              <div class="mt-1 text-base text-gray-900">{{ validMember.member.family_number }}</div>
            </div>

            <div v-if="validMember.contact_no">
              <label class="block text-sm font-medium text-gray-500">Contact Number</label>
              <div class="mt-1 text-base text-gray-900">
                <a :href="`tel:${validMember.contact_no}`" class="text-blue-600 hover:text-blue-800">
                  {{ validMember.contact_no }}
                </a>
              </div>
            </div>

            <div v-if="validMember.aadhar_no">
              <label class="block text-sm font-medium text-gray-500">Aadhar Number</label>
              <div class="mt-1 text-base text-gray-900">{{ validMember.aadhar_no }}</div>
            </div>
          </div>
        </div>

        <!-- Grave Information -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center gap-2">
            <MapPin class="h-5 w-5 text-purple-600" />
            <h2 class="text-lg font-semibold text-gray-900">Grave Information</h2>
          </div>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-500">Grave Type</label>
              <div class="mt-1">
                <span 
                  class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                  :class="validMember.grave_type === 'permanent_grave' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'"
                >
                  {{ validMember.grave_type === 'permanent_grave' ? 'Permanent Grave' : 'Niche' }}
                </span>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-500">Grave Number</label>
              <div class="mt-1 text-base font-medium text-gray-900">
                {{ getGraveIdentifier() }}
              </div>
            </div>

            <div v-if="getGraveLocation()">
              <label class="block text-sm font-medium text-gray-500">Location</label>
              <div class="mt-1 text-base text-gray-900">{{ getGraveLocation() }}</div>
            </div>

            <div v-if="getGraveDimensions()">
              <label class="block text-sm font-medium text-gray-500">Dimensions</label>
              <div class="mt-1 text-base text-gray-900">{{ getGraveDimensions() }}</div>
            </div>

            <div v-if="getGraveStatus()">
              <label class="block text-sm font-medium text-gray-500">Status</label>
              <div class="mt-1">
                <span class="inline-flex rounded-full px-2 py-1 text-sm font-semibold" :class="getStatusClass()">
                  {{ getGraveStatus() }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Registration Information -->
        <div class="lg:col-span-2 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center gap-2">
            <Calendar class="h-5 w-5 text-green-600" />
            <h2 class="text-lg font-semibold text-gray-900">Registration Information</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-500">Created</label>
              <div class="mt-1 text-base text-gray-900">{{ formatDate(validMember.created_at) }}</div>
            </div>

            <div v-if="validMember.updated_at !== validMember.created_at">
              <label class="block text-sm font-medium text-gray-500">Last Updated</label>
              <div class="mt-1 text-base text-gray-900">{{ formatDate(validMember.updated_at) }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-500">Status</label>
              <div class="mt-1">
                <span class="inline-flex rounded-full px-2 py-1 text-sm font-semibold bg-green-100 text-green-800">
                  Active
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Other Valid Members for this Grave -->
        <div v-if="otherValidMembers.length > 0" class="lg:col-span-2 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center gap-2">
            <Users class="h-5 w-5 text-orange-600" />
            <h2 class="text-lg font-semibold text-gray-900">Other Valid Members for this Grave</h2>
          </div>
          
          <div class="space-y-2">
            <div 
              v-for="member in otherValidMembers" 
              :key="member.id"
              class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-md"
            >
              <div>
                <div class="font-medium text-gray-900">{{ member.full_name }}</div>
                <div class="text-sm text-gray-500">
                  {{ member.is_parish_member ? `Family: ${member.member?.family_number}` : 'External Member' }}
                </div>
              </div>
              <Button
                @click="router.visit(`/graveyard/valid-members/${member.id}`)"
                variant="outline"
                size="sm"
              >
                View
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { parseLocalDate } from '@/lib/utils';
import { Calendar, MapPin, Pencil, User, Users } from 'lucide-vue-next';

const { can } = permissionHelpers();

// Permission checks
const canUpdateValidMember = can('update-valid-member');

interface Props {
  validMember: {
    id: number;
    permanent_grave_id: number | null;
    niche_id: number | null;
    member_id: number | null;
    first_name: string;
    last_name: string;
    contact_no: string | null;
    aadhar_no: string | null;
    full_name: string;
    is_parish_member: boolean;
    grave_type: 'permanent_grave' | 'niche';
    created_at: string;
    updated_at: string;
    member?: {
      id: number;
      first_name: string;
      last_name: string;
      family_number: string;
      contact_no: string | null;
    };
    permanent_grave?: {
      id: number;
      grave_no: string;
      section: string;
      row_no: number;
      status?: string;
    };
    niche?: {
      id: number;
      niche_no: string;
      location: string;
      size_width?: number;
      size_height?: number;
      size_depth?: number;
      status?: string;
    };
  };
  otherValidMembers: Array<{
    id: number;
    full_name: string;
    is_parish_member: boolean;
    member?: {
      family_number: string;
    };
  }>;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Valid Members', href: '/graveyard/valid-members' },
  { title: props.validMember.full_name, href: `/graveyard/valid-members/${props.validMember.id}` },
];

// Helper functions
const getGraveIdentifier = () => {
  if (props.validMember.permanent_grave) {
    return `G${props.validMember.permanent_grave.grave_no}`;
  } else if (props.validMember.niche) {
    return `N${props.validMember.niche.niche_no}`;
  }
  return '-';
};

const getGraveLocation = () => {
  if (props.validMember.permanent_grave) {
    return `Section ${props.validMember.permanent_grave.section}, Row ${props.validMember.permanent_grave.row_no}`;
  } else if (props.validMember.niche) {
    return props.validMember.niche.location;
  }
  return null;
};

const getGraveDimensions = () => {
  if (props.validMember.niche) {
    const niche = props.validMember.niche;
    if (niche.size_width && niche.size_height && niche.size_depth) {
      return `${niche.size_width}" × ${niche.size_height}" × ${niche.size_depth}"`;
    }
  }
  return null;
};

const getGraveStatus = () => {
  if (props.validMember.permanent_grave) {
    return props.validMember.permanent_grave.status;
  } else if (props.validMember.niche) {
    return props.validMember.niche.status;
  }
  return null;
};

const getStatusClass = () => {
  const status = getGraveStatus();
  const classes = {
    available: 'bg-green-100 text-green-800',
    occupied: 'bg-red-100 text-red-800',
    reserved: 'bg-yellow-100 text-yellow-800',
    maintenance: 'bg-orange-100 text-orange-800',
  };
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString: string) => {
  const date = parseLocalDate(dateString);
  return date ? date.toLocaleDateString('en-IN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }) : '';
};
</script>

<style scoped>
/* Custom styles for the show page */
</style>