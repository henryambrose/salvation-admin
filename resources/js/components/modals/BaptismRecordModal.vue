<template>
  <Dialog :open="open" @update:open="handleClose">
    <DialogContent class="max-w-3xl max-h-[90vh] overflow-y-auto">
      <DialogHeader>
        <DialogTitle>Baptism Record</DialogTitle>
      </DialogHeader>

      <div v-if="loading" class="flex items-center justify-center py-8">
        <div class="text-gray-500">Loading...</div>
      </div>

      <div v-else-if="error" class="rounded-lg bg-red-50 p-4 text-red-800">
        {{ error }}
      </div>

      <div v-else-if="record" class="space-y-6">
        <!-- Member Information -->
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
          <h3 class="mb-3 font-semibold text-gray-700">Member Information</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-600">Name:</label>
              <p class="text-gray-900">{{ record.member?.first_name }} {{ record.member?.middle_name }} {{ record.member?.last_name }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Date of Birth:</label>
              <p class="text-gray-900">{{ formatDate(record.member?.date_of_birth) }}</p>
            </div>
          </div>
        </div>

        <!-- Baptism Details -->
        <div class="rounded-lg border border-gray-200 p-4">
          <h3 class="mb-3 font-semibold text-gray-700">Baptism Details</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-600">Baptism Date:</label>
              <p class="text-gray-900">{{ record.formatted_baptism_date || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Registration No:</label>
              <p class="text-gray-900">{{ record.baptism_reg_no || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Place of Baptism:</label>
              <p class="text-gray-900">{{ record.place_of_baptism || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Parish:</label>
              <p class="text-gray-900">{{ record.baptism_parish?.name || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Birth Information -->
        <div class="rounded-lg border border-gray-200 p-4">
          <h3 class="mb-3 font-semibold text-gray-700">Birth Information</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-600">Place of Birth:</label>
              <p class="text-gray-900">{{ record.place_of_birth || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Nationality:</label>
              <p class="text-gray-900">{{ record.nationality || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Parents Information -->
        <div class="rounded-lg border border-gray-200 p-4">
          <h3 class="mb-3 font-semibold text-gray-700">Parents</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-600">Father Name:</label>
              <p class="text-gray-900">{{ record.father_name || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Mother Name:</label>
              <p class="text-gray-900">{{ record.mother_name || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Father Residence:</label>
              <p class="text-gray-900">{{ record.father_residence || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Father Profession:</label>
              <p class="text-gray-900">{{ record.father_profession || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Godparents Information -->
        <div class="rounded-lg border border-gray-200 p-4">
          <h3 class="mb-3 font-semibold text-gray-700">Godparents</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-600">Godfather Name:</label>
              <p class="text-gray-900">{{ record.godfather_name || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Godfather Residence:</label>
              <p class="text-gray-900">{{ record.godfather_residence || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Godmother Name:</label>
              <p class="text-gray-900">{{ record.godmother_name || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Godmother Residence:</label>
              <p class="text-gray-900">{{ record.godmother_residence || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Minister & Remarks -->
        <div class="rounded-lg border border-gray-200 p-4">
          <h3 class="mb-3 font-semibold text-gray-700">Additional Information</h3>
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-600">Minister Name:</label>
              <p class="text-gray-900">{{ record.minister_name || 'N/A' }}</p>
            </div>
            <div v-if="record.baptism_remarks">
              <label class="text-sm font-medium text-gray-600">Remarks:</label>
              <p class="text-gray-900">{{ record.baptism_remarks }}</p>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 border-t pt-4">
          <Button @click="printCertificate" class="flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Certificate
          </Button>
          <Button @click="editRecord" variant="outline" class="flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Record
          </Button>
          <Button @click="handleClose" variant="outline">
            Close
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { router } from '@inertiajs/vue3'

interface Props {
  open: boolean
  recordId: number | null
}

const props = defineProps<Props>()
const emit = defineEmits(['close'])

const loading = ref(false)
const error = ref<string | null>(null)
const record = ref<any>(null)

const formatDate = (date: string | null) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

const fetchRecord = async () => {
  if (!props.recordId) return

  loading.value = true
  error.value = null

  try {
    const response = await fetch(`/baptism-records/${props.recordId}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })

    if (!response.ok) {
      throw new Error('Failed to fetch baptism record')
    }

    const data = await response.json()
    record.value = data.baptismRecord || data
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'An error occurred'
  } finally {
    loading.value = false
  }
}

const printCertificate = () => {
  if (props.recordId) {
    window.open(`/baptism-records/${props.recordId}/download-pdf`, '_blank')
  }
}

const editRecord = () => {
  if (props.recordId) {
    router.visit(`/baptism-records/${props.recordId}/edit`)
  }
}

const handleClose = () => {
  emit('close')
}

watch(() => props.open, (newVal) => {
  if (newVal && props.recordId) {
    fetchRecord()
  }
}, { immediate: true })
</script>
