<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { parseLocalDate } from '@/lib/utils';
import { Head } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, DollarSign, Edit, Eye, FileText, MapPin, User } from 'lucide-vue-next';

interface ContributionType {
  id: number;
  name: string;
  description: string;
}

interface User {
  id: number;
  name: string;
}

interface CommunityContribution {
  id: number;
  contribution_type_id: number;
  collection_date: string;
  amount: string;
  description: string;
  location: string;
  collected_by_user_id: number | null;
  notes: string;
  status: string;
  created_at: string;
  updated_at: string;
  contribution_type: ContributionType;
  collected_by: User | null;
}

defineProps<{
  communityContribution: CommunityContribution;
}>();

const breadcrumbs = [
  { title: 'Fund Management', href: '/fund' },
  { title: 'Community Contributions', href: '/fund/community-contributions' },
  { title: 'View Details' },
];

const formatCurrency = (amount: string) => {
  const number = parseFloat(amount);
  if (isNaN(number)) return '₹0.00';
  return (
    '₹' +
    number.toLocaleString('en-IN', {
      maximumFractionDigits: 2,
      minimumFractionDigits: 2,
    })
  );
};

const formatDate = (dateString: string) => {
  const date = parseLocalDate(dateString);
  return date
    ? date.toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    : '';
};

const getStatusBadgeClass = (status: string) => {
  const classes = {
    recorded: 'bg-blue-100 text-blue-800',
    verified: 'bg-green-100 text-green-800',
    deposited: 'bg-purple-100 text-purple-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
  const labels = {
    recorded: 'Recorded',
    verified: 'Verified',
    deposited: 'Deposited',
    cancelled: 'Cancelled',
  };
  return labels[status as keyof typeof labels] || status;
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Community Contribution Details" />

    <div class="mx-auto max-w-4xl">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Community Contribution Details</h1>
          <p class="mt-2 text-gray-600">View community collection information</p>
        </div>
        <div class="flex space-x-3">
          <Button @click="$inertia.visit('/fund/community-contributions')" variant="outline">
            <ArrowLeft class="mr-2 h-4 w-4" />
            Back to List
          </Button>
          <Button @click="$inertia.visit(`/fund/community-contributions/${communityContribution.id}/edit`)" variant="default">
            <Edit class="mr-2 h-4 w-4" />
            Edit
          </Button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="grid gap-6">
        <!-- Collection Overview -->
        <Card>
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle class="flex items-center">
                  <Eye class="mr-2 h-5 w-5" />
                  Collection Overview
                </CardTitle>
                <CardDescription> Basic information about this community contribution </CardDescription>
              </div>
              <span :class="['rounded-full px-3 py-1 text-sm font-medium', getStatusBadgeClass(communityContribution.status)]">
                {{ getStatusLabel(communityContribution.status) }}
              </span>
            </div>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <!-- Amount -->
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <DollarSign class="h-5 w-5 text-green-600" />
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Amount</p>
                  <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(communityContribution.amount) }}</p>
                </div>
              </div>

              <!-- Collection Date -->
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <Calendar class="h-5 w-5 text-blue-600" />
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Collection Date</p>
                  <p class="text-lg font-semibold text-gray-900">{{ formatDate(communityContribution.collection_date) }}</p>
                </div>
              </div>

              <!-- Contribution Type -->
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <FileText class="h-5 w-5 text-purple-600" />
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Contribution Type</p>
                  <p class="text-lg font-semibold text-gray-900">{{ communityContribution.contribution_type.name }}</p>
                  <p v-if="communityContribution.contribution_type.description" class="text-sm text-gray-600">
                    {{ communityContribution.contribution_type.description }}
                  </p>
                </div>
              </div>

              <!-- Collected By -->
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <User class="h-5 w-5 text-indigo-600" />
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Collected By</p>
                  <p class="text-lg font-semibold text-gray-900">
                    {{ communityContribution.collected_by?.name || 'Not specified' }}
                  </p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Additional Details -->
        <Card>
          <CardHeader>
            <CardTitle>Additional Information</CardTitle>
            <CardDescription> Extra details about this collection </CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-6">
              <!-- Description -->
              <div v-if="communityContribution.description">
                <h4 class="mb-2 text-sm font-medium text-gray-500">Description</h4>
                <p class="text-gray-900">{{ communityContribution.description }}</p>
              </div>

              <!-- Location -->
              <div v-if="communityContribution.location">
                <h4 class="mb-2 flex items-center text-sm font-medium text-gray-500">
                  <MapPin class="mr-1 h-4 w-4" />
                  Location
                </h4>
                <p class="text-gray-900">{{ communityContribution.location }}</p>
              </div>

              <!-- Notes -->
              <div v-if="communityContribution.notes">
                <h4 class="mb-2 text-sm font-medium text-gray-500">Notes</h4>
                <div class="rounded-lg bg-gray-50 p-4">
                  <p class="whitespace-pre-wrap text-gray-900">{{ communityContribution.notes }}</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- System Information -->
        <Card>
          <CardHeader>
            <CardTitle>System Information</CardTitle>
            <CardDescription> Record creation and modification details </CardDescription>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div>
                <h4 class="mb-1 text-sm font-medium text-gray-500">Created At</h4>
                <p class="text-gray-900">{{ formatDate(communityContribution.created_at) }}</p>
              </div>
              <div>
                <h4 class="mb-1 text-sm font-medium text-gray-500">Last Updated</h4>
                <p class="text-gray-900">{{ formatDate(communityContribution.updated_at) }}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
