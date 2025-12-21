<template>
  <transition name="fade-scale">
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="closeModal">
      <div class="w-full max-w-2xl max-h-[80vh] overflow-y-auto bg-[#ffffff] rounded-2xl shadow-2xl" @keydown="handleFocusTrap">
        <!-- Focus trap start -->
        <div ref="firstFocusRef" tabindex="0" class="sr-only"></div>
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-red-50 rounded-t-2xl">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
              <svg class="w-[1.5rem] h-[1.5rem] text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-bold text-red-800">Validation Errors Found</h2>
              <p class="text-sm text-red-600">{{ totalErrors }} error{{ totalErrors !== 1 ? 's' : '' }} need{{ totalErrors !== 1 ? '' : 's' }} to be fixed</p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-[1.5rem] h-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6">
          <div class="space-y-6">
            <!-- Personal Information -->
            <div v-if="personalInfoErrors.length > 0" class="error-section">
              <div class="flex items-center gap-2 mb-3">
                <div class="w-[1.5rem] h-[1.5rem] bg-blue-100 rounded-full flex items-center justify-center">
                  <svg class="w-[1rem] h-[1rem] text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
              </div>
              <div class="space-y-2">
                <div 
                  v-for="error in personalInfoErrors" 
                  :key="error.field"
                  @click="focusField(error.field)"
                  class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg cursor-pointer hover:bg-red-100 transition group"
                >
                  <div class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></div>
                  <span class="text-red-700 group-hover:text-red-800">{{ error.message }}</span>
                  <svg class="w-[1rem] h-[1rem] text-red-400 ml-auto group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Contact Details -->
            <div v-if="contactErrors.length > 0" class="error-section">
              <div class="flex items-center gap-2 mb-3">
                <div class="w-[1.5rem] h-[1.5rem] bg-green-100 rounded-full flex items-center justify-center">
                  <svg class="w-[1rem] h-[1rem] text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Contact Details</h3>
              </div>
              <div class="space-y-2">
                <div 
                  v-for="error in contactErrors" 
                  :key="error.field"
                  @click="focusField(error.field)"
                  class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg cursor-pointer hover:bg-red-100 transition group"
                >
                  <div class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></div>
                  <span class="text-red-700 group-hover:text-red-800">{{ error.message }}</span>
                  <svg class="w-[1rem] h-[1rem] text-red-400 ml-auto group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Address Information -->
            <div v-if="addressErrors.length > 0" class="error-section">
              <div class="flex items-center gap-2 mb-3">
                <div class="w-[1.5rem] h-[1.5rem] bg-purple-100 rounded-full flex items-center justify-center">
                  <svg class="w-[1rem] h-[1rem] text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Address Information</h3>
              </div>
              <div class="space-y-2">
                <div 
                  v-for="error in addressErrors" 
                  :key="error.field"
                  @click="focusField(error.field)"
                  class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg cursor-pointer hover:bg-red-100 transition group"
                >
                  <div class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></div>
                  <span class="text-red-700 group-hover:text-red-800">{{ error.message }}</span>
                  <svg class="w-[1rem] h-[1rem] text-red-400 ml-auto group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Community Information -->
            <div v-if="communityErrors.length > 0" class="error-section">
              <div class="flex items-center gap-2 mb-3">
                <div class="w-[1.5rem] h-[1.5rem] bg-orange-100 rounded-full flex items-center justify-center">
                  <svg class="w-[1rem] h-[1rem] text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Community Information</h3>
              </div>
              <div class="space-y-2">
                <div 
                  v-for="error in communityErrors" 
                  :key="error.field"
                  @click="focusField(error.field)"
                  class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg cursor-pointer hover:bg-red-100 transition group"
                >
                  <div class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></div>
                  <span class="text-red-700 group-hover:text-red-800">{{ error.message }}</span>
                  <svg class="w-[1rem] h-[1rem] text-red-400 ml-auto group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Religious Information -->
            <div v-if="religiousErrors.length > 0" class="error-section">
              <div class="flex items-center gap-2 mb-3">
                <div class="w-[1.5rem] h-[1.5rem] bg-yellow-100 rounded-full flex items-center justify-center">
                  <svg class="w-[1rem] h-[1rem] text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Religious Information</h3>
              </div>
              <div class="space-y-2">
                <div 
                  v-for="error in religiousErrors" 
                  :key="error.field"
                  @click="focusField(error.field)"
                  class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg cursor-pointer hover:bg-red-100 transition group"
                >
                  <div class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></div>
                  <span class="text-red-700 group-hover:text-red-800">{{ error.message }}</span>
                  <svg class="w-[1rem] h-[1rem] text-red-400 ml-auto group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Other Errors -->
            <div v-if="otherErrors.length > 0" class="error-section">
              <div class="flex items-center gap-2 mb-3">
                <div class="w-[1.5rem] h-[1.5rem] bg-gray-100 rounded-full flex items-center justify-center">
                  <svg class="w-[1rem] h-[1rem] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Other Information</h3>
              </div>
              <div class="space-y-2">
                <div 
                  v-for="error in otherErrors" 
                  :key="error.field"
                  @click="focusField(error.field)"
                  class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg cursor-pointer hover:bg-red-100 transition group"
                >
                  <div class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></div>
                  <span class="text-red-700 group-hover:text-red-800">{{ error.message }}</span>
                  <svg class="w-[1rem] h-[1rem] text-red-400 ml-auto group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
          <div class="text-sm text-gray-600">
            Click on any error to navigate to the corresponding field
          </div>
          <div class="flex gap-3">
            <button 
              @click="closeModal"
              class="px-4 py-2 text-gray-700 bg-[#ffffff] border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
              Close
            </button>
            <!-- <button 
              @click="submitAnyway"
              class="px-4 py-2 text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition"
            >
              Submit Anyway
            </button> -->
          </div>
        </div>

        <!-- Focus trap end -->
        <div ref="lastFocusRef" tabindex="0" class="sr-only"></div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

