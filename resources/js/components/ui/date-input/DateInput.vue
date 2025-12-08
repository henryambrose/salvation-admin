<script setup lang="ts">
import { computed, ref, watch, nextTick } from 'vue';
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

// Hidden date input reference for calendar picker
const dateInputRef = ref<HTMLInputElement | null>(null);

// Track focus state for styling
const isFocused = ref(false);

// Local editable value in DD/MM/YYYY format
const editableValue = ref('');

// Reference to the text input element
const textInputRef = ref<HTMLInputElement | null>(null);

// Initialize editable value from model value
const initializeEditableValue = () => {
  if (props.modelValue) {
    editableValue.value = formatDateForDisplay(props.modelValue);
  } else {
    editableValue.value = '';
  }
};

// Watch for external changes to modelValue
watch(() => props.modelValue, () => {
  if (!isFocused.value) {
    initializeEditableValue();
  }
});

// Value for the hidden HTML5 date input (YYYY-MM-DD)
const inputValue = computed({
  get: () => formatDateForInput(props.modelValue),
  set: (value: string) => {
    emit('update:modelValue', value || null);
    initializeEditableValue();
  },
});

// Auto-format date input with slashes as user types
const formatDateAsUserTypes = (input: string): string => {
  // Remove all non-numeric characters
  const numbersOnly = input.replace(/\D/g, '');

  // Limit to 8 digits (DDMMYYYY)
  const limited = numbersOnly.slice(0, 8);

  // Add slashes automatically
  let formatted = '';
  for (let i = 0; i < limited.length; i++) {
    if (i === 2 || i === 4) {
      formatted += '/';
    }
    formatted += limited[i];
  }

  return formatted;
};

// Parse DD/MM/YYYY to YYYY-MM-DD
const parseDateInput = (input: string): string | null => {
  // Remove any non-numeric characters except /
  const cleaned = input.replace(/[^\d/]/g, '');

  // Try to match DD/MM/YYYY format
  const match = cleaned.match(/^(\d{1,2})[\/]?(\d{1,2})[\/]?(\d{4})$/);

  if (match) {
    const day = match[1].padStart(2, '0');
    const month = match[2].padStart(2, '0');
    const year = match[3];

    // Basic validation
    const dayNum = parseInt(day);
    const monthNum = parseInt(month);
    const yearNum = parseInt(year);

    if (monthNum >= 1 && monthNum <= 12 && dayNum >= 1 && dayNum <= 31 && yearNum >= 1900 && yearNum <= 2100) {
      return `${year}-${month}-${day}`;
    }
  }

  return null;
};

// Handle manual input change with auto-formatting
const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const cursorPosition = target.selectionStart || 0;
  const previousValue = editableValue.value;

  // Get current input value and extract digits only
  const currentInput = target.value;
  const previousDigits = previousValue.replace(/\D/g, '');
  const currentDigits = currentInput.replace(/\D/g, '');

  // Check if user is deleting (backspace)
  const isDeleting = currentDigits.length < previousDigits.length;

  // Format the input
  const formatted = formatDateAsUserTypes(currentInput);

  // Count how many digits are before the cursor position in the current input
  const digitsBeforeCursor = currentInput.slice(0, cursorPosition).replace(/\D/g, '').length;

  // Update the value
  editableValue.value = formatted;

  // Calculate new cursor position
  // Find the position in formatted string where we have the same number of digits
  nextTick(() => {
    if (!target) return;

    let newCursorPos = 0;
    let digitsSeen = 0;

    for (let i = 0; i < formatted.length; i++) {
      if (/\d/.test(formatted[i])) {
        digitsSeen++;
        if (digitsSeen === digitsBeforeCursor) {
          newCursorPos = i + 1;
          break;
        }
      } else if (digitsSeen === digitsBeforeCursor) {
        // If we're at a slash and have seen all the digits, position after the slash
        newCursorPos = i + 1;
        break;
      }
    }

    // If we've seen all digits, position at the end
    if (digitsSeen === digitsBeforeCursor || digitsSeen < digitsBeforeCursor) {
      newCursorPos = newCursorPos || formatted.length;
    }

    target.setSelectionRange(newCursorPos, newCursorPos);
  });
};

// Handle blur - validate and update model value
const handleBlur = () => {
  isFocused.value = false;

  if (!editableValue.value.trim()) {
    emit('update:modelValue', null);
    return;
  }

  const parsedDate = parseDateInput(editableValue.value);

  if (parsedDate) {
    emit('update:modelValue', parsedDate);
  } else {
    // Invalid date - reset to previous valid value
    initializeEditableValue();
  }
};

// Handle focus
const handleFocus = () => {
  isFocused.value = true;
  initializeEditableValue();
};

// Open date picker on calendar icon click
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

// Initialize on mount
initializeEditableValue();
</script>

<template>
  <div class="relative w-full h-9">
    <!-- Hidden native date input for calendar picker -->
    <input
      ref="dateInputRef"
      v-model="inputValue"
      type="date"
      :disabled="disabled"
      class="absolute inset-0 w-full h-full opacity-0 pointer-events-none"
      tabindex="-1"
    />

    <!-- Visible text input for manual entry -->
    <div class="relative w-full h-9">
      <input
        :id="id"
        ref="textInputRef"
        v-model="editableValue"
        type="text"
        :disabled="disabled"
        :placeholder="placeholder"
        maxlength="10"
        inputmode="numeric"
        autocomplete="off"
        :class="[
          'flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 pr-9 text-sm shadow-sm transition-colors',
          'focus:border-blue-500 focus:ring-blue-500/50 focus:ring-2 focus:outline-none',
          disabled && 'opacity-50 bg-gray-100 cursor-not-allowed',
          props.class
        ]"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
      />

      <!-- Calendar icon button -->
      <button
        type="button"
        :disabled="disabled"
        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
        :class="{ 'cursor-not-allowed opacity-50': disabled }"
        @click="openDatePicker"
        tabindex="-1"
      >
        <Calendar class="h-4 w-4" />
      </button>
    </div>
  </div>
</template>
