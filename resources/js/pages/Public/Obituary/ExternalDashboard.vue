<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { PencilIcon, MessageCircleIcon, EyeIcon, QrCodeIcon, TrendingUpIcon, ClockIcon } from 'lucide-vue-next';

interface Props {
  obituary: {
    uuid: string;
    view_count: number;
    qr_scan_count: number;
    service_type: string;
    is_published: boolean;
  };
  deceasedName: string;
  stats: {
    total_condolences: number;
    pending_condolences: number;
    approved_condolences: number;
    rejected_condolences: number;
    total_views: number;
    qr_scans: number;
  };
  recentCondolences: Array<{
    id: number;
    visitor_name: string;
    message: string;
    created_at: string;
    is_approved: boolean;
    is_rejected: boolean;
  }>;
}

const props = defineProps<Props>();

const logout = async () => {
  try {
    await fetch(`/obituary/${props.obituary.uuid}/manage/logout`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });
    window.location.href = `/obituary/${props.obituary.uuid}`;
  } catch (error) {
    console.error('Logout error:', error);
    window.location.href = `/obituary/${props.obituary.uuid}`;
  }
};

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getStatusBadge = (condolence: any) => {
  if (condolence.is_approved) {
    return { text: 'Approved', class: 'bg-green-100 text-green-800' };
  } else if (condolence.is_rejected) {
    return { text: 'Rejected', class: 'bg-red-100 text-red-800' };
  } else {
    return { text: 'Pending', class: 'bg-yellow-100 text-yellow-800' };
  }
};
</script>

<template>
  <Head :title="`Managing ${deceasedName} - Obituary Dashboard`" />

  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div>
            <h1 class="text-xl font-semibold text-gray-900">
              Managing: {{ deceasedName }}
            </h1>
            <p class="text-sm text-gray-500">Obituary Management Dashboard</p>
          </div>
          <div class="flex items-center space-x-4">
            <a
              :href="`/obituary/${obituary.uuid}`"
              target="_blank"
              class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <EyeIcon class="h-4 w-4 mr-2" />
              View Public Page
            </a>
            <button
              @click="logout"
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Views -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <EyeIcon class="h-6 w-6 text-blue-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Views</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_views.toLocaleString() }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- QR Scans -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <QrCodeIcon class="h-6 w-6 text-purple-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">QR Code Scans</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.qr_scans.toLocaleString() }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Condolences -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <MessageCircleIcon class="h-6 w-6 text-green-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Condolences</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.total_condolences }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Condolences -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <ClockIcon class="h-6 w-6 text-yellow-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Pending Review</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.pending_condolences }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Cards -->
      <div v-if="obituary.is_published" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Edit Obituary -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <PencilIcon class="h-8 w-8 text-blue-600" />
              </div>
              <div class="ml-5">
                <h3 class="text-lg font-medium text-gray-900">Edit Obituary Content</h3>
                <p class="text-sm text-gray-500 mt-1">
                  Update biography, memories, achievements, and other personal details.
                </p>
              </div>
            </div>
            <div class="mt-4">
              <a
                :href="`/obituary/${obituary.uuid}/manage/edit`"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Edit Content
              </a>
            </div>
          </div>
        </div>

        <!-- Manage Condolences -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <MessageCircleIcon class="h-8 w-8 text-green-600" />
              </div>
              <div class="ml-5">
                <h3 class="text-lg font-medium text-gray-900">Manage Condolences</h3>
                <p class="text-sm text-gray-500 mt-1">
                  Review, approve, or reject condolence messages from visitors.
                </p>
              </div>
            </div>
            <div class="mt-4 flex items-center space-x-3">
              <a
                :href="`/obituary/${obituary.uuid}/manage/condolences`"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                Manage Condolences
              </a>
              <span v-if="stats.pending_condolences > 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                {{ stats.pending_condolences }} pending
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Unpublished Notice -->
      <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-8 w-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-yellow-800">Obituary Not Yet Published</h3>
            <p class="text-yellow-700 mt-1">
              This obituary page is currently under review and has not been published yet.
              Edit and condolence management features will be available once the page is published by an administrator.
            </p>
          </div>
        </div>
      </div>

      <!-- Recent Condolences -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Recent Condolences</h3>
          <p class="text-sm text-gray-500">Latest messages from visitors</p>
        </div>
        <div class="divide-y divide-gray-200">
          <div v-if="recentCondolences.length === 0" class="px-6 py-8 text-center">
            <MessageCircleIcon class="h-12 w-12 text-gray-300 mx-auto mb-3" />
            <p class="text-gray-500">No condolences received yet</p>
          </div>
          <div v-else v-for="condolence in recentCondolences" :key="condolence.id" class="px-6 py-4">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center space-x-3">
                  <h4 class="text-sm font-medium text-gray-900">{{ condolence.visitor_name }}</h4>
                  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusBadge(condolence).class]">
                    {{ getStatusBadge(condolence).text }}
                  </span>
                </div>
                <p class="text-sm text-gray-700 mt-1 line-clamp-2">{{ condolence.message }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ formatDate(condolence.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
        <div v-if="recentCondolences.length > 0 && obituary.is_published" class="px-6 py-3 bg-gray-50 border-t border-gray-200">
          <a
            :href="`/obituary/${obituary.uuid}/manage/condolences`"
            class="text-sm text-blue-600 hover:text-blue-500 font-medium"
          >
            View all condolences →
          </a>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>