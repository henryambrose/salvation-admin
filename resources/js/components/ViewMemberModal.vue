<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps<{
  modelValue: boolean;
  member: Record<string, any> | null;
}>();

const emit = defineEmits(['update:modelValue']);

const modalRef = ref<HTMLElement | null>(null);
const currentTab = ref('basic');

// Close modal + return focus
function closeModal() {
  emit('update:modelValue', false);
}

// Trap focus inside modal when open
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
    <div v-if="modelValue" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black" role="dialog" aria-modal="true">
      <div ref="modalRef" class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 shadow-lg transition-all duration-200">
        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-xl font-semibold">Member Details</h3>
          <button class="text-gray-500 hover:text-gray-700" @click="closeModal" aria-label="Close Modal">✕</button>
        </div>

        <!-- Tabs -->
        <div class="mb-4 flex gap-4 border-b pb-2">
          <button
            class="border-b-2 px-3 py-1 text-sm font-medium"
            :class="currentTab === 'basic' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600'"
            @click="currentTab = 'basic'"
          >
            Basic Info
          </button>
          <button
            class="border-b-2 px-3 py-1 text-sm font-medium"
            :class="currentTab === 'other' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600'"
            @click="currentTab = 'other'"
          >
            Other Info
          </button>
          <button
            class="border-b-2 px-3 py-1 text-sm font-medium"
            :class="currentTab === 'custom' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600'"
            @click="currentTab = 'custom'"
          >
            More (Slot)
          </button>
        </div>

        <!-- Tab Content -->
        <div v-if="member">
          <div v-show="currentTab === 'basic'" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div><strong>First Name:</strong> {{ member.first_name }}</div>
            <div><strong>Last Name:</strong> {{ member.last_name }}</div>
            <div><strong>Email:</strong> {{ member.email }}</div>
            <div><strong>Phone:</strong> {{ member.contact_no }}</div>
            <div><strong>Community:</strong> {{ member.community_id }}</div>
            <div><strong>Address:</strong> {{ member.permanent_add1 }}</div>
          </div>

          <div v-show="currentTab === 'other'" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div><strong>Age:</strong> {{ member.age }}</div>
            <div><strong>Added On:</strong> {{ member.added_on }}</div>
            <div><strong>Last Updated:</strong> {{ member.last_updated }}</div>
          </div>

          <div v-show="currentTab === 'custom'" class="mt-4">
            <slot name="extra" :member="member" />
          </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-right">
          <button @click="closeModal" class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-700">Close</button>
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
