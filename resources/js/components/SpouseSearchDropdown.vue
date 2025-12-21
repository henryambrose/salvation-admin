<template>
  <div class="relative">
    <!-- Input Field -->
    <div class="relative">
      <input
        ref="inputRef"
        v-model="searchQuery"
        type="text"
        :placeholder="placeholder"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#3b82f6] focus:border-blue-500 pr-10"
        @focus="showDropdown = true"
        @blur="handleBlur"
        @keydown.escape="closeDropdown"
        @keydown.enter="selectFirstResult"
        @keydown.down="navigateResults(1)"
        @keydown.up="navigateResults(-1)"
        @keydown.tab="handleTab"
      />
      
      <!-- Clear Button -->
      <button
        v-if="selectedMember || searchQuery"
        @click="clearSelection"
        class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
      >
        ✕
      </button>
      
      <!-- Loading Indicator -->
      <div
        v-if="isLoading"
        class="absolute right-2 top-1/2 transform -translate-y-1/2"
      >
        <div class="animate-spin rounded-full h-[1rem] w-[1rem] border-b-2 border-blue-500"></div>
      </div>
    </div>

    <!-- Dropdown Results -->
    <div
      v-if="showDropdown && (searchResults.length > 0 || isLoading || searchQuery.length >= 2)"
      class="absolute z-50 w-full mt-1 bg-[#ffffff] border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
      <!-- Loading State -->
      <div v-if="isLoading" class="p-3 text-center text-gray-500">
        Searching...
      </div>
      
      <!-- No Results -->
      <div v-else-if="searchResults.length === 0 && searchQuery.length >= 2" class="p-3 text-center text-gray-500">
        No members found
      </div>
      
      <!-- Results List -->
      <div v-else>
        <div
          v-for="(member, index) in searchResults"
          :key="member.id"
          @click="selectMember(member)"
          @mouseenter="highlightedIndex = index"
          :class="[
            'p-3 cursor-pointer hover:bg-blue-50 transition-colors',
            highlightedIndex === index ? 'bg-blue-50' : '',
            selectedMember?.id === member.id ? 'bg-blue-100' : ''
          ]"
        >
          <div class="font-medium text-gray-900">{{ member.full_name }}</div>
          <div class="text-sm text-gray-600">
            {{ member.member_no }} • {{ member.family_no }}
          </div>
          <div class="text-xs text-gray-500">
            {{ member.community }} • {{ member.relationship }}
          </div>
        </div>
      </div>
    </div>
    
    <!-- Help Text -->
    <!-- <div class="text-xs text-gray-500 mt-1">
      💡 Tip: Type member number (e.g., SAL-001) and press Enter, or click on search results
    </div> -->
  </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import axios from 'axios';

interface Member {
  id: number;
  text: string;
  member_no: string;
  family_no: string;
  full_name: string;
  community: string;
  relationship: string;
  gender: string;
}

interface Props {
  modelValue?: number | null;
  placeholder?: string;
  excludeId?: number;
  familyNo?: string; // Add this prop
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Search for spouse by name, member number, or family number...',
  excludeId: undefined
});

const emit = defineEmits<{
  'update:modelValue': [value: number | null];
}>();

// Reactive data
const searchQuery = ref('');
const searchResults = ref<Member[]>([]);
const selectedMember = ref<Member | null>(null);
const showDropdown = ref(false);
const isLoading = ref(false);
const highlightedIndex = ref(-1);
const inputRef = ref<HTMLInputElement | null>(null)

// Debounce timer
let searchTimeout: number;

// Member number pattern
const memberNumberPattern = /^[A-Z]{3}-\d{3}$/;

// Methods
const performSearch = async (query: string, isIdSearch: boolean = false) => {
  if (!isIdSearch && query.length < 2) return;
  
  isLoading.value = true;
  showDropdown.value = true;
  
  try {
    const params = new URLSearchParams({
      q: query,
      limit: isIdSearch ? '1' : '10'
    });
    
    if (props.excludeId) {
      params.append('exclude_id', props.excludeId.toString());
    }
    
    // Add family number filter if provided
    if (props.familyNo) {
      params.append('familyNo', props.familyNo);
    }
    
    const response = await axios.get(`/member/search-members?${params}`);
    
    if (isIdSearch) {
      // For ID search, select the member directly
      if (response.data.length > 0) {
        selectMember(response.data[0]);
      }
    } else {
      // For regular search, update results
      searchResults.value = response.data;
      highlightedIndex.value = -1;
    }
  } catch (error) {
    console.error('Error searching members:', error);
    if (!isIdSearch) {
      searchResults.value = [];
    }
  } finally {
    isLoading.value = false;
  }
};

// Update the watch for modelValue to use the consolidated function
watch(() => props.modelValue, (newValue) => {
  if (newValue && !selectedMember.value) {
    // Use the same search function for loading existing data
    performSearch(newValue.toString(), true);
  } else if (!newValue) {
    selectedMember.value = null;
    searchQuery.value = '';
  }
}, { immediate: true });

// Watch search query for debounced search
watch(searchQuery, (newQuery) => {
  clearTimeout(searchTimeout);
  
  if (newQuery.length >= 2) {
    searchTimeout = setTimeout(() => {
      performSearch(newQuery);
    }, 300); // 300ms debounce
  } else {
    searchResults.value = [];
    showDropdown.value = false;
  }
  
  // Check if it's a member number and handle it
  if (memberNumberPattern.test(newQuery)) {
    handleMemberNumberInput();
  }
});

const selectMember = (member: Member) => {
  selectedMember.value = member;
  searchQuery.value = member.full_name;
  emit('update:modelValue', member.id);
  closeDropdown();

  nextTick(() => {
    inputRef.value?.focus()
  })
};

const clearSelection = () => {
  selectedMember.value = null;
  searchQuery.value = '';
  searchResults.value = [];
  emit('update:modelValue', null);
  showDropdown.value = false;
};

const closeDropdown = () => {
  setTimeout(() => {
    showDropdown.value = false;
  }, 200);
};

const handleBlur = () => {
  // Keep dropdown open briefly to allow for clicks
  setTimeout(() => {
    if (!document.activeElement?.closest('.relative')) {
      showDropdown.value = false;
    }
  }, 200);
};

const navigateResults = (direction: number) => {
  if (searchResults.value.length === 0) return;
  
  const newIndex = highlightedIndex.value + direction;
  if (newIndex >= 0 && newIndex < searchResults.value.length) {
    highlightedIndex.value = newIndex;
  } else if (newIndex >= searchResults.value.length) {
    highlightedIndex.value = 0;
  } else if (newIndex < 0) {
    highlightedIndex.value = searchResults.value.length - 1;
  }
};

const selectFirstResult = () => {
  if (searchResults.value.length > 0) {
    const index = highlightedIndex.value >= 0 ? highlightedIndex.value : 0;
    selectMember(searchResults.value[index]);
  } else if (memberNumberPattern.test(searchQuery.value)) {
    // If no results but input looks like member number, try to find it
    handleMemberNumberInput();
  }
};

const handleMemberNumberInput = async () => {
  if (memberNumberPattern.test(searchQuery.value)) {
    performSearch(searchQuery.value, true);
  }
};

const handleTab = () => {
  if (showDropdown.value) {
    closeDropdown()
  }
}
</script> 