<script setup lang="ts">
import { computed, ref } from 'vue';
import { formatDateForDisplay, formatDateForInput } from '@/lib/utils';
import { Calendar } from 'lucide-vue-next';

interface Props {
  modelValue?: string | null;
  placeholder?: string;
  disabled?: boolean;
  class?: string | string[] | Record<string, boolean>;
  id?: string;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'DD/MM/YYYY',
  disabled: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | null): void;
}>();

// Hidden date input reference
const dateInputRef = ref<HTMLInputElement | null>(null);

// Track focus state for styling
const isFocused = ref(false);

// Display value in DD/MM/YYYY format
const displayValue = computed(() => {
  if (!props.modelValue) return '';
  return formatDateForDisplay(props.modelValue);
});

// Value for the hidden HTML5 date input (YYYY-MM-DD)
const inputValue = computed({
  get: () => formatDateForInput(props.modelValue),
  set: (value: string) => {
    emit('update:modelValue', value || null);
  },
});

// Open date picker on click
const openDatePicker = () => {
  if (dateInputRef.value && !props.disabled) {
    try {
      dateInputRef.value.showPicker();
    } catch (error) {
      // Fallback for browsers that don't support showPicker()
      dateInputRef.value.focus();
    }
  }
};
</script>

<template>
  <div class="relative w-full h-9">
    <!-- Native date input (hidden) -->
    <input
      :id="id"
      ref="dateInputRef"
      v-model="inputValue"
      type="date"
      :disabled="disabled"
      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
      :class="{ 'cursor-not-allowed': disabled }"
      @click="openDatePicker"
      @focus="isFocused = true"
      @blur="isFocused = false"
    />

    <!-- Visible display showing DD/MM/YYYY format -->
    <div
      :class="[
        'flex h-9 w-full items-center rounded-md border border-gray-300 bg-white px-3 py-1 text-sm shadow-sm transition-colors pointer-events-none',
        isFocused && 'border-blue-500 ring-blue-500/50 ring-2',
        disabled && 'opacity-50 bg-gray-100',
        props.class
      ]"
    >
      <span v-if="displayValue" class="flex-1 text-gray-900">
        {{ displayValue }}
      </span>
      <span v-else class="flex-1 text-gray-400">
        {{ placeholder }}
      </span>
      <Calendar class="h-4 w-4 text-gray-400 ml-2 shrink-0" />
    </div>
  </div>
</template>
