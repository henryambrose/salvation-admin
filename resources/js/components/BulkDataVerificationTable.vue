<template>
  <div class="w-full p-6 bg-[#ffffff] rounded-lg shadow-sm">
         <!-- Header with Summary Stats -->
     <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
       <div class="flex items-center justify-between">
         <div>
           <h2 class="text-xl font-semibold text-blue-900">Data Verification & Correction</h2>
           <p class="text-blue-700 text-sm mt-1">
             <span v-if="canEdit">Review and update member data in bulk</span>
             <span v-else class="text-orange-600">Read-only view - You can only view data</span>
           </p>
         </div>
        <div class="flex items-center gap-4 text-sm">
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ totalRecords }}</div>
            <div class="text-blue-600">Total Records</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ selectedRecords.length }}</div>
            <div class="text-orange-600">Selected</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ modifiedRecords.length }}</div>
            <div class="text-green-600">Modified</div>
          </div>
        </div>
      </div>
    </div>

         <!-- Bulk Actions Bar -->
     <div v-if="canEdit" class="mb-4 flex flex-wrap items-center justify-between gap-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
       <div class="flex items-center gap-3">
         <label class="flex items-center gap-2">
           <input
             type="checkbox"
             :checked="isAllSelected"
             @change="toggleSelectAll"
             class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
           />
           <span class="text-sm font-medium text-gray-700">Select All</span>
         </label>
         
         <span v-if="selectedRecords.length > 0" class="text-sm text-gray-600">
           {{ selectedRecords.length }} record(s) selected
         </span>
       </div>

      <div class="flex items-center gap-2">
        <button
          v-if="selectedRecords.length > 0"
          @click="saveBulkChanges"
          :disabled="saving"
          class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50"
        >
          <svg v-if="saving" class="animate-spin -ml-1 mr-2 h-[1rem] w-[1rem] text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else class="-ml-1 mr-2 h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          {{ saving ? 'Saving...' : `Save ${selectedRecords.length} Changes` }}
        </button>

        <button
          v-if="modifiedRecords.length > 0"
          @click="discardChanges"
          class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
        >
          <svg class="-ml-1 mr-2 h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
          Discard Changes
        </button>

        <button
          @click="exportSelected"
          :disabled="selectedRecords.length === 0"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:ring-offset-2 disabled:opacity-50"
        >
          <svg class="-ml-1 mr-2 h-[1rem] w-[1rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Export Selected
        </button>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
      <div class="flex-1">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search members by name, old SAL ID, or contact number..."
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
        />
      </div>
      
      <div class="flex gap-2">
        <select v-model="filterStatus" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500">
          <option value="">All Status</option>
          <option v-for="status in statuses" :key="status.id" :value="status.id">
            {{ status.name }}
          </option>
        </select>
        
        <select v-model="filterCommunity" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500">
          <option value="">All Communities</option>
          <option v-for="community in communities" :key="community.id" :value="community.id">
            {{ community.name }}
          </option>
        </select>
        
        <select v-model="perPage" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500">
          <option value="25">25 per page</option>
          <option value="50">50 per page</option>
          <option value="100">100 per page</option>
          <option value="200">200 per page</option>
        </select>
      </div>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
                 <thead class="bg-gray-50">
           <tr>
             <th v-if="canEdit" class="px-3 py-3 text-left">
               <input
                 type="checkbox"
                 :checked="isAllSelected"
                 @change="toggleSelectAll"
                 class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
               />
             </th>
            <th
              v-for="column in columns"
              :key="column.key"
              class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              @click="sortBy(column.key)"
            >
              <div class="flex items-center gap-1">
                <span>{{ column.label }}</span>
                <svg v-if="sortColumn === column.key" class="h-[1rem] w-[1rem] text-gray-400">
                  <path v-if="sortDirection === 'asc'" d="M7 14l5-5 5 5" />
                  <path v-else d="M7 10l5 5 5-5" />
                </svg>
              </div>
            </th>
            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-[#ffffff] divide-y divide-gray-200">
          <tr
            v-for="(item, index) in paginatedData"
            :key="item.id || index"
            :class="[
              'hover:bg-gray-50',
              selectedRecords.includes(item.id) ? 'bg-blue-50' : '',
              modifiedRecords.includes(item.id) ? 'bg-yellow-50' : ''
            ]"
          >
                         <!-- Selection Checkbox -->
             <td v-if="canEdit" class="px-3 py-4">
               <input
                 type="checkbox"
                 :checked="selectedRecords.includes(item.id)"
                 @change="toggleSelection(item.id)"
                 class="rounded border-gray-300 text-blue-600 focus:ring-[#3b82f6]"
               />
             </td>

            <!-- Data Columns -->
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-3 py-4 text-sm"
            >
              <!-- Editable Fields -->
              <div v-if="column.editable && selectedRecords.includes(item.id)" class="relative">
                <input
                  v-if="column.type === 'text' || column.type === 'number' || column.type === 'date'"
                  v-model="editingData[item.id][column.key]"
                  :type="column.type"
                  :placeholder="column.placeholder || ''"
                  class="w-full px-2 py-1 text-sm border border-blue-300 rounded focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
                  @input="markAsModified(item.id)"
                />
                
                <select
                  v-else-if="column.type === 'select' && column.options"
                  v-model="editingData[item.id][column.key]"
                  class="w-full px-2 py-1 text-sm border border-blue-300 rounded focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
                  @change="markAsModified(item.id)"
                >
                  <option value="">{{ column.placeholder || 'Select...' }}</option>
                  <option v-for="option in column.options" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>
                
                <textarea
                  v-else-if="column.type === 'textarea'"
                  v-model="editingData[item.id][column.key]"
                  :placeholder="column.placeholder || ''"
                  rows="2"
                  class="w-full px-2 py-1 text-sm border border-blue-300 rounded focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500"
                  @input="markAsModified(item.id)"
                />
              </div>

              <!-- Read-only Display -->
              <div v-else class="text-gray-900">
                <span v-if="column.formatter" v-html="column.formatter(item[column.key], item)"></span>
                <span v-else-if="column.type === 'date' && item[column.key]">
                  {{ formatDate(item[column.key]) }}
                </span>
                <span v-else>{{ item[column.key] || '—' }}</span>
              </div>
            </td>

                         <!-- Actions -->
             <td v-if="canEdit" class="px-3 py-4 text-sm">
               <div class="flex items-center gap-2">
                 <button
                   v-if="!selectedRecords.includes(item.id)"
                   @click="selectForEditing(item)"
                   class="text-blue-600 hover:text-blue-800 text-xs font-medium"
                 >
                   Edit
                 </button>
                 <button
                   v-else
                   @click="deselectFromEditing(item)"
                   class="text-gray-600 hover:text-gray-800 text-xs font-medium"
                 >
                   Cancel
                 </button>
                 
                 <button
                   v-if="modifiedRecords.includes(item.id)"
                   @click="saveSingleRecord(item)"
                   :disabled="saving"
                   class="text-green-600 hover:text-green-800 text-xs font-medium disabled:opacity-50"
                 >
                   Save
                 </button>
               </div>
             </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
      <div class="text-sm text-gray-700">
        Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{ filteredData.length }} entries
      </div>
      
      <div class="flex items-center gap-2">
        <button
          @click="currentPage = 1"
          :disabled="currentPage === 1"
          class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
        >
          First
        </button>
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
        >
          Previous
        </button>
        
        <span class="px-3 py-1 text-sm text-gray-700">
          Page {{ currentPage }} of {{ totalPages }}
        </span>
        
        <button
          @click="currentPage++"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
        >
          Next
        </button>
        <button
          @click="currentPage = totalPages"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
        >
          Last
        </button>
      </div>
    </div>

    <!-- Data Quality Summary -->
    <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
      <h3 class="text-lg font-medium text-gray-900 mb-3">Data Quality Summary</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div class="text-center">
          <div class="text-2xl font-bold text-red-600">{{ dataQualityStats.missingNames }}</div>
          <div class="text-red-600">Missing Names</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-orange-600">{{ dataQualityStats.missingRelationships }}</div>
          <div class="text-orange-600">Missing Relationships</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-yellow-600">{{ dataQualityStats.missingDates }}</div>
          <div class="text-yellow-600">Missing Dates</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-green-600">{{ dataQualityStats.completeRecords }}</div>
          <div class="text-green-600">Complete Records</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import { permissionHelpers } from '@/composables/permissionHelpers'

