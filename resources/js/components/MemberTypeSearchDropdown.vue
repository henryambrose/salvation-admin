<template>
  <div class="relative">
    <!-- Member Type Selection -->
    <div class="mb-3">
      <Label>Member Type</Label>
      <div class="flex space-x-4 mt-1">
        <label class="flex items-center">
          <input
            type="radio"
            v-model="memberType"
            value="Member"
            class="mr-2"
          />
          <span class="text-sm">Member</span>
        </label>
        <label class="flex items-center">
          <input
            type="radio"
            v-model="memberType"
            value="External"
            class="mr-2"
          />
          <span class="text-sm">External Member</span>
        </label>
      </div>
    </div>

    <!-- Search Input -->
    <div class="relative">
      <Input
        v-model="searchQuery"
        :placeholder="placeholder"
        @input="handleSearch"
        @focus="showDropdown = true"
        class="w-full"
      />
      
      <!-- Dropdown -->
      <div
        v-if="showDropdown && searchResults.length > 0"
        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
      >
        <div
          v-for="result in searchResults"
          :key="`${result.type}-${result.id}`"
          @click="selectResult(result)"
          class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
        >
          <div class="flex items-center justify-between">
            <div>
              <div class="font-medium">{{ result.name }}</div>
              <div class="text-sm text-gray-500">
                {{ result.family_no }} • {{ result.type === 'external' ? 'External' : 'Member' }}
              </div>
            </div>
            <div class="text-xs px-2 py-1 rounded-full"
                 :class="result.type === 'external' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'">
              {{ result.type === 'external' ? 'External' : 'Member' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Selected Member Display -->
    <div v-if="selectedMember" class="mt-2 p-2 bg-gray-50 rounded border">
      <div class="flex items-center justify-between">
        <div>
          <div class="font-medium">{{ selectedMember.name }}</div>
          <div class="text-sm text-gray-500">{{ selectedMember.family_no }}</div>
        </div>
        <div class="flex items-center space-x-2">
          <span class="text-xs px-2 py-1 rounded-full"
                :class="selectedMember.type === 'external' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'">
            {{ selectedMember.type === 'external' ? 'External' : 'Member' }}
          </span>
          <button
            @click="clearSelection"
            class="text-red-600 hover:text-red-800 text-sm"
          >
            ×
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps({
  modelValue: {
    type: [Number, String],
    default: null
  },
  placeholder: {
    type: String,
    default: 'Search for member...'
  },
  existingData: {
    type: Object,
    default: null
  },
  sourceType: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'update:sourceType'])

const memberType = ref('Member')
const searchQuery = ref('')
const searchResults = ref([])
const showDropdown = ref(false)
const selectedMember = ref(null)

// Debug logging
onMounted(() => {
  console.log('MemberTypeSearchDropdown mounted with modelValue:', props.modelValue)
  console.log('Existing data:', props.existingData)
  
  if (props.existingData) {
    console.log('Using existing data from props:', props.existingData)
    selectedMember.value = {
      id: props.existingData.id,
      name: props.existingData.name,
      family_no: props.existingData.family_no || '',
      type: props.existingData.type
    }
    memberType.value = props.existingData.type
    searchQuery.value = props.existingData.name
    console.log('Initialized with existing data:', selectedMember.value)
  } else if (props.modelValue) {
    console.log('Fetching member details for ID:', props.modelValue)
    fetchMemberDetails(props.modelValue)
  }
})

// Debounce search
let searchTimeout = null

const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    if (searchQuery.value.length >= 2) {
      performSearch()
    } else {
      searchResults.value = []
    }
  }, 300)
}

// Re-run search when memberType toggles
watch(memberType, () => {
  searchResults.value = [];
  if (searchQuery.value.length >= 2) performSearch();
});

// Ensure we include family_no safely
const performSearch = async () => {
  try {
    const endpoint = memberType.value === 'External'
      ? '/api/external-members/search-all'
      : '/api/members/search-spouse';

    const response = await fetch(`${endpoint}?query=${encodeURIComponent(searchQuery.value)}`);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

    const data = await response.json();
    searchResults.value = data.map((item) => ({
      id: item.id,
      name: (item.first_name || '') + ' ' + (item.last_name || ''),
      family_no: item.family_no || '',
      type: memberType.value,
    }));
  } catch {
    searchResults.value = [];
  }
};

const selectResult = (result) => {
  console.log('Selecting result:', result)
  selectedMember.value = result
  emit('update:modelValue', result.id)
  emit('update:sourceType', result.type)
  searchQuery.value = result.name
  showDropdown.value = false
}

const clearSelection = () => {
  console.log('Clearing selection')
  selectedMember.value = null
  emit('update:modelValue', null)
  searchQuery.value = ''
}

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  console.log('modelValue changed to:', newValue)
  if (!newValue) {
    clearSelection()
  }
})

const fetchMemberDetails = async (memberId) => {
  console.log('fetchMemberDetails called with ID:', memberId)
  try {
    // Try internal members first
    console.log('Trying internal members API...')
    let response = await fetch(`/api/members/${memberId}`)
    console.log('Internal members response status:', response.status)
    
    if (response.ok) {
      const data = await response.json()
      console.log('Internal member data:', data)
      selectedMember.value = {
        id: data.id,
        name: data.first_name + ' ' + (data.last_name || ''),
        family_no: data.family_no,
        type: 'Member'
      }
      memberType.value = 'Member'
      searchQuery.value = selectedMember.value.name
      console.log('Set internal member:', selectedMember.value)
      return
    }
    
    // Try external members
    console.log('Trying external members API...')
    response = await fetch(`/api/external-members/${memberId}`)
    console.log('External members response status:', response.status)
    
    if (response.ok) {
      const data = await response.json()
      console.log('External member data:', data)
      selectedMember.value = {
        id: data.id,
        name: data.first_name + ' ' + (data.last_name || ''),
        family_no: data.family_no,
        type: 'external'
      }
      memberType.value = 'external'
      searchQuery.value = selectedMember.value.name
      console.log('Set external member:', selectedMember.value)
    } else {
      console.error('Failed to fetch member details from both APIs')
    }
  } catch (error) {
    console.error('Error fetching member details:', error)
  }
}

// Add this new function to handle initialization with existing data
const initializeWithExistingData = (memberId, memberData) => {
  if (memberId && memberData) {
    console.log('Initializing with existing data:', memberData)
    selectedMember.value = {
      id: memberData.id,
      name: memberData.name,
      family_no: memberData.family_no || '',
      type: memberData.type
    }
    memberType.value = memberData.type
    searchQuery.value = memberData.name
    console.log('Initialized member:', selectedMember.value)
  }
}
</script>

