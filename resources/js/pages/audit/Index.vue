<template>
  <div class="p-6">
    <div class="mb-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <button
          type="button"
          class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          @click="goBack"
          aria-label="Go back"
        >
          <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
            <line x1="9" y1="12" x2="21" y2="12"></line>
          </svg>
          Back
        </button>
        <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
      </div>
      <p class="text-gray-600">Track all system changes and user activities</p>
    </div>

    <!-- Enhanced Audit Logs Display -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
      <h2 class="text-lg font-semibold mb-4">Audit Logs System</h2>
      
      <div v-if="logs && logs.data && logs.data.length > 0" class="space-y-4">
        <div class="text-green-600 font-medium">
          ✅ Audit system is working! Found {{ logs.total }} audit logs.
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="log in logs.data.slice(0, 10)" :key="log.id" class="hover:bg-gray-50">
                <td class="px-4 py-4 whitespace-nowrap">
                  <span
                    :class="{
                      'px-2 py-1 text-xs font-medium rounded-full': true,
                      'bg-green-100 text-green-800': log.action === 'CREATE',
                      'bg-yellow-100 text-yellow-800': log.action === 'UPDATE',
                      'bg-red-100 text-red-800': log.action === 'DELETE'
                    }"
                  >
                    {{ log.action }}
                  </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ log.table_name }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ log.record_id }}</span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div v-if="log.user" class="flex flex-col">
                    <span class="font-medium text-blue-600">{{ log.user.name }}</span>
                    <span class="text-xs text-gray-500">{{ log.user.email }}</span>
                  </div>
                  <span v-else class="text-gray-500 italic">Unknown User</span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span v-if="log.ip_address" class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                    {{ log.ip_address }}
                  </span>
                  <span v-else class="text-gray-400">N/A</span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div class="flex flex-col">
                    <span class="font-medium">{{ formatDate(log.created_at) }}</span>
                    <span class="text-xs text-gray-500">{{ formatTime(log.created_at) }}</span>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    @click="viewLogDetails(log)"
                    class="text-blue-600 hover:text-blue-900 font-medium"
                  >
                    View Details
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Info -->
        <div class="mt-4 text-sm text-gray-600">
          Showing {{ logs.data.length }} of {{ logs.total }} audit logs
          <span v-if="logs.data.length < logs.total" class="text-blue-600">
            (showing first 10 for preview)
          </span>
        </div>
      </div>
      
      <div v-else class="text-center py-8">
        <div class="text-gray-500">
          <p class="text-lg font-medium">No audit logs found</p>
          <p class="text-sm mt-2">Audit logs will appear here when you perform actions in the system.</p>
        </div>
      </div>
    </div>

    <!-- Enhanced Log Details Modal -->
    <div v-if="selectedLog" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Audit Log Details</h3>
            <button
              @click="selectedLog = null"
              class="text-gray-400 hover:text-gray-600 text-2xl font-bold"
            >
              ×
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-4">
              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3">Basic Information</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Action:</span>
                    <span
                      :class="{
                        'px-2 py-1 text-xs font-medium rounded-full': true,
                        'bg-green-100 text-green-800': selectedLog.action === 'CREATE',
                        'bg-yellow-100 text-yellow-800': selectedLog.action === 'UPDATE',
                        'bg-red-100 text-red-800': selectedLog.action === 'DELETE'
                      }"
                    >
                      {{ selectedLog.action }}
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Table:</span>
                    <span class="font-mono">{{ selectedLog.table_name }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Record ID:</span>
                    <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ selectedLog.record_id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Date & Time:</span>
                    <span>{{ formatDateTime(selectedLog.created_at) }}</span>
                  </div>
                </div>
              </div>

              <!-- User Information -->
              <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3">User Information</h4>
                <div v-if="selectedLog.user" class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Name:</span>
                    <span class="font-medium text-blue-600">{{ selectedLog.user.name }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Email:</span>
                    <span class="font-mono">{{ selectedLog.user.email }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">User ID:</span>
                    <span class="font-mono">{{ selectedLog.user.id }}</span>
                  </div>
                </div>
                <div v-else class="text-gray-500 italic">No user information available</div>
              </div>

              <!-- Technical Information -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3">Technical Information</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">IP Address:</span>
                    <span v-if="selectedLog.ip_address" class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                      {{ selectedLog.ip_address }}
                    </span>
                    <span v-else class="text-gray-400">N/A</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Log ID:</span>
                    <span class="font-mono">{{ selectedLog.id }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Data Changes -->
            <div class="space-y-4">
              <!-- Previous Values (for UPDATE/DELETE) -->
              <div v-if="selectedLog.old_values && Object.keys(selectedLog.old_values).length > 0" class="bg-red-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 text-red-700">
                  Previous Values
                </h4>
                <div class="max-h-64 overflow-y-auto space-y-2">
                  <div v-for="(value, key) in selectedLog.old_values" :key="key" 
                       class="flex justify-between items-center p-2 bg-white rounded border-l-4 border-red-400">
                    <span class="font-medium text-gray-600">{{ key }}:</span>
                    <span class="text-red-700 bg-red-50 px-2 py-1 rounded shadow-sm">
                      {{ formatValue(value) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- New Values (for CREATE/UPDATE) -->
              <div v-if="selectedLog.new_values && Object.keys(selectedLog.new_values).length > 0" class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 text-green-700">
                  New Values
                </h4>
                <div class="max-h-64 overflow-y-auto space-y-2">
                  <div v-for="(value, key) in selectedLog.new_values" :key="key" 
                       class="flex justify-between items-center p-2 bg-white rounded border-l-4 border-green-400">
                    <span class="font-medium text-gray-600">{{ key }}:</span>
                    <span class="text-green-700 bg-green-50 px-2 py-1 rounded shadow-sm">
                      {{ formatValue(value) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Changes Summary (for UPDATE) -->
              <div v-if="selectedLog.action === 'UPDATE' && selectedLog.old_values && selectedLog.new_values" class="bg-yellow-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3 text-yellow-700">
                  Changes Summary
                </h4>
                <div class="space-y-2 text-sm max-h-64 overflow-y-auto">
                  <div v-for="(value, key) in selectedLog.new_values" :key="key" 
                       v-show="hasValueChanged(selectedLog.old_values[key], value)"
                       class="flex justify-between items-center p-2 bg-white rounded border-l-4 border-yellow-400">
                    <span class="font-medium text-gray-600">{{ key }}:</span>
                    <div class="flex items-center space-x-2">
                      <!-- Show old value if it exists and is different -->
                      <span v-if="selectedLog.old_values[key] !== undefined && hasValueChanged(selectedLog.old_values[key], value)" 
                            class="text-red-600 line-through text-xs bg-red-50 px-2 py-1 rounded shadow-sm">
                        {{ formatValue(selectedLog.old_values[key]) }}
                      </span>
                      <!-- Show new value -->
                      <span class="text-green-600 font-medium bg-green-50 px-2 py-1 rounded shadow-sm">
                        {{ formatValue(value) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button
              @click="selectedLog = null"
              class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  logs: Object,
  filters: Object,
  filterOptions: Object
})

const selectedLog = ref(null)

function formatDateTime(dateTime) {
  const date = new Date(dateTime)
  return date.toLocaleString('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

function formatDate(dateTime) {
  const date = new Date(dateTime)
  return date.toLocaleDateString('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

function formatTime(dateTime) {
  const date = new Date(dateTime)
  return date.toLocaleTimeString('en-GB', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatValue(value) {
  if (value === null || value === undefined || value === '') {
    return 'N/A'
  }
  
  // Check if the value is a date (common date field names or ISO date format)
  const dateFields = ['created_at', 'updated_at', 'date_of_birth', 'marriage_date', 'baptism_date', 'confirmation_date', 'death_date']
  const isDateField = (key) => dateFields.some(field => key.toLowerCase().includes(field))
  
  // Check if it's an ISO date string (YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
  const isISODate = /^\d{4}-\d{2}-\d{2}/.test(value)
  
  if (isISODate) {
    try {
      const date = new Date(value)
      if (!isNaN(date.getTime())) {
        return date.toLocaleDateString('en-GB', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric'
        })
      }
    } catch (e) {
      // If date parsing fails, return original value
    }
  }
  
  return value
}

function hasValueChanged(oldValue, newValue) {
  // Handle null/undefined/empty string comparisons
  const oldFormatted = oldValue === null || oldValue === undefined || oldValue === '' ? null : oldValue
  const newFormatted = newValue === null || newValue === undefined || newValue === '' ? null : newValue
  return oldFormatted !== newFormatted
}

function viewLogDetails(log) {
  selectedLog.value = log
}

function goBack() {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    router.visit(route('dashboard'))
  }
}
</script> 