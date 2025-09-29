<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="`Annual Maintenance Fee - ${fee.year}`" />

    <div class="mx-auto max-w-4xl">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-blue-700">Annual Maintenance Fee - {{ fee.year }}</h1>
          <p class="text-gray-600 mt-1">View fee details</p>
        </div>
        <div class="flex items-center space-x-3">
          <Button
            @click="router.visit(`/graveyard/annual-maintenance-fees/${fee.id}/edit`)"
            variant="outline"
          >
            <Edit class="h-4 w-4 mr-2" />
            Edit
          </Button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Basic Information -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Fee Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-500">Year</label>
                <p class="mt-1 text-sm text-gray-900">{{ fee.year }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Permanent Grave Amount</label>
                <p class="mt-1 text-lg font-semibold text-green-600">{{ fee.formatted_permanent_grave_amount }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Niche Amount</label>
                <p class="mt-1 text-lg font-semibold text-blue-600">{{ fee.formatted_niche_amount }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Effective From</label>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(fee.effective_from) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Effective Until</label>
                <p class="mt-1 text-sm text-gray-900">
                  {{ fee.effective_until ? formatDate(fee.effective_until) : 'No end date' }}
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500">Status</label>
                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="fee.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ fee.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
            <div v-if="fee.notes" class="mt-6">
              <label class="block text-sm font-medium text-gray-500">Notes</label>
              <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ fee.notes }}</p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <Button
                @click="router.visit(`/graveyard/annual-maintenance-fees/${fee.id}/edit`)"
                variant="outline"
                class="w-full justify-start"
              >
                <Edit class="h-4 w-4 mr-2" />
                Edit Fee
              </Button>
              <Button
                @click="deleteFee"
                variant="outline"
                class="w-full justify-start text-red-600 hover:text-red-800"
              >
                <Trash2 class="h-4 w-4 mr-2" />
                Delete Fee
              </Button>
            </div>
          </div>

          <!-- Audit Information -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Audit Information</h3>
            <div class="space-y-3 text-sm">
              <div>
                <label class="block font-medium text-gray-500">Created By</label>
                <p class="mt-1 text-gray-900">{{ fee.creator?.name || 'System' }}</p>
              </div>
              <div>
                <label class="block font-medium text-gray-500">Created At</label>
                <p class="mt-1 text-gray-900">{{ formatDateTime(fee.created_at) }}</p>
              </div>
              <div v-if="fee.updater">
                <label class="block font-medium text-gray-500">Last Updated By</label>
                <p class="mt-1 text-gray-900">{{ fee.updater.name }}</p>
              </div>
              <div>
                <label class="block font-medium text-gray-500">Updated At</label>
                <p class="mt-1 text-gray-900">{{ formatDateTime(fee.updated_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Modal -->
      <transition name="fade">
        <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
          <div class="bg-opacity-50 absolute inset-0 bg-black" @click="showDeleteModal = false"></div>
          <div class="from-grey-900 via-grey-800 to-grey-600 relative z-10 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
            <div class="rounded-lg bg-[#ffffff] p-6">
              <h3 class="mb-4 text-xl font-semibold">Delete Annual Maintenance Fee</h3>
              <p>
                Are you sure you want to delete the maintenance fee for
                <span class="font-bold">{{ fee.year }}</span>?
              </p>
              <div class="mt-6 flex justify-end space-x-2">
                <Button
                  type="button"
                  @click="showDeleteModal = false"
                  class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200"
                >
                  Cancel
                </Button>
                <Button
                  type="button"
                  @click="confirmDelete"
                  class="flex items-center gap-2 rounded-full bg-red-600 px-6 py-2 text-white shadow transition hover:bg-red-700"
                >
                  Delete
                </Button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Edit, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  fee: Object,
})

const showDeleteModal = ref(false)

const breadcrumbs = computed(() => [
  { name: 'Graveyard', href: '/graveyard' },
  { name: 'Annual Maintenance Fees', href: '/graveyard/annual-maintenance-fees' },
  { name: `Year ${props.fee.year}`, href: null },
])

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatDateTime = (dateString: string) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const deleteFee = () => {
  showDeleteModal.value = true
}

const confirmDelete = () => {
  router.delete(`/graveyard/annual-maintenance-fees/${props.fee.id}`, {
    onSuccess: () => {
      router.visit('/graveyard/annual-maintenance-fees')
    }
  })
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>