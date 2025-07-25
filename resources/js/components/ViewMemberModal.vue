<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, computed } from 'vue';

const props = defineProps<{
  modelValue: boolean;
  member: Record<string, any> | null;
  familyIncomeRange: Record<string, any> | null;
}>();

const emit = defineEmits(['update:modelValue']);

const modalRef = ref<HTMLElement | null>(null);
const currentTab = ref('personal');

// Tab definitions with categories and fields
const tabs = [
  {
    key: 'personal',
    label: 'Personal Info',
    fields: [
      { key: 'first_name', label: 'First Name' },
      { key: 'middle_name', label: 'Middle Name' },
      { key: 'last_name', label: 'Last Name' },
      { key: 'date_of_birth', label: 'Date of Birth' },
      { key: 'age', label: 'Age' },
      { key: 'aadhar', label: 'Aadhar' },
      { key: 'new_olsc_id', label: 'New OLSC ID' },
      { key: 'old_sal_id', label: 'Old SAL ID' },
    ],
  },
  {
    key: 'religious',
    label: 'Religious Details',
    fields: [
      { key: 'baptism_date', label: 'Baptism Date' },
      { key: 'baptism_reg_no', label: 'Baptism Reg. No.' },
      { key: 'baptism_parish', label: 'Baptism Parish' },
      { key: 'confirmation_date', label: 'Confirmation Date' },
      { key: 'confirmation_reg_no', label: 'Confirmation Reg. No.' },
      { key: 'confirmation_parish', label: 'Confirmation Parish' },
      { key: 'marriage_date', label: 'Marriage Date' },
      { key: 'marriage_reg_no', label: 'Marriage Reg. No.' },
      { key: 'marriage_parish', label: 'Marriage Parish' },
      { key: 'death_date', label: 'Death Date' },
      { key: 'deaths_reg_no', label: 'Death Reg. No.' },
      { key: 'death_parish', label: 'Death Parish' },
    ],
  },
  {
    key: 'contact',
    label: 'Contact Info',
    fields: [
      { key: 'contact_no', label: 'Contact No' },
      { key: 'email', label: 'Email' },
    ],
  },
  {
    key: 'permanent_address',
    label: 'Permanent Address',
    fields: [
      { key: 'permanent_add1', label: 'Address 1' },
      { key: 'permanent_add2', label: 'Address 2' },
      { key: 'permanent_add3', label: 'Address 3' },
      { key: 'permanent_town', label: 'Town' },
      { key: 'permanent_city', label: 'City' },
      { key: 'permanent_pincode', label: 'Pincode' },
      { key: 'permanent_state', label: 'State' },
      { key: 'permanent_country', label: 'Country' },
    ],
  },
  {
    key: 'current_address',
    label: 'Current Address',
    fields: [
      { key: 'current_add1', label: 'Address 1' },
      { key: 'current_add2', label: 'Address 2' },
      { key: 'current_add3', label: 'Address 3' },
      { key: 'current_town', label: 'Town' },
      { key: 'current_city', label: 'City' },
      { key: 'current_pincode', label: 'Pincode' },
      { key: 'current_state', label: 'State' },
      { key: 'current_country', label: 'Country' },
    ],
  },
  {
    key: 'community',
    label: 'Community & Family',
    fields: [
      { key: 'community_id', label: 'Community' },
      { key: 'community_cluster_id', label: 'Cluster' },
      { key: 'family_no', label: 'Family No' },
    ],
  },
  {
    key: 'education',
    label: 'Education & Work',
    fields: [
      { key: 'school_name', label: 'School Name' },
      { key: 'college_name', label: 'College Name' },
      { key: 'latest_qualifications', label: 'Latest Qualifications' },
      { key: 'company_name', label: 'Company Name' },
      { key: 'designation', label: 'Designation' },
      { key: 'family_income_range', label: 'Family Income Range' },
    ],
  },
  {
    key: 'other',
    label: 'Other',
    fields: [
      { key: 'blood_group_id', label: 'Blood Group' },
      { key: 'cells_and_association_id', label: 'Cells & Association' },
    ],
  },
];

const enhancedFamilyIncomeRanges = computed(() => {
  const c = props.familyIncomeRange || {};
  return {
    data: c.data || [],
    prev_page_url: c.prev_page_url ?? c.meta?.prev_page_url,
    next_page_url: c.next_page_url ?? c.meta?.next_page_url,
    current_page: c.current_page ?? c.meta?.current_page,
    last_page: c.last_page ?? c.meta?.last_page,
  };
});

function closeModal() {
  emit('update:modelValue', false);
}

function handleTab(e: KeyboardEvent) {
  if (!modalRef.value || !props.modelValue) return;

  const focusable = modalRef.value.querySelectorAll<HTMLElement>('a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])');
  const first = focusable[0];
  const last = focusable[focusable.length - 1];

  if (e.key === 'Tab') {
    if (e.shiftKey) {
      if (document.activeElement === first) {
        e.preventDefault();
        last.focus();
      }
    } else {
      if (document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }

  if (e.key === 'Escape') {
    closeModal();
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleTab);
});

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleTab);
});
</script>

<template>
  <transition name="fade-scale">
    <div v-if="modelValue" class="bg-opacity-60 fixed inset-0 z-50 flex items-center justify-center bg-black" role="dialog" aria-modal="true">
      <div
        ref="modalRef"
        class="max-h-[90vh] w-full max-w-6xl overflow-y-auto rounded-2xl border border-gray-200 bg-gray-50 p-0 shadow-2xl transition-all duration-200"
        style="overflow-x:hidden;"
      >
        <!-- Header -->
        <div class="flex items-center justify-between rounded-t-2xl bg-blue-600 px-6 py-4">
          <h3 class="text-2xl font-bold text-white">Member Details</h3>
          <button class="rounded-full bg-white/20 p-2 text-white hover:bg-white/40" @click="closeModal" aria-label="Close Modal">
            <span class="text-2xl leading-none">×</span>
          </button>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 border-b border-gray-200 bg-white px-6 pt-4">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            class="rounded-full px-4 py-2 text-sm font-medium transition-all duration-150"
            :class="currentTab === tab.key
              ? 'bg-blue-600 text-white shadow'
              : 'bg-gray-100 text-gray-700 hover:bg-blue-100'"
            @click="currentTab = tab.key"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Tab Content -->
        <div v-if="member" class="bg-white px-6 py-6">
          <div
            v-for="tab in tabs"
            :key="tab.key"
            v-show="currentTab === tab.key"
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3"
          >
            <div
              v-for="field in tab.fields"
              :key="field.key"
              class="rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 shadow-sm"
            >
              <div class="text-xs font-semibold text-gray-500">{{ field.label }}</div>
              <div class="mt-1 text-base font-medium text-gray-800 break-all">
                {{ member[field.key] ?? '—' }}
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 left-0 right-0 z-10 flex justify-end rounded-b-2xl bg-gray-100 px-6 py-4">
          <button @click="closeModal" class="rounded bg-blue-600 px-6 py-2 text-lg font-semibold text-white shadow hover:bg-blue-700 transition">
            Close
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

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
</style>
