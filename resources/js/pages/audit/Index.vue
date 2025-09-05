<template>
  <div class="p-6">
    <div class="mb-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <button
          type="button"
          class="inline-flex items-center rounded-md border border-gray-200 bg-[#ffffff] px-3 py-1.5 text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
          @click="goBack"
          aria-label="Go back"
        >
          <svg
            class="mr-2 h-[1rem] w-[1rem]"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
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
    <div class="rounded-lg border bg-[#ffffff] p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold">Audit Logs System</h2>

      <div v-if="logs && logs.data && logs.data.length > 0" class="space-y-4">
        <div class="font-medium text-green-600">✅ Audit system is working! Found {{ logs.total }} audit logs.</div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Action</th>
                <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Table</th>
                <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Record ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">User</th>
                <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">IP Address</th>
                <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Date & Time</th>
                <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Details</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-[#ffffff]">
              <tr v-for="log in logs.data.slice(0, 10)" :key="log.id" class="hover:bg-gray-50">
                <td class="px-4 py-4 whitespace-nowrap">
                  <span
                    :class="{
                      'rounded-full px-2 py-1 text-xs font-medium': true,
                      'bg-green-100 text-green-800': log.action === 'CREATE',
                      'bg-yellow-100 text-yellow-800': log.action === 'UPDATE',
                      'bg-red-100 text-red-800': log.action === 'DELETE',
                    }"
                  >
                    {{ log.action }}
                  </span>
                </td>
                <td class="px-4 py-4 text-sm font-medium whitespace-nowrap text-gray-900">
                  {{ log.table_name }}
                </td>
                <td class="px-4 py-4 text-sm whitespace-nowrap text-gray-900">
                  <span class="rounded bg-gray-100 px-2 py-1 font-mono">{{ log.record_id }}</span>
                </td>
                <td class="px-4 py-4 text-sm whitespace-nowrap text-gray-900">
                  <div v-if="log.user" class="flex flex-col">
                    <span class="font-medium text-blue-600">{{ log.user.name }}</span>
                    <span class="text-xs text-gray-500">{{ log.user.email }}</span>
                  </div>
                  <span v-else class="text-gray-500 italic">Unknown User</span>
                </td>
                <td class="px-4 py-4 text-sm whitespace-nowrap text-gray-900">
                  <span v-if="log.ip_address" class="rounded bg-gray-100 px-2 py-1 font-mono text-xs">
                    {{ log.ip_address }}
                  </span>
                  <span v-else class="text-gray-400">N/A</span>
                </td>
                <td class="px-4 py-4 text-sm whitespace-nowrap text-gray-900">
                  <div class="flex flex-col">
                    <span class="font-medium">{{ formatDate(log.created_at) }}</span>
                    <span class="text-xs text-gray-500">{{ formatTime(log.created_at) }}</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                  <button @click="viewLogDetails(log)" class="font-medium text-blue-600 hover:text-blue-900">View Details</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Info -->
        <div class="mt-4 text-sm text-gray-600">
          Showing {{ logs.data.length }} of {{ logs.total }} audit logs
          <span v-if="logs.data.length < logs.total" class="text-blue-600"> (showing first 10 for preview) </span>
        </div>
      </div>

      <div v-else class="py-8 text-center">
        <div class="text-gray-500">
          <p class="text-lg font-medium">No audit logs found</p>
          <p class="mt-2 text-sm">Audit logs will appear here when you perform actions in the system.</p>
        </div>
      </div>
    </div>

    <!-- Enhanced Log Details Modal -->
    <div v-if="selectedLog" class="bg-opacity-50 fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600">
      <div class="w-[2.75rem]/12 relative top-10 mx-auto max-h-[90vh] overflow-y-auto rounded-md border bg-[#ffffff] p-5 shadow-lg md:w-3/4 lg:w-2/3">
        <div class="mt-3">
          <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Audit Log Details</h3>
            <button @click="selectedLog = null" class="text-2xl font-bold text-gray-400 hover:text-gray-600">×</button>
          </div>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Basic Information -->
            <div class="space-y-4">
              <div class="rounded-lg bg-gray-50 p-4">
                <h4 class="mb-3 font-semibold text-gray-900">Basic Information</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Action:</span>
                    <span
                      :class="{
                        'rounded-full px-2 py-1 text-xs font-medium': true,
                        'bg-green-100 text-green-800': selectedLog.action === 'CREATE',
                        'bg-yellow-100 text-yellow-800': selectedLog.action === 'UPDATE',
                        'bg-red-100 text-red-800': selectedLog.action === 'DELETE',
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
                    <span class="rounded bg-gray-100 px-2 py-1 font-mono">{{ selectedLog.record_id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Date & Time:</span>
                    <span>{{ formatDateTime(selectedLog.created_at) }}</span>
                  </div>
                </div>
              </div>

              <!-- User Information -->
              <div class="rounded-lg bg-blue-50 p-4">
                <h4 class="mb-3 font-semibold text-gray-900">User Information</h4>
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
              <div class="rounded-lg bg-gray-50 p-4">
                <h4 class="mb-3 font-semibold text-gray-900">Technical Information</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="font-medium text-gray-600">IP Address:</span>
                    <span v-if="selectedLog.ip_address" class="rounded bg-gray-100 px-2 py-1 font-mono text-xs">
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
              <div v-if="selectedLog.old_values && Object.keys(selectedLog.old_values).length > 0" class="rounded-lg bg-red-50 p-4">
                <h4 class="mb-3 font-semibold text-red-700">Previous Values</h4>
                <div class="max-h-64 space-y-2 overflow-y-auto">
                  <div
                    v-for="(value, key) in selectedLog.old_values"
                    :key="key"
                    class="flex items-center justify-between rounded border-l-4 border-red-400 bg-[#ffffff] p-2"
                  >
                    <span class="font-medium text-gray-600">{{ key }}:</span>
                    <span class="rounded bg-red-50 px-2 py-1 text-red-700 shadow-sm">
                      {{ formatValue(value) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- New Values (for CREATE/UPDATE) -->
              <div v-if="selectedLog.new_values && Object.keys(selectedLog.new_values).length > 0" class="rounded-lg bg-green-50 p-4">
                <h4 class="mb-3 font-semibold text-green-700">New Values</h4>
                <div class="max-h-64 space-y-2 overflow-y-auto">
                  <div
                    v-for="(value, key) in selectedLog.new_values"
                    :key="key"
                    class="flex items-center justify-between rounded border-l-4 border-green-400 bg-[#ffffff] p-2"
                  >
                    <span class="font-medium text-gray-600">{{ key }}:</span>
                    <span class="rounded bg-green-50 px-2 py-1 text-green-700 shadow-sm">
                      {{ formatValue(value) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Changes Summary (for UPDATE) -->
              <div v-if="selectedLog.action === 'UPDATE' && selectedLog.old_values && selectedLog.new_values" class="rounded-lg bg-yellow-50 p-4">
                <h4 class="mb-3 font-semibold text-yellow-700">Changes Summary</h4>
                <div class="max-h-64 space-y-2 overflow-y-auto text-sm">
                  <div
                    v-for="(value, key) in selectedLog.new_values"
                    :key="key"
                    v-show="hasValueChanged(selectedLog.old_values[key], value)"
                    class="flex items-center justify-between rounded border-l-4 border-yellow-400 bg-[#ffffff] p-2"
                  >
                    <span class="font-medium text-gray-600">{{ key }}:</span>
                    <div class="flex items-center space-x-2">
                      <!-- Show old value if it exists and is different -->
                      <span
                        v-if="selectedLog.old_values[key] !== undefined && hasValueChanged(selectedLog.old_values[key], value)"
                        class="rounded bg-red-50 px-2 py-1 text-xs text-red-600 line-through shadow-sm"
                      >
                        {{ formatValue(selectedLog.old_values[key]) }}
                      </span>
                      <!-- Show new value -->
                      <span class="rounded bg-green-50 px-2 py-1 font-medium text-green-600 shadow-sm">
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
              class="rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:outline-none"
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
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  logs: Object,
  filters: Object,
  filterOptions: Object,
});

const selectedLog = ref(null);

function formatDateTime(dateTime) {
  const date = new Date(dateTime);
  return date.toLocaleString('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  });
}

function formatDate(dateTime) {
  const date = new Date(dateTime);
  return date.toLocaleDateString('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
}

function formatTime(dateTime) {
  const date = new Date(dateTime);
  return date.toLocaleTimeString('en-GB', {
    hour: '2-digit',
    minute: '2-digit',
  });
}

function formatValue(value) {
  if (value === null || value === undefined || value === '') {
    return 'N/A';
  }

  // Check if the value is a date (common date field names or ISO date format)
  const dateFields = ['created_at', 'updated_at', 'date_of_birth', 'marriage_date', 'baptism_date', 'confirmation_date', 'death_date'];
  const isDateField = (key) => dateFields.some((field) => key.toLowerCase().includes(field));

  // Check if it's an ISO date string (YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
  const isISODate = /^\d{4}-\d{2}-\d{2}/.test(value);

  if (isISODate) {
    try {
      const date = new Date(value);
      if (!isNaN(date.getTime())) {
        return date.toLocaleDateString('en-GB', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
        });
      }
    } catch (e) {
      // If date parsing fails, return original value
    }
  }

  return value;
}

function hasValueChanged(oldValue, newValue) {
  // Handle null/undefined/empty string comparisons
  const oldFormatted = oldValue === null || oldValue === undefined || oldValue === '' ? null : oldValue;
  const newFormatted = newValue === null || newValue === undefined || newValue === '' ? null : newValue;
  return oldFormatted !== newFormatted;
}

function viewLogDetails(log) {
  selectedLog.value = log;
}

function goBack() {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    router.visit(route('dashboard'));
  }
}
</script>
