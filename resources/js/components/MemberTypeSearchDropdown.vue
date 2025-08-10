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
  if (props.existingData) {
    selectedMember.value = {
      id: props.existingData.id,
      name: props.existingData.name,
      type: props.existingData.type
    }
    memberType.value = props.existingData.type
    searchQuery.value = props.existingData.name
  } else if (props.modelValue) {
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
  selectedMember.value = result
  emit('update:modelValue', result.id)
  emit('memberSelected', result)
  searchQuery.value = result.name
  showDropdown.value = false
}

const clearSelection = () => {
  selectedMember.value = null
  emit('update:modelValue', null)
  emit('memberSelected', null)
  searchQuery.value = ''
}

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  if (!newValue) {
    clearSelection()
  } else if (newValue && !selectedMember.value) {
    fetchMemberDetails(newValue)
  }
})

const fetchMemberDetails = async (memberId) => {
  try {
    // Try internal members first
    let response = await fetch(`/api/members/${memberId}`)
    
    if (response.ok) {
      const data = await response.json()
      selectedMember.value = {
        id: data.id,
        name: data.name,
        type: 'Member'
      }
      memberType.value = 'Member'
      searchQuery.value = selectedMember.value.name
      return
    }
    
    // Try external members
    response = await fetch(`/api/external-members/${memberId}`)
    
    if (response.ok) {
      const data = await response.json()
      selectedMember.value = {
        id: data.id,
        name: data.name,
        type: 'external'
      }
      memberType.value = 'external'
      searchQuery.value = selectedMember.value.name
    } else {
      console.error('Failed to fetch member details from both APIs')
    }
  } catch (error) {
    console.error('Error fetching member details:', error)
  }
}

const initializeWithExistingData = (memberId, memberData) => {
  if (memberId && memberData) {
    selectedMember.value = {
      id: memberData.id,
      name: memberData.name,
      type: memberData.type
    }
    memberType.value = memberData.type
    searchQuery.value = memberData.name
  }
}
</script>