// Focus management refs
const firstFocusRef = ref<HTMLElement | null>(null);
const lastFocusRef = ref<HTMLElement | null>(null);

// Focus trap handler
const handleFocusTrap = (event: KeyboardEvent) => {
  if (event.key !== 'Tab') return;

  if (event.shiftKey) {
    // Shift+Tab on first element - move to last
    if (document.activeElement === firstFocusRef.value) {
      event.preventDefault();
      lastFocusRef.value?.focus();
    }
  } else {
    // Tab on last element - move to first
    if (document.activeElement === lastFocusRef.value) {
      event.preventDefault();
      firstFocusRef.value?.focus();
    }
  }
};

interface ValidationError {
  field: string;
  message: string;
}

interface Props {
  modelValue: boolean;
  errors: ValidationError[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
  'update:modelValue': [value: boolean];
  'focus-field': [field: string];
  'submit-anyway': [];
}>();

// Categorize errors by field groups
const personalInfoFields = ['first_name', 'last_name', 'middle_name', 'date_of_birth', 'gender_id', 'blood_group_id', 'marital_status', 'aadhar'];
const contactFields = ['contact_no_1', 'contact_no_2', 'email'];
const addressFields = ['permanent_add1', 'permanent_add2', 'permanent_add3', 'permanent_town_id', 'permanent_city', 'permanent_pincode', 'permanent_state_id', 'permanent_country_id', 'current_add1', 'current_add2', 'current_add3', 'current_town_id', 'current_city', 'current_pincode', 'current_state_id', 'current_country_id'];
const communityFields = ['community_id', 'community_cluster_id', 'relationship_id', 'relation_member_id'];
const religiousFields = ['baptism_date', 'baptism_reg_no', 'baptism_parish', 'baptism_parish_id', 'confirmation_date', 'confirmation_reg_no', 'confirmation_parish', 'confirmation_parish_id', 'marriage_date', 'marriage_reg_no', 'marriage_parish', 'marriage_parish_id', 'death_parish', 'death_parish_id'];

const personalInfoErrors = computed(() => 
  props.errors.filter(error => personalInfoFields.includes(error.field))
);

const contactErrors = computed(() => 
  props.errors.filter(error => contactFields.includes(error.field))
);

const addressErrors = computed(() => 
  props.errors.filter(error => addressFields.includes(error.field))
);

const communityErrors = computed(() => 
  props.errors.filter(error => communityFields.includes(error.field))
);

const religiousErrors = computed(() => 
  props.errors.filter(error => religiousFields.includes(error.field))
);

const otherErrors = computed(() => 
  props.errors.filter(error => 
    !personalInfoFields.includes(error.field) &&
    !contactFields.includes(error.field) &&
    !addressFields.includes(error.field) &&
    !communityFields.includes(error.field) &&
    !religiousFields.includes(error.field)
  )
);

const totalErrors = computed(() => props.errors.length);

const closeModal = () => {
  emit('update:modelValue', false);
};

const focusField = (field: string) => {
  emit('focus-field', field);
  closeModal();
};

const submitAnyway = () => {
  emit('submit-anyway');
  closeModal();
};
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.error-section {
  border-left-width: 4px;
  padding-left: 1rem;
  
}
</style> 