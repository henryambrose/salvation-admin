<template>
  <div class="py-6">
    <div class="mx-auto max-w-7xl">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-blue-700">Edit Grave Category</h1>
            <Link
              :href="route('graveyard.grave-categories.index')"
              class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-900"
            >
              Back to List
            </Link>
          </div>

          <div class="mx-auto max-w-2xl">
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Basic Information Section -->
              <div class="rounded-lg bg-gray-50 p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Category Information</h3>

                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Category Name *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Enter category name"
                    required
                  />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('graveyard.grave-categories.index')"
                  class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="submitting"
                  class="rounded-md bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                >
                  {{ submitting ? 'Updating...' : 'Update Category' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineOptions({
  layout: AppLayout,
});

interface Props {
  graveCategory: any;
  errors?: any;
}

const props = defineProps<Props>();

const submitting = ref(false);

const form = useForm({
  name: '',
});

const initializeForm = () => {
  if (props.graveCategory) {
    form.name = props.graveCategory.name || '';
  }
};

const submitForm = () => {
  // Prevent duplicate submissions
  if (submitting.value) return;
  submitting.value = true;

  form.put(route('graveyard.grave-categories.update', props.graveCategory.id), {
    onSuccess: () => {
      submitting.value = false;
    },
    onError: () => {
      submitting.value = false;
    },
  });
};

onMounted(() => {
  initializeForm();
});
</script>