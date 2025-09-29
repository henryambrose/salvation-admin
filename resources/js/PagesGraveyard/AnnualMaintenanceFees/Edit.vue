<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Edit Annual Maintenance Fee" />

    <div class="mx-auto max-w-3xl">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Edit Annual Maintenance Fee ({{ fee.year }})</h1>
        <p class="text-gray-600 mt-1">Modify maintenance fee details</p>
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
            </div>

            <!-- Permanent Grave Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Permanent Grave Amount (₹) <span class="text-red-500">*</span>
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
            </div>

            <!-- Niche Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Niche Amount (₹) <span class="text-red-500">*</span>
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
            </div>

            <!-- Effective From -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Effective From <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.effective_from"
                type="date"
                class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.effective_from }"
              />
              <p v-if="form.errors.effective_from" class="mt-1 text-sm text-red-600">{{ form.errors.effective_from }}</p>
            </div>

            <!-- Effective Until -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Effective Until
              </label>
              <input
                v-model="form.effective_until"
                type="date"
                class="w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-300': form.errors.effective_until }"
              />
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
              <span v-if="form.processing">Updating...</span>
              <span v-else>Update Annual Fee</span>
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'

const props = defineProps({
  fee: Object,
})

const breadcrumbs = [
  { name: 'Graveyard', href: '/graveyard' },
  { name: 'Annual Maintenance Fees', href: '/graveyard/annual-maintenance-fees' },
  { name: `Edit ${props.fee.year}`, href: null },
]

const form = useForm({
  year: props.fee.year,
  permanent_grave_amount: props.fee.permanent_grave_amount || '',
  niche_amount: props.fee.niche_amount || '',
  effective_from: props.fee.effective_from,
  effective_until: props.fee.effective_until || '',
  is_active: props.fee.is_active,
  notes: props.fee.notes || '',
})

const submit = () => {
  form.put(`/graveyard/annual-maintenance-fees/${props.fee.id}`)
}

</script>