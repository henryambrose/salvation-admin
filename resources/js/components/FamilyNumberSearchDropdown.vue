<template>
  <div ref="wrapperRef" class="relative">
    <!-- Search Input -->
    <div class="relative">
      <Input
        ref="inputRef"
        v-model="searchQuery"
        :placeholder="placeholder"
        @input="handleSearch"
        @focus="handleFocus"
        @keydown="handleKeyDown"
        class="w-full"
      />

      <!-- Dropdown -->
      <div
        v-if="showDropdown && searchResults.length > 0"
        class="absolute z-50 mt-1 max-h-60 w-full overflow-y-auto rounded-md border border-gray-300 bg-[#ffffff] shadow-lg"
      >
        <div
          v-for="result in searchResults"
          :key="result.family_no"
          @click="selectResult(result)"
          class="cursor-pointer border-b border-gray-100 px-4 py-2 last:border-b-0 hover:bg-gray-100"
        >
          <div class="flex items-center justify-between">
            <div>
              <div class="font-medium">{{ result.family_no }}</div>
              <div class="text-sm text-gray-500">{{ result.member_count }} member(s) • {{ result.sample_members }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { onClickOutside } from '@vueuse/core';
import { nextTick, onMounted, ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'Search for family number...',
  },
});

const emit = defineEmits(['update:modelValue']);

const searchQuery = ref('');
const searchResults = ref([]);
const showDropdown = ref(false);
const selectedFamily = ref(null);
const inputRef = ref(null);
const wrapperRef = ref(null);

onClickOutside(wrapperRef, () => {
  showDropdown.value = false;
});

const justSelected = ref(false);

const handleFocus = () => {
  if (justSelected.value) {
    justSelected.value = false;
    return;
  }
  showDropdown.value = true;
};

// Debounce search
let searchTimeout = null;

const handleSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    if (searchQuery.value.length >= 2) {
      performSearch();
    } else {
      searchResults.value = [];
    }
  }, 300);
};

const performSearch = async () => {
  try {
    const response = await fetch(`/member/search-families?q=${encodeURIComponent(searchQuery.value)}`);
    const data = await response.json();

    if (Array.isArray(data)) {
      searchResults.value = data.map((item) => ({
        family_no: item.family_no || '',
        member_count: item.member_count || 0,
        sample_members: Array.isArray(item.members) ? item.members.join(', ') : 'No members',
      }));
    } else {
      searchResults.value = [];
    }
  } catch (error) {
    console.error('Search error:', error);
    searchResults.value = [];
  }
};

const selectResult = (result) => {
  selectedFamily.value = result;
  emit('update:modelValue', result.family_no);
  searchQuery.value = result.family_no;
  showDropdown.value = false;
  searchResults.value = [];
  justSelected.value = true;
};

const clearSelection = () => {
  selectedFamily.value = null;
  emit('update:modelValue', '');
  searchQuery.value = '';
};

const closeDropdown = () => {
  showDropdown.value = false;
};

const handleKeyDown = (event) => {
  if (event.key === 'Tab' && showDropdown.value) {
    closeDropdown();
    nextTick(() => {
      inputRef.value?.$el?.focus();
    });
  } else if (event.key === 'Escape' && showDropdown.value) {
    event.preventDefault();
    closeDropdown();
  }
};

// Watch for external changes to modelValue
watch(
  () => props.modelValue,
  (newValue) => {
    if (!newValue) {
      clearSelection();
    }
  },
);

// Initialize if modelValue is provided
onMounted(() => {
  if (props.modelValue) {
    searchQuery.value = props.modelValue;
    // Optionally fetch family details to populate the display
  }
});
</script>
