<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ArrowLeftIcon, CheckCircleIcon, ClockIcon, XCircleIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface Condolence {
  id: number;
  visitor_name: string;
  visitor_email?: string;
  visitor_phone?: string;
  relationship?: string;
  message: string;
  visitor_ip?: string;
  created_at: string;
  is_approved: boolean;
  is_rejected: boolean;
}

interface Props {
  obituary: {
    uuid: string;
  };
  deceasedName: string;
  condolences: {
    data: Condolence[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
}

const props = defineProps<Props>();

const isProcessing = ref<{ [key: number]: boolean }>({});
const successMessage = ref('');
const errorMessage = ref('');

const approveCondolence = async (condolenceId: number) => {
  isProcessing.value[condolenceId] = true;

  try {
    const response = await fetch(`/obituary/${props.obituary.uuid}/manage/condolences/${condolenceId}/approve`, {
      method: 'PATCH',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });

    const data = await response.json();

    if (response.ok && data.success) {
      // Update the condolence in the local data
      const condolence = props.condolences.data.find((c) => c.id === condolenceId);
      if (condolence) {
        condolence.is_approved = true;
        condolence.is_rejected = false;
      }
      successMessage.value = data.message || 'Condolence approved successfully!';
      setTimeout(() => {
        successMessage.value = '';
      }, 3000);
    } else {
      errorMessage.value = data.error || 'Failed to approve condolence.';
      setTimeout(() => {
        errorMessage.value = '';
      }, 5000);
    }
  } catch (error) {
    console.error('Approve error:', error);
    errorMessage.value = 'Network error. Please try again.';
    setTimeout(() => {
      errorMessage.value = '';
    }, 5000);
  } finally {
    isProcessing.value[condolenceId] = false;
  }
};

const rejectCondolence = async (condolenceId: number) => {
  isProcessing.value[condolenceId] = true;

  try {
    const response = await fetch(`/obituary/${props.obituary.uuid}/manage/condolences/${condolenceId}/reject`, {
      method: 'PATCH',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });

    const data = await response.json();

    if (response.ok && data.success) {
      // Update the condolence in the local data
      const condolence = props.condolences.data.find((c) => c.id === condolenceId);
      if (condolence) {
        condolence.is_approved = false;
        condolence.is_rejected = true;
      }
      successMessage.value = data.message || 'Condolence rejected successfully!';
      setTimeout(() => {
        successMessage.value = '';
      }, 3000);
    } else {
      errorMessage.value = data.error || 'Failed to reject condolence.';
      setTimeout(() => {
        errorMessage.value = '';
      }, 5000);
    }
  } catch (error) {
    console.error('Reject error:', error);
    errorMessage.value = 'Network error. Please try again.';
    setTimeout(() => {
      errorMessage.value = '';
    }, 5000);
  } finally {
    isProcessing.value[condolenceId] = false;
  }
};

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getStatusInfo = (condolence: Condolence) => {
  if (condolence.is_approved) {
    return {
      text: 'Approved',
      class: 'bg-green-100 text-green-800 border-green-200',
      icon: CheckCircleIcon,
    };
  } else if (condolence.is_rejected) {
    return {
      text: 'Rejected',
      class: 'bg-red-100 text-red-800 border-red-200',
      icon: XCircleIcon,
    };
  } else {
    return {
      text: 'Pending Review',
      class: 'bg-yellow-100 text-yellow-800 border-yellow-200',
      icon: ClockIcon,
    };
  }
};

const getStats = () => {
  const total = props.condolences.data.length;
  const approved = props.condolences.data.filter((c) => c.is_approved).length;
  const rejected = props.condolences.data.filter((c) => c.is_rejected).length;
  const pending = props.condolences.data.filter((c) => !c.is_approved && !c.is_rejected).length;

  return { total, approved, rejected, pending };
};

const navigateToPage = (page: number) => {
  window.location.href = `?page=${page}`;
};
</script>

<template>
  <Head :title="`Managing Condolences - ${deceasedName}`" />

  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="border-b bg-white shadow-sm">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <a :href="`/obituary/${obituary.uuid}/manage/dashboard`" class="mr-4 inline-flex items-center text-gray-600 hover:text-gray-900">
              <ArrowLeftIcon class="h-5 w-5" />
            </a>
            <div>
              <h1 class="text-xl font-semibold text-gray-900">Condolence Management</h1>
              <p class="text-sm text-gray-500">{{ deceasedName }}</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <!-- Success Message -->
      <div v-if="successMessage" class="mb-6 rounded-md border border-green-200 bg-green-50 p-4">
        <div class="flex">
          <CheckCircleIcon class="h-5 w-5 flex-shrink-0 text-green-400" />
          <div class="ml-3">
            <p class="text-sm text-green-800">{{ successMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mb-6 rounded-md border border-red-200 bg-red-50 p-4">
        <div class="flex">
          <XCircleIcon class="h-5 w-5 flex-shrink-0 text-red-400" />
          <div class="ml-3">
            <p class="text-sm text-red-800">{{ errorMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Stats Summary -->
      <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white shadow">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                  <span class="text-sm font-semibold text-blue-600">{{ getStats().total }}</span>
                </div>
              </div>
              <div class="ml-5">
                <p class="text-sm font-medium text-gray-500">Total</p>
              </div>
            </div>
          </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                  <span class="text-sm font-semibold text-green-600">{{ getStats().approved }}</span>
                </div>
              </div>
              <div class="ml-5">
                <p class="text-sm font-medium text-gray-500">Approved</p>
              </div>
            </div>
          </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100">
                  <span class="text-sm font-semibold text-yellow-600">{{ getStats().pending }}</span>
                </div>
              </div>
              <div class="ml-5">
                <p class="text-sm font-medium text-gray-500">Pending</p>
              </div>
            </div>
          </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                  <span class="text-sm font-semibold text-red-600">{{ getStats().rejected }}</span>
                </div>
              </div>
              <div class="ml-5">
                <p class="text-sm font-medium text-gray-500">Rejected</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Condolences List -->
      <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="border-b border-gray-200 px-6 py-4">
          <h3 class="text-lg font-medium text-gray-900">All Condolences</h3>
          <p class="text-sm text-gray-500">Showing {{ condolences.from }} to {{ condolences.to }} of {{ condolences.total }} condolences</p>
        </div>

        <div v-if="condolences.data.length === 0" class="px-6 py-12 text-center">
          <ClockIcon class="mx-auto mb-4 h-12 w-12 text-gray-300" />
          <h3 class="mb-2 text-lg font-medium text-gray-900">No condolences yet</h3>
          <p class="text-gray-500">When people submit condolences, they will appear here for your review.</p>
        </div>

        <div v-else class="divide-y divide-gray-200">
          <div v-for="condolence in condolences.data" :key="condolence.id" class="px-6 py-6">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <!-- Header -->
                <div class="mb-3 flex items-center space-x-3">
                  <h4 class="text-lg font-medium text-gray-900">{{ condolence.visitor_name }}</h4>
                  <div :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium', getStatusInfo(condolence).class]">
                    <component :is="getStatusInfo(condolence).icon" class="mr-1 h-3 w-3" />
                    {{ getStatusInfo(condolence).text }}
                  </div>
                </div>

                <!-- Contact Info -->
                <div class="mb-3 flex flex-wrap gap-4 text-sm text-gray-600">
                  <span v-if="condolence.visitor_email"> 📧 {{ condolence.visitor_email }} </span>
                  <span v-if="condolence.visitor_phone"> 📞 {{ condolence.visitor_phone }} </span>
                  <span v-if="condolence.relationship"> 👥 {{ condolence.relationship }} </span>
                  <span class="text-gray-500"> 🕒 {{ formatDate(condolence.created_at) }} </span>
                </div>

                <!-- Message -->
                <div class="mb-4 rounded-lg bg-gray-50 p-4">
                  <p class="leading-relaxed text-gray-800">{{ condolence.message }}</p>
                </div>

                <!-- Actions -->
                <div v-if="!condolence.is_approved && !condolence.is_rejected" class="flex space-x-3">
                  <button
                    @click="approveCondolence(condolence.id)"
                    :disabled="isProcessing[condolence.id]"
                    class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    <CheckCircleIcon class="mr-1 h-4 w-4" />
                    {{ isProcessing[condolence.id] ? 'Approving...' : 'Approve' }}
                  </button>
                  <button
                    @click="rejectCondolence(condolence.id)"
                    :disabled="isProcessing[condolence.id]"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    <XCircleIcon class="mr-1 h-4 w-4" />
                    {{ isProcessing[condolence.id] ? 'Rejecting...' : 'Reject' }}
                  </button>
                </div>

                <div v-else class="text-sm text-gray-500">
                  {{
                    condolence.is_approved
                      ? '✅ This condolence has been approved and is visible to the public.'
                      : '❌ This condolence has been rejected and is not visible to the public.'
                  }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination (if needed) -->
        <div v-if="condolences.last_page > 1" class="border-t border-gray-200 bg-gray-50 px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">Showing {{ condolences.from }} to {{ condolences.to }} of {{ condolences.total }} results</div>
            <div class="flex space-x-2">
              <button
                v-if="condolences.current_page > 1"
                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50"
                @click="navigateToPage(condolences.current_page - 1)"
              >
                Previous
              </button>
              <button
                v-if="condolences.current_page < condolences.last_page"
                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50"
                @click="navigateToPage(condolences.current_page + 1)"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
