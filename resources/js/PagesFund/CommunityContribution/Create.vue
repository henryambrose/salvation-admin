<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { DateInput } from '@/components/ui/date-input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

interface ContributionType {
  id: number;
  name: string;
  description: string;
}

interface User {
  id: number;
  name: string;
}

defineProps<{
  contributionTypes: ContributionType[];
  users: User[];
  statusOptions: Record<string, string>;
}>();

const breadcrumbs = [
  { title: 'Fund Management', href: '/fund' },
  { title: 'Community Contributions', href: '/fund/community-contributions' },
  { title: 'Create New' },
];

const form = useForm({
  contribution_type_id: '',
  collection_date: new Date().toISOString().split('T')[0],
  amount: '',
  description: '',
  location: '',
  collected_by_user_id: '',
  notes: '',
  status: 'recorded',
});

const submitForm = () => {
  form.post('/fund/community-contributions', {
    onSuccess: () => {
      // Success handled by redirect
    },
  });
};

const formatCurrency = (value: string) => {
  if (!value) return '';
  const number = parseFloat(value.replace(/[^\d.]/g, ''));
  if (isNaN(number)) return '';
  return number.toLocaleString('en-IN', {
    maximumFractionDigits: 2,
    minimumFractionDigits: 2,
  });
};

const handleAmountInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const value = target.value.replace(/[^\d.]/g, '');
  form.amount = value;
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Create Community Contribution" />

    <div class="mx-auto max-w-4xl">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Create Community Contribution</h1>
          <p class="mt-2 text-gray-600">Record a new community collection</p>
        </div>
        <Button @click="$inertia.visit('/fund/community-contributions')" variant="outline">
          <ArrowLeft class="mr-2 h-4 w-4" />
          Back to List
        </Button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Collection Details</CardTitle>
            <CardDescription> Basic information about the community contribution </CardDescription>
          </CardHeader>
          <CardContent class="grid gap-6 sm:grid-cols-2">
            <!-- Contribution Type -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700"> Contribution Type <span class="text-red-500">*</span> </label>
              <select
                v-model="form.contribution_type_id"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.contribution_type_id }"
              >
                <option value="">Select a contribution type</option>
                <option v-for="type in contributionTypes" :key="type.id" :value="type.id">
                  {{ type.name }}
                </option>
              </select>
              <div v-if="form.errors.contribution_type_id" class="mt-1 text-sm text-red-600">
                {{ form.errors.contribution_type_id }}
              </div>
            </div>

            <!-- Collection Date -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700"> Collection Date <span class="text-red-500">*</span> </label>
              <DateInput v-model="form.collection_date" class="w-full" />
              <div v-if="form.errors.collection_date" class="mt-1 text-sm text-red-600">
                {{ form.errors.collection_date }}
              </div>
            </div>

            <!-- Amount -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700"> Amount (₹) <span class="text-red-500">*</span> </label>
              <input
                v-model="form.amount"
                @input="handleAmountInput"
                type="text"
                placeholder="0.00"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.amount }"
              />
              <div v-if="form.amount" class="mt-1 text-sm text-gray-500">Formatted: ₹{{ formatCurrency(form.amount) }}</div>
              <div v-if="form.errors.amount" class="mt-1 text-sm text-red-600">
                {{ form.errors.amount }}
              </div>
            </div>

            <!-- Status -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700"> Status <span class="text-red-500">*</span> </label>
              <select
                v-model="form.status"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.status }"
              >
                <option v-for="(label, value) in statusOptions" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
              <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                {{ form.errors.status }}
              </div>
            </div>

            <!-- Description -->
            <div class="sm:col-span-2">
              <label class="mb-2 block text-sm font-medium text-gray-700"> Description </label>
              <input
                v-model="form.description"
                type="text"
                placeholder="Brief description of the collection"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.description }"
              />
              <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                {{ form.errors.description }}
              </div>
            </div>

            <!-- Location -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700"> Location </label>
              <input
                v-model="form.location"
                type="text"
                placeholder="Collection location"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.location }"
              />
              <div v-if="form.errors.location" class="mt-1 text-sm text-red-600">
                {{ form.errors.location }}
              </div>
            </div>

            <!-- Collected By -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700"> Collected By </label>
              <select
                v-model="form.collected_by_user_id"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.collected_by_user_id }"
              >
                <option value="">Select collector</option>
                <option v-for="user in users" :key="user.id" :value="user.id">
                  {{ user.name }}
                </option>
              </select>
              <div v-if="form.errors.collected_by_user_id" class="mt-1 text-sm text-red-600">
                {{ form.errors.collected_by_user_id }}
              </div>
            </div>

            <!-- Notes -->
            <div class="sm:col-span-2">
              <label class="mb-2 block text-sm font-medium text-gray-700"> Notes </label>
              <textarea
                v-model="form.notes"
                rows="3"
                placeholder="Additional notes about this collection"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.notes }"
              ></textarea>
              <div v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                {{ form.errors.notes }}
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4">
          <Button @click="$inertia.visit('/fund/community-contributions')" type="button" variant="outline"> Cancel </Button>
          <Button type="submit" :disabled="form.processing" class="min-w-[120px]">
            <Save class="mr-2 h-4 w-4" />
            {{ form.processing ? 'Saving...' : 'Save Contribution' }}
          </Button>
        </div>

        <!-- Error Summary -->
        <div v-if="Object.keys(form.errors).length > 0" class="rounded-md bg-red-50 p-4">
          <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
          <ul class="mt-2 list-inside list-disc text-sm text-red-700">
            <li v-for="(error, field) in form.errors" :key="field">
              {{ error }}
            </li>
          </ul>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