const { can } = permissionHelpers()

interface Column {
  key: string
  label: string
  editable?: boolean
  type?: 'text' | 'number' | 'select' | 'textarea' | 'date'
  options?: Array<{ value: any; label: string }>
  placeholder?: string
  formatter?: (value: any, row: any) => string
}

interface Props {
  columns: Column[]
  data: any[]
  communities?: Array<{ id: number; name: string }>
  statuses?: Array<{ id: number; name: string }>
  relationships?: Array<{ id: number; name: string }>
  apiEndpoint?: string
}

const props = withDefaults(defineProps<Props>(), {
  communities: () => [],
  statuses: () => [],
  relationships: () => [],
  apiEndpoint: '/api/members/bulk-update'
})

// Permission checks
const canEdit = computed(() => {
  return can('update-data-verification')
})

const canRead = computed(() => {
  return can('read-data-verification')
})

// State
const searchQuery = ref('')
const filterStatus = ref('')
const filterCommunity = ref('')
const perPage = ref(25)
const currentPage = ref(1)
const sortColumn = ref('')
const sortDirection = ref<'asc' | 'desc'>('asc')
const saving = ref(false)

// Selection and editing
const selectedRecords = ref<number[]>([])
const modifiedRecords = ref<number[]>([])
const editingData = ref<Record<number, Record<string, any>>>({})

