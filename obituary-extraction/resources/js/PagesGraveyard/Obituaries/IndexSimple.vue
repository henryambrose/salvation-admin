<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: AppLayout
});

const props = defineProps<{
  obituaries?: any;
  filters?: any;
}>();
</script>

<template>
  <Head title="Obituary Management" />

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-4 lg:px-6">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
          <h1 class="text-2xl font-bold text-gray-900">Obituary Management</h1>
          <p class="mt-2 text-gray-600">Manage all obituary pages and memorial content</p>
          
          <div class="mt-6">
            <p v-if="!obituaries?.data?.length" class="text-gray-500">
              No obituaries found. The obituary system is set up and ready to use!
            </p>
            <div v-else>
              <p class="text-green-600">
                Found {{ obituaries.data.length }} obituaries.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>