<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="'Obituary Plan: ' + plan.name" />

    <div class="mx-auto max-w-4xl">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-blue-700">{{ plan.name }}</h2>
          <p class="text-gray-600">Obituary plan details and usage statistics</p>
        </div>
        <div class="flex gap-3">
          <Button
            v-if="can.update"
            @click="router.visit('/graveyard/obituary-plans/' + plan.id + '/edit')"
            class="flex items-center gap-2 rounded-full bg-yellow-600 px-4 py-2 text-white shadow transition hover:bg-yellow-700"
          >
            <Pencil class="h-[1rem] w-[1rem]" />
            <span>Edit</span>
          </Button>
          <Button
            @click="router.visit('/graveyard/obituary-plans')"
            variant="secondary"
            class="rounded-full bg-gray-100 px-4 py-2 text-gray-700 transition hover:bg-gray-200"
          >
            Back to List
          </Button>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Plan Details -->
        <div class="lg:col-span-2">
          <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Plan Details</h3>

            <dl class="space-y-4">
              <div>
                <dt class="text-sm font-medium text-gray-500">Name</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ plan.name }}</dd>
              </div>

              <div v-if="plan.description">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ plan.description }}</dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Duration</dt>
                <dd class="mt-1">
                  <span
                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                    :class="plan.duration_in_days ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                  >
                    {{ plan.formatted_duration }}
                  </span>
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Cost</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ plan.formatted_cost }}</dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                  <span
                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                    :class="plan.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ plan.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ plan.sort_order }}</dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Created</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  {{ formatDate(plan.created_at) }}
                </dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                <dd class="mt-1 text-sm text-gray-900">
                  {{ formatDate(plan.updated_at) }}
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Usage Statistics -->
        <div>
          <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Usage Statistics</h3>

            <div class="space-y-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">
                  {{ plan.obituary_pages_count || 0 }}
                </div>
                <div class="text-sm text-gray-500">Active Obituary Pages</div>
              </div>

              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">
                  {{ plan.obituary_payments_count || 0 }}
                </div>
                <div class="text-sm text-gray-500">Total Payments</div>
              </div>

              <div v-if="plan.obituary_pages_count > 0 || plan.obituary_payments_count > 0" class="pt-4 border-t">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                  <div class="flex">
                    <AlertTriangle class="h-5 w-5 text-yellow-400" />
                    <div class="ml-3">
                      <h3 class="text-sm font-medium text-yellow-800">
                        Plan in Use
                      </h3>
                      <div class="mt-1 text-sm text-yellow-700">
                        This plan cannot be deleted because it has active obituary pages or payments.
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="pt-4 border-t">
                <Button
                  v-if="can.delete"
                  @click="showDeleteModal = true"
                  variant="destructive"
                  class="w-full rounded-full bg-red-600 px-4 py-2 text-white shadow transition hover:bg-red-700"
                >
                  <Trash2 class="h-[1rem] w-[1rem] mr-2" />
                  Delete Plan
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <transition name="fade">
      <div v-if="showDeleteModal" class="bg-opacity-20 fixed inset-0 z-50 flex items-center justify-center bg-transparent">
        <div class="bg-opacity-50 absolute inset-0 bg-black" @click="showDeleteModal = false"></div>
        <div class="from-grey-900 via-grey-800 to-grey-600 relative z-10 w-full max-w-[448px] rounded-lg bg-gradient-to-r p-[2px] shadow-lg">
          <div class="rounded-lg bg-[#ffffff] p-6">
            <h3 class="mb-4 text-xl font-semibold">Delete Obituary Plan</h3>
            <p>
              Are you sure you want to delete the plan
              <span class="font-bold">{{ plan.name }}</span>?
              This action cannot be undone.
            </p>
            <div class="mt-6 flex justify-end space-x-2">
              <Button
                variant="secondary"
                type="button"
                @click="showDeleteModal = false"
                class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200"
              >
                Cancel
              </Button>
              <Button
                variant="destructive"
                type="button"
                @click="confirmDelete"
                class="flex items-center gap-2 rounded-full bg-red-600 px-6 py-2 text-white shadow transition hover:bg-red-700"
              >
                Delete
              </Button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </AppLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { AlertTriangle, Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
  plan: any;
  can: any;
}

const props = defineProps<Props>();

// Breadcrumbs
const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Graveyard', href: '/graveyard' },
  { title: 'Obituary Plans', href: '/graveyard/obituary-plans' },
  { title: props.plan.name, href: '/graveyard/obituary-plans/' + props.plan.id },
];

// Delete modal state
const showDeleteModal = ref(false);

// Delete confirmation
const confirmDelete = () => {
  router.delete('/graveyard/obituary-plans/' + props.plan.id, {
    onSuccess: () => {
      // Redirect will be handled by controller
    },
  });
};

// Helper functions
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>