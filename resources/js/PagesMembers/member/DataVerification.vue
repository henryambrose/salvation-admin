<template>
  <div class="min-h-screen bg-gray-50">
    <Head title="Data Verification & Correction" />
    
    <!-- Page Header -->
    <div class="bg-[#ffffff] border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Data Verification & Correction</h1>
          <p class="text-gray-600 mt-1">Review and update member data in bulk for improved data quality</p>
        </div>
        
        <div class="flex items-center gap-3">
          <button
            @click="refreshData"
            :disabled="loading"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:ring-offset-2 disabled:opacity-50"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-[1rem] w-[1rem] text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="-ml-1 mr-2 h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            {{ loading ? 'Refreshing...' : 'Refresh Data' }}
          </button>
          
          <Link
            :href="route('member.index')"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
          >
            <svg class="-ml-1 mr-2 h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Members
          </Link>
        </div>
      </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="bg-[#ffffff] border-b border-gray-200 px-6 py-3">
      <div class="flex flex-wrap items-center gap-4 text-sm">
        <div class="flex items-center gap-2">
          <span class="font-medium text-gray-700">Quick Actions:</span>
        </div>
        
        <button
          @click="focusOnMissingNames"
          class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full hover:bg-red-200"
        >
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          Focus Missing Names
        </button>
        
        <button
          @click="focusOnMissingRelationships"
          class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full hover:bg-orange-200"
        >
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          Focus Missing Relationships
        </button>
        
        <button
          @click="focusOnMissingDates"
          class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full hover:bg-yellow-200"
        >
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          Focus Missing Dates
        </button>
        
        <button
          @click="exportAllData"
          class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full hover:bg-blue-200"
        >
          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Export All Data
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 py-6">
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="text-center">
          <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-gray-600">Loading member data...</p>
        </div>
      </div>

      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <h3 class="text-lg font-medium text-red-800 mb-2">Error Loading Data</h3>
        <p class="text-red-700 mb-4">{{ error }}</p>
        <button
          @click="loadData"
          class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
        >
          Try Again
        </button>
      </div>

             <div v-else>
         <!-- Permission check for data verification access -->
         <div v-if="!can('read-data-verification') && !can('update-data-verification')" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
           <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
           </svg>
           <h3 class="text-lg font-medium text-red-800 mb-2">Access Denied</h3>
           <p class="text-red-700 mb-4">You don't have permission to access Data Verification & Correction.</p>
           <p class="text-sm text-red-600">Contact your administrator to request read or update permissions.</p>
         </div>
         
         <!-- Data verification table for users with any data verification permission -->
         <BulkDataVerificationTable
           v-else
           :columns="tableColumns"
           :data="members"
           :communities="communities"
           :statuses="statuses"
           :relationships="relationships"
           :api-endpoint="'/member/bulk-update'"
         />
       </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import BulkDataVerificationTable from '@/components/BulkDataVerificationTable.vue'
import { permissionHelpers } from '@/composables/permissionHelpers'

const { can } = permissionHelpers()

// Props
const props = defineProps<{
  members?: any[]
  communities?: Array<{ id: number; name: string }>
  statuses?: Array<{ id: number; name: string }>
  relationships?: Array<{ id: number; name: string }>
}>()

// State
const loading = ref(false)
const error = ref('')
const members = ref<any[]>(props.members || [])
const communities = ref<any[]>(props.communities || [])

// Table columns configuration
const tableColumns = [
  {
    key: 'first_name',
    label: 'First Name',
    editable: true,
    type: 'text' as const,
    placeholder: 'Enter first name'
  },
  {
    key: 'middle_name',
    label: 'Middle Name',
    editable: true,
    type: 'text' as const,
    placeholder: 'Enter middle name'
  },
  {
    key: 'last_name',
    label: 'Last Name',
    editable: true,
    type: 'text' as const,
    placeholder: 'Enter last name'
  },
  {
    key: 'old_sal_id',
    label: 'Old SAL ID',
    editable: true,
    type: 'text' as const,
    placeholder: 'Enter old SAL ID'
  },
  {
    key: 'date_of_birth',
    label: 'Date of Birth',
    editable: true,
    type: 'date' as const,
    placeholder: 'Select date',
    formatter: (value: string) => {
      if (!value) return '<span class="text-red-500">Missing</span>'
      const date = new Date(value)
      const day = date.getDate().toString().padStart(2, '0')
      const month = (date.getMonth() + 1).toString().padStart(2, '0')
      const year = date.getFullYear()
      return `${day}-${month}-${year}`
    }
  },
  {
    key: 'community_id',
    label: 'Community',
    editable: true,
    type: 'select' as const,
    options: props.communities?.map(c => ({ value: c.id, label: c.name })) || [],
    placeholder: 'Select community'
  },
  {
    key: 'status_id',
    label: 'Status',
    editable: true,
    type: 'select' as const,
    options: props.statuses?.map(s => ({ value: s.id, label: s.name })) || [],
    placeholder: 'Select status'
  },
  {
    key: 'contact_no_1',
    label: 'Contact No 1',
    editable: true,
    type: 'text' as const,
    placeholder: 'Enter contact number'
  },
  {
    key: 'contact_no_2',
    label: 'Contact No 2',
    editable: true,
    type: 'text' as const,
    placeholder: 'Enter contact number'
  },
  {
    key: 'relationship_id',
    label: 'Relationship',
    editable: true,
    type: 'select' as const,
    options: props.relationships?.map(r => ({ value: r.id, label: r.name })) || [],
    placeholder: 'Select relationship'
  }
]

// Methods
const loadData = async () => {
  // Data is already loaded via props, just refresh if needed
  loading.value = false
  error.value = ''
}

const refreshData = () => {
  loadData()
}

const focusOnMissingNames = () => {
  // This would filter the table to show only records with missing names
  // TODO: Implement filtering logic
}

const focusOnMissingRelationships = () => {
  // This would filter the table to show only records with missing relationships
  // TODO: Implement filtering logic
}

const focusOnMissingDates = () => {
  // This would filter the table to show only records with missing dates
  // TODO: Implement filtering logic
}

const exportAllData = () => {
  // Export all member data to CSV
  const csv = convertToCSV(members.value)
  downloadCSV(csv, `all_member_data_${new Date().toISOString().split('T')[0]}.csv`)
}

const convertToCSV = (data: any[]) => {
  if (data.length === 0) return ''
  
  const headers = tableColumns.map(col => col.label)
  const rows = data.map(item => 
    tableColumns.map(col => {
      const value = item[col.key]
      return typeof value === 'string' && value.includes(',') ? `"${value}"` : value
    }).join(',')
  )
  
  return [headers.join(','), ...rows].join('\n')
}

const downloadCSV = (csv: string, filename: string) => {
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  link.click()
  window.URL.revokeObjectURL(url)
}

// Data is loaded via props, no need to load on mount
onMounted(() => {
  // Initialize with props data
  if (props.members) {
    members.value = props.members
  }
  if (props.communities) {
    communities.value = props.communities
  }
})
</script>