// Computed
const filteredData = computed(() => {
  let filtered = [...props.data]
  
  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item => 
      (item.first_name && item.first_name.toLowerCase().includes(query)) ||
      (item.last_name && item.last_name.toLowerCase().includes(query)) ||
      (item.old_sal_id && item.old_sal_id.toString().includes(query)) ||
      (item.contact_no_1 && item.contact_no_1.toString().includes(query))
    )
  }
  
  // Status filter
  if (filterStatus.value) {
    filtered = filtered.filter(item => item.status_id === parseInt(filterStatus.value))
  }
  
  // Community filter
  if (filterCommunity.value) {
    filtered = filtered.filter(item => item.community_id === parseInt(filterCommunity.value))
  }
  
  return filtered
})

const sortedData = computed(() => {
  if (!sortColumn.value) return filteredData.value
  
  return [...filteredData.value].sort((a, b) => {
    const aVal = a[sortColumn.value] || ''
    const bVal = b[sortColumn.value] || ''
    
    if (sortDirection.value === 'asc') {
      return aVal > bVal ? 1 : -1
    } else {
      return aVal < bVal ? 1 : -1
    }
  })
})

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  const end = start + perPage.value
  return sortedData.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / perPage.value))

const paginationInfo = computed(() => {
  const start = (currentPage.value - 1) * perPage.value + 1
  const end = Math.min(currentPage.value * perPage.value, filteredData.value.length)
  return { from: start, to: end }
})

const isAllSelected = computed(() => {
  return paginatedData.value.length > 0 && selectedRecords.value.length === paginatedData.value.length
})

const totalRecords = computed(() => props.data.length)

const dataQualityStats = computed(() => {
  const stats = {
    missingNames: 0,
    missingRelationships: 0,
    missingDates: 0,
    completeRecords: 0
  }
  
  props.data.forEach(item => {
    if (!item.first_name || !item.last_name) {
      stats.missingNames++
    }
    if (!item.relationship_id) {
      stats.missingRelationships++
    }
    if (!item.date_of_birth) {
      stats.missingDates++
    }
    if (item.first_name && item.last_name && item.relationship_id && item.date_of_birth) {
      stats.completeRecords++
    }
  })
  
  return stats
})

// Methods
const sortBy = (column: string) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column
    sortDirection.value = 'asc'
  }
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedRecords.value = []
    editingData.value = {}
  } else {
    selectedRecords.value = paginatedData.value.map(item => item.id)
    paginatedData.value.forEach(item => {
      if (!editingData.value[item.id]) {
        editingData.value[item.id] = { ...item }
      }
    })
  }
}

