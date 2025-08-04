<template>
  <div class="space-y-2">
    <Label :for="id">{{ label }}</Label>
    
    <!-- Parish Selection Mode -->
    <div class="flex gap-2 mb-2">
      <button
        type="button"
        @click="selectionMode = 'dropdown'"
        :class="[
          'px-3 py-1 text-sm rounded-md transition',
          selectionMode === 'dropdown' 
            ? 'bg-blue-100 text-blue-700 border border-blue-300' 
            : 'bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200'
        ]"
      >
        Select Existing
      </button>
      <button
        type="button"
        @click="selectionMode = 'custom'"
        :class="[
          'px-3 py-1 text-sm rounded-md transition',
          selectionMode === 'custom' 
            ? 'bg-green-100 text-green-700 border border-green-300' 
            : 'bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200'
        ]"
      >
        Enter Custom
      </button>
    </div>

    <!-- Dropdown Selection -->
    <div v-if="selectionMode === 'dropdown'" class="space-y-2">
      <SelectInput
        :id="`${id}_dropdown`"
        v-model="selectedParishId"
        :options="parishOptions"
        placeholder="Select a parish..."
        @change="onParishSelected"
      />
      <p class="text-xs text-gray-500">
        Select from existing parishes in the system
      </p>
    </div>

    <!-- Custom Input -->
    <div v-if="selectionMode === 'custom'" class="space-y-2">
      <Input
        :id="`${id}_custom`"
        v-model="customParishName"
        placeholder="Enter parish name..."
        @input="onCustomParishInput"
        @blur="validateCustomParish"
      />
      <div v-if="validationMessage" class="text-xs" :class="validationMessage.type === 'error' ? 'text-red-500' : 'text-yellow-500'">
        {{ validationMessage.text }}
      </div>
      <div v-if="similarParishes.length > 0" class="text-xs text-blue-600">
        <p class="font-medium">Similar parishes found:</p>
        <ul class="list-disc list-inside mt-1">
          <li v-for="parish in similarParishes" :key="parish" class="cursor-pointer hover:underline" @click="selectSimilarParish(parish)">
            {{ parish }}
          </li>
        </ul>
      </div>
      <p class="text-xs text-gray-500">
        Enter a custom parish name (will be validated for duplicates)
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SelectInput } from '@/components/ui/select';
import axios from 'axios';

interface Props {
  id: string;
  label: string;
  modelValue: { parishId?: number | null; parishName?: string | null };
  parishes: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:modelValue': [value: { parishId?: number | null; parishName?: string | null }];
}>();

const selectionMode = ref<'dropdown' | 'custom'>('dropdown');
const selectedParishId = ref<number | null>(null);
const customParishName = ref<string>('');
const validationMessage = ref<{ type: 'error' | 'warning'; text: string } | null>(null);
const similarParishes = ref<string[]>([]);

// Create options for dropdown
const parishOptions = computed(() => {
  return [
    { id: '', name: 'Select a parish...' },
    ...props.parishes.map(parish => ({
      id: parish.id.toString(),
      name: parish.name
    }))
  ];
});

// Initialize based on modelValue
onMounted(() => {
  if (props.modelValue.parishId) {
    selectionMode.value = 'dropdown';
    selectedParishId.value = props.modelValue.parishId;
  } else if (props.modelValue.parishName) {
    selectionMode.value = 'custom';
    customParishName.value = props.modelValue.parishName;
  }
});

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  if (newValue.parishId) {
    selectionMode.value = 'dropdown';
    selectedParishId.value = newValue.parishId;
    customParishName.value = '';
  } else if (newValue.parishName) {
    selectionMode.value = 'custom';
    customParishName.value = newValue.parishName;
    selectedParishId.value = null;
  }
}, { deep: true });

// Handle parish selection from dropdown
const onParishSelected = () => {
  const parishId = selectedParishId.value;
  const parishName = parishId ? props.parishes.find(p => p.id === parishId)?.name : null;
  
  emit('update:modelValue', {
    parishId: parishId,
    parishName: null // Clear custom name when using dropdown
  });
  
  validationMessage.value = null;
  similarParishes.value = [];
};

// Handle custom parish input
const onCustomParishInput = () => {
  // Clear dropdown selection when using custom input
  selectedParishId.value = null;
  
  emit('update:modelValue', {
    parishId: null,
    parishName: customParishName.value || null
  });
  
  // Clear validation messages
  validationMessage.value = null;
  similarParishes.value = [];
};

// Validate custom parish name
const validateCustomParish = async () => {
  if (!customParishName.value.trim()) {
    validationMessage.value = null;
    similarParishes.value = [];
    return;
  }

  try {
    const response = await axios.post('/api/validate-parish', {
      name: customParishName.value.trim()
    });

    const { valid, exists, similar } = response.data;

    if (exists) {
      validationMessage.value = {
        type: 'error',
        text: response.data.message || 'This parish already exists. Please select it from the dropdown.'
      };
      similarParishes.value = [];
    } else if (similar && similar.length > 0) {
      validationMessage.value = {
        type: 'warning',
        text: 'Similar parishes found. Consider using one of these or enter a different name.'
      };
      similarParishes.value = similar;
    } else {
      validationMessage.value = null;
      similarParishes.value = [];
    }
  } catch (error) {
    console.error('Parish validation error:', error);
    validationMessage.value = {
      type: 'error',
      text: 'Error validating parish name. Please try again.'
    };
  }
};

// Select a similar parish
const selectSimilarParish = (parishName: string) => {
  const parish = props.parishes.find(p => p.name === parishName);
  if (parish) {
    selectionMode.value = 'dropdown';
    selectedParishId.value = parish.id;
    customParishName.value = '';
    onParishSelected();
  }
};
</script> 