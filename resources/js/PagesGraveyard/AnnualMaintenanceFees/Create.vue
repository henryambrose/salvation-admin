<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Add Annual Maintenance Fee" />

    <div class="mx-auto max-w-3xl">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Add Annual Maintenance Fee</h1>
        <p class="text-gray-600 mt-1">Set up maintenance fees for a specific year</p>
        <p class="text-sm text-blue-600 mt-2">
          <strong>Note:</strong> You can set either permanent grave amount, niche amount, or both. At least one amount is required.
        </p>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <form @submit.prevent="submit">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Year -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Year <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.year"
                type="number"
                :min="2020"
                :max="new Date().getFullYear() + 10"
                class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.year }"
              />
              <p v-if="form.errors.year" class="mt-1 text-sm text-red-600">{{ form.errors.year }}</p>
              <p class="mt-1 text-sm text-gray-500">Suggested: {{ suggestedYear }}</p>
            </div>

            <!-- Permanent Grave Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Permanent Grave Amount (₹) <span class="text-gray-500">(Optional)</span>
              </label>
              <input
                v-model="form.permanent_grave_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.permanent_grave_amount }"
                placeholder="5000.00"
              />
              <p v-if="form.errors.permanent_grave_amount" class="mt-1 text-sm text-red-600">{{ form.errors.permanent_grave_amount }}</p>
              <p v-if="previousFee?.permanent_grave_amount" class="mt-1 text-sm text-gray-500">
                Previous year: ₹{{ Number(previousFee.permanent_grave_amount).toLocaleString() }}
              </p>
            </div>

            <!-- Niche Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Niche Amount (₹) <span class="text-gray-500">(Optional)</span>
              </label>
              <input
                v-model="form.niche_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.niche_amount }"
                placeholder="3000.00"
              />
              <p v-if="form.errors.niche_amount" class="mt-1 text-sm text-red-600">{{ form.errors.niche_amount }}</p>
              <p v-if="previousFee?.niche_amount" class="mt-1 text-sm text-gray-500">
                Previous year: ₹{{ Number(previousFee.niche_amount).toLocaleString() }}
              </p>
            </div>

            <!-- Effective From -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Effective From <span class="text-red-500">*</span>
              </label>
              <DateInput v-model="form.effective_from" class="w-full" />
              <p v-if="form.errors.effective_from" class="mt-1 text-sm text-red-600">{{ form.errors.effective_from }}</p>
            </div>

            <!-- Effective Until -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Effective Until
              </label>
              <DateInput v-model="form.effective_until" class="w-full" />
              <p v-if="form.errors.effective_until" class="mt-1 text-sm text-red-600">{{ form.errors.effective_until }}</p>
              <p class="mt-1 text-sm text-gray-500">Leave blank for no end date</p>
            </div>
          </div>

          <!-- Active Status -->
          <div class="mt-6">
            <label class="flex items-center">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="rounded border-gray-300 focus:ring-2 focus:ring-blue-500"
              />
              <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
          </div>

          <!-- Notes -->
          <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Notes
            </label>
            <textarea
              v-model="form.notes"
              rows="3"
              class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-300': form.errors.notes }"
              placeholder="Optional notes about this fee structure..."
            ></textarea>
            <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
          </div>

          <!-- Previous Year Reference -->
          <div v-if="previousFee" class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="text-sm font-medium text-blue-900 mb-2">Previous Year Reference ({{ previousFee.year }})</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
              <div v-if="previousFee.permanent_grave_amount">
                <strong>Permanent Grave:</strong> ₹{{ Number(previousFee.permanent_grave_amount).toLocaleString() }}
              </div>
              <div v-if="previousFee.niche_amount">
                <strong>Niche:</strong> ₹{{ Number(previousFee.niche_amount).toLocaleString() }}
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
            <Button
              type="button"
              @click="router.visit('/graveyard/annual-maintenance-fees')"
              variant="outline"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="form.processing"
              class="flex items-center gap-2"
            >
              <span v-if="form.processing">Creating...</span>
              <span v-else>Create Annual Fee</span>
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { DateInput } from '@/components/ui/date-input';
import { formatDateForDisplay } from '@/lib/utils';
import { useToast } from '@/composables/useToast';

const props = defineProps({
  suggestedYear: Number,
  previousFee: Object,
})

const breadcrumbs = [
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Annual Maintenance Fees', href: '/graveyard/annual-maintenance-fees' },
  { title: 'Add Fee' }, // No href for current page
]

const form = useForm({
  year: props.suggestedYear || new Date().getFullYear(),
  permanent_grave_amount: '',
  niche_amount: '',
  effective_from: `${props.suggestedYear || new Date().getFullYear()}-01-01`,
  effective_until: '',
  is_active: true,
  notes: '',
})

const { error } = useToast();

const submit = () => {
  // Client-side validation to ensure at least one amount is provided
  if ((!form.permanent_grave_amount || form.permanent_grave_amount <= 0) &&
      (!form.niche_amount || form.niche_amount <= 0)) {
    error('Please provide either permanent grave amount or niche amount (or both).');
    return;
  }

  form.post(route('graveyard.annual-maintenance-fees.store'))
}

// Watch for year changes to auto-populate effective_from
watch(() => form.year, (newYear) => {
  if (newYear) {
    form.effective_from = `${newYear}-01-01`
  }
})
</script>