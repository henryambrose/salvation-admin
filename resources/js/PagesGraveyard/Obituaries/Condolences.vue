<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { CheckCircle, XCircle } from 'lucide-vue-next';
import { permissionHelpers } from '@/composables/permissionHelpers';

defineOptions({
    layout: AppLayout
});

const props = defineProps<{
  condolences?: any;
  filters?: any;
}>();

// Permission checks
const { can } = permissionHelpers();
const canApproveCondolences = can('approve-obituary-condolence');
const canRejectCondolences = can('reject-obituary-condolence');

// Actions
const approveCondolence = (condolenceId: number) => {
  router.patch(`/graveyard/condolences/${condolenceId}/approve`, {}, {
    preserveScroll: true,
  });
};

const rejectCondolence = (condolenceId: number) => {
  router.patch(`/graveyard/condolences/${condolenceId}/reject`, {}, {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Manage Condolences" />

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-4 lg:px-6">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
          <h1 class="text-2xl font-bold text-gray-900">Manage Condolences</h1>
          <p class="mt-2 text-gray-600">Review and moderate obituary condolences</p>
          
          <div class="mt-6">
            <div v-if="!condolences?.data?.length" class="text-center py-8">
              <p class="text-gray-500">No condolences to review</p>
            </div>
            <div v-else>
              <p class="text-green-600">
                Found {{ condolences.data.length }} condolences to review.
              </p>
              <div class="mt-4 space-y-4">
                <div v-for="condolence in condolences.data" :key="condolence.id" class="bg-gray-50 p-4 rounded-lg">
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <h4 class="font-medium">{{ condolence.visitor_name }}</h4>
                      <p class="text-sm text-gray-600 mt-1">{{ condolence.message }}</p>
                      <p class="text-xs text-gray-400 mt-2">For: {{ condolence.obituary_page?.title || 'Unknown Obituary' }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span class="text-xs px-2 py-1 rounded"
                            :class="condolence.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                        {{ condolence.is_approved ? 'Approved' : 'Pending' }}
                      </span>

                      <!-- Action buttons for pending condolences -->
                      <div v-if="!condolence.is_approved && (canApproveCondolences || canRejectCondolences)" class="flex space-x-2">
                        <Button
                          v-if="canApproveCondolences"
                          size="sm"
                          variant="default"
                          @click="approveCondolence(condolence.id)"
                          class="bg-green-600 hover:bg-green-700"
                        >
                          <CheckCircle class="h-3 w-3 mr-1" />
                          Approve
                        </Button>
                        <Button
                          v-if="canRejectCondolences"
                          size="sm"
                          variant="outline"
                          @click="rejectCondolence(condolence.id)"
                          class="border-red-200 text-red-700 hover:bg-red-50"
                        >
                          <XCircle class="h-3 w-3 mr-1" />
                          Reject
                        </Button>
                      </div>

                      <!-- Show message if user has no permissions -->
                      <div v-if="!condolence.is_approved && !canApproveCondolences && !canRejectCondolences" class="text-xs text-gray-500">
                        No permission to moderate
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>