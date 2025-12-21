<template>
  <div class="relative">
    <!-- Search Input -->
    <div class="relative">
      <Input
        ref="inputRef"
        v-model="searchQuery"
        :placeholder="placeholder"
        @input="handleSearch"
        @focus="showDropdown = true"
        @keydown="handleKeyDown"
        class="w-full"
      />
      
      <!-- Dropdown -->
      <div
        v-if="showDropdown && searchResults.length > 0"
        class="absolute z-50 w-full mt-1 bg-[#ffffff] border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
      >
        <div
          v-for="result in searchResults"
          :key="result.family_no"
          @click="selectResult(result)"
          class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
        >
          <div class="flex items-center justify-between">
            <div>
              <div class="font-medium">{{ result.family_no }}</div>
              <div class="text-sm text-gray-500">
                {{ result.member_count }} member(s) • {{ result.sample_members }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Selected Family Display -->
    <div v-if="selectedFamily" class="mt-2 p-2 bg-gray-50 rounded border">
      <div class="flex items-center justify-between">
        <div>
          <div class="font-medium">{{ selectedFamily.family_no }}</div>
          <div class="text-sm text-gray-500">{{ selectedFamily.member_count }} member(s)</div>
        </div>
        <button
          @click="clearSelection"
          class="text-red-600 hover:text-red-800 text-sm"
        >
          ×
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import { Input } from '@/components/ui/input'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Search for family number...'
  }
})

const emit = defineEmits(['update:modelValue'])

const searchQuery = ref('')
const searchResults = ref([])
const showDropdown = ref(false)
const selectedFamily = ref(null)
const inputRef = ref(null)

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

const performSearch = async () => {
  try {
    const response = await fetch(`/member/search-families?q=${encodeURIComponent(searchQuery.value)}`)
    const data = await response.json()
    
    if (Array.isArray(data)) {
      searchResults.value = data.map(item => ({
        family_no: item.family_no || '',
        member_count: item.member_count || 0,
        sample_members: Array.isArray(item.members) ? item.members.join(', ') : 'No members'
      }))
    } else {
      searchResults.value = []
    }
  } catch (error) {
    console.error('Search error:', error)
    searchResults.value = []
  }
}

const selectResult = (result) => {
  selectedFamily.value = result
  emit('update:modelValue', result.family_no)
  searchQuery.value = result.family_no
  showDropdown.value = false

  nextTick(() => {
    inputRef.value?.$el?.focus()
  })
}

const clearSelection = () => {
  selectedFamily.value = null
  emit('update:modelValue', '')
  searchQuery.value = ''
}

const closeDropdown = () => {
  showDropdown.value = false
}

const handleKeyDown = (event) => {
  if (event.key === 'Tab' && showDropdown.value) {
    closeDropdown()
    nextTick(() => {
      inputRef.value?.$el?.focus()
    })
  } else if (event.key === 'Escape' && showDropdown.value) {
    event.preventDefault()
    closeDropdown()
  }
}

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  if (!newValue) {
    clearSelection()
  }
})

// Initialize if modelValue is provided
onMounted(() => {
  if (props.modelValue) {
    searchQuery.value = props.modelValue
    // Optionally fetch family details to populate the display
  }
})
</script>
