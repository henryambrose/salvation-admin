<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: AppLayout
});

const props = defineProps<{
  condolences?: any;
  filters?: any;
}>();
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
                    <div>
                      <h4 class="font-medium">{{ condolence.visitor_name }}</h4>
                      <p class="text-sm text-gray-600">{{ condolence.message }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded" 
                          :class="condolence.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                      {{ condolence.is_approved ? 'Approved' : 'Pending' }}
                    </span>
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