const toggleSelection = (id: number) => {
  const index = selectedRecords.value.indexOf(id)
  if (index > -1) {
    selectedRecords.value.splice(index, 1)
    delete editingData.value[id]
  } else {
    selectedRecords.value.push(id)
    if (!editingData.value[id]) {
      const item = props.data.find(item => item.id === id)
      editingData.value[id] = { ...item }
    }
  }
}

const selectForEditing = (item: any) => {
  selectedRecords.value.push(item.id)
  editingData.value[item.id] = { ...item }
}

const deselectFromEditing = (item: any) => {
  const index = selectedRecords.value.indexOf(item.id)
  if (index > -1) {
    selectedRecords.value.splice(index, 1)
    delete editingData.value[item.id]
  }
}

const markAsModified = (id: number) => {
  if (!modifiedRecords.value.includes(id)) {
    modifiedRecords.value.push(id)
  }
}

const saveSingleRecord = async (item: any) => {
  try {
    saving.value = true
    const response = await axios.put(`/member/${item.id}`, editingData.value[item.id])
    
    // Update the original data
    Object.assign(item, editingData.value[item.id])
    
    // Remove from modified records
    const index = modifiedRecords.value.indexOf(item.id)
    if (index > -1) {
      modifiedRecords.value.splice(index, 1)
    }
    
    console.log('Record saved successfully:', response.data)
  } catch (error) {
    console.error('Error saving record:', error)
    alert('Failed to save record. Please try again.')
  } finally {
    saving.value = false
  }
}

const saveBulkChanges = async () => {
  try {
    saving.value = true
    
    const changes = selectedRecords.value.map(id => ({
      id,
      data: editingData.value[id]
    }))
    
    const response = await axios.post(props.apiEndpoint, { changes })
    
    // Update original data
    changes.forEach(({ id, data }) => {
      const item = props.data.find(item => item.id === id)
      if (item) {
        Object.assign(item, data)
      }
    })
    
    // Clear selections
    selectedRecords.value = []
    modifiedRecords.value = []
    editingData.value = {}
    
    console.log('Bulk changes saved successfully:', response.data)
    alert(`${changes.length} records updated successfully!`)
    
  } catch (error) {
    console.error('Error saving bulk changes:', error)
    alert('Failed to save changes. Please try again.')
  } finally {
    saving.value = false
  }
}

const discardChanges = () => {
  selectedRecords.value = []
  modifiedRecords.value = []
  editingData.value = {}
}

const exportSelected = () => {
  const dataToExport = selectedRecords.value.map(id => {
    const item = props.data.find(item => item.id === id)
    return editingData.value[id] || item
  })
  
  const csv = convertToCSV(dataToExport)
  downloadCSV(csv, `member_data_export_${new Date().toISOString().split('T')[0]}.csv`)
}

const convertToCSV = (data: any[]) => {
  if (data.length === 0) return ''
  
  const headers = props.columns.map(col => col.label)
  const rows = data.map(item => 
    props.columns.map(col => {
      const value = item[col.key]
      return typeof value === 'string' && value.includes(',') ? `"${value}"` : value
    }).join(',')
  )
  
  return [headers.join(','), ...rows].join('\n')
}

const formatDate = (dateString: string) => {
  if (!dateString) return '—'
  try {
    const date = new Date(dateString)
    const day = date.getDate().toString().padStart(2, '0')
    const month = (date.getMonth() + 1).toString().padStart(2, '0')
    const year = date.getFullYear()
    return `${day}-${month}-${year}`
  } catch (error) {
    return dateString
  }
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

// Watch for page changes
watch(currentPage, () => {
  // Reset selections when page changes
  selectedRecords.value = []
  editingData.value = {}
})

// Initialize
onMounted(() => {
  // Set default sort
  if (props.columns.length > 0) {
    sortColumn.value = props.columns[0].key
  }
})
</script>

<style scoped>
/* Custom scrollbar for better UX */
.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
