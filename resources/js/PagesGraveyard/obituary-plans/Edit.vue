<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="'Edit Obituary Plan: ' + plan.name" />

    <div class="mx-auto max-w-2xl">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-blue-700">Edit Obituary Plan</h2>
        <p class="text-gray-600">Modify the obituary service plan settings.</p>
      </div>

      <form @submit.prevent="submit" class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
        <!-- Plan Name -->
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Plan Name <span class="text-red-500">*</span>
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.name }"
            placeholder="e.g., Basic Plan, Premium Plan"
          />
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
        </div>

        <!-- Description -->
        <div class="mb-4">
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Description
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.description }"
            placeholder="Describe what this plan offers..."
          ></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
        </div>

        <!-- Duration -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Plan Duration
          </label>
          <div class="space-y-3">
            <div class="flex items-center">
              <input
                id="lifetime"
                v-model="durationType"
                type="radio"
                value="lifetime"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500"
              />
              <label for="lifetime" class="ml-2 text-sm text-gray-700">Lifetime (No expiration)</label>
            </div>
            <div class="flex items-center">
              <input
                id="limited"
                v-model="durationType"
                type="radio"
                value="limited"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500"
              />
              <label for="limited" class="ml-2 text-sm text-gray-700">Limited duration</label>
            </div>

            <div v-if="durationType === 'limited'" class="ml-6 flex items-center gap-3">
              <input
                v-model.number="form.duration_in_days"
                type="number"
                min="1"
                class="w-24 rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.duration_in_days }"
                placeholder="30"
              />
              <span class="text-sm text-gray-600">days</span>
            </div>
          </div>
          <p v-if="errors.duration_in_days" class="mt-1 text-sm text-red-600">{{ errors.duration_in_days }}</p>
        </div>

        <!-- Cost -->
        <div class="mb-4">
          <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
            Cost <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₹</span>
            <input
              id="cost"
              v-model.number="form.cost"
              type="number"
              step="0.01"
              min="0"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 pl-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.cost }"
              placeholder="0.00"
            />
          </div>
          <p v-if="errors.cost" class="mt-1 text-sm text-red-600">{{ errors.cost }}</p>
        </div>

        <!-- Sort Order -->
        <div class="mb-4">
          <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
            Sort Order
          </label>
          <input
            id="sort_order"
            v-model.number="form.sort_order"
            type="number"
            min="0"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.sort_order }"
            placeholder="0"
          />
          <p class="mt-1 text-sm text-gray-600">Lower numbers appear first in lists</p>
          <p v-if="errors.sort_order" class="mt-1 text-sm text-red-600">{{ errors.sort_order }}</p>
        </div>

        <!-- Active Status -->
        <div class="mb-6">
          <div class="flex items-center">
            <input
              id="is_active"
              v-model="form.is_active"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 rounded"
            />
            <label for="is_active" class="ml-2 text-sm text-gray-700">
              Plan is active and available for selection
            </label>
          </div>
          <div v-if="hasUsage && !form.is_active" class="mt-2 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <div class="flex">
              <AlertTriangle class="h-5 w-5 text-yellow-400" />
              <div class="ml-3">
                <p class="text-sm text-yellow-700">
                  This plan has active obituary pages or payments. Deactivating it may affect existing users.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between">
          <div>
            <Button
              @click="router.visit('/graveyard/obituary-plans/' + plan.id)"
              variant="secondary"
              class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200"
            >
              View Details
            </Button>
          </div>
          <div class="flex gap-3">
            <Button
              type="button"
              @click="router.visit('/graveyard/obituary-plans')"
              variant="secondary"
              class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="processing"
              class="flex items-center gap-2 rounded-full bg-blue-600 px-6 py-2 text-white shadow transition hover:bg-blue-700 disabled:opacity-50"
            >
              <span v-if="processing">Updating...</span>
              <span v-else>Update Plan</span>
            </Button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { AlertTriangle } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';

interface Props {
  plan: any;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Obituary Plans', href: '/graveyard/obituary-plans' },
  { title: props.plan.name, href: '/graveyard/obituary-plans/' + props.plan.id },
  { title: 'Edit', href: '/graveyard/obituary-plans/' + props.plan.id + '/edit' },
];

// Form setup with existing plan data
const form = useForm({
  name: props.plan.name,
  description: props.plan.description || '',
  duration_in_days: props.plan.duration_in_days,
  cost: props.plan.cost,
  is_active: props.plan.is_active,
  sort_order: props.plan.sort_order,
});

// Duration type toggle
const durationType = ref(props.plan.duration_in_days ? 'limited' : 'lifetime');

// Check if plan has usage
const hasUsage = computed(() => {
  return (props.plan.obituary_pages_count || 0) > 0 || (props.plan.obituary_payments_count || 0) > 0;
});

// Watch duration type changes
watch(durationType, (newType) => {
  if (newType === 'lifetime') {
    form.duration_in_days = null;
  } else if (newType === 'limited' && form.duration_in_days === null) {
    form.duration_in_days = 30; // Default to 30 days
  }
});

// Form submission
const submit = () => {
  form.put('/graveyard/obituary-plans/' + props.plan.id, {
    onSuccess: () => {
      // Redirect will be handled by the controller
    },
  });
};

// Extract errors for easier access
const { errors, processing } = form;
</script>