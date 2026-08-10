<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const breadcrumbs = [
  { title: 'Users', href: '/users' },
  { title: 'View User', href: '#' },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="View User" />

    <div class="mx-auto max-w-2xl">
      <div class="rounded-2xl border border-gray-100 bg-[#ffffff] p-6 shadow-xl">
        <div class="mb-6 flex items-center justify-between">
          <h2 class="text-2xl font-bold text-gray-900">User Details</h2>
          <Button
            @click="router.visit(`/users/${user.id}/edit`)"
            class="rounded-full bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
          >
            Edit User
          </Button>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <p class="mt-1 text-sm text-gray-900">{{ user.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <p class="mt-1 text-sm text-gray-900">{{ user.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Created At</label>
            <p class="mt-1 text-sm text-gray-900">{{ new Date(user.created_at).toLocaleDateString() }}</p>
          </div>

          <div v-if="user.updated_at">
            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
            <p class="mt-1 text-sm text-gray-900">{{ new Date(user.updated_at).toLocaleDateString() }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <Button @click="router.visit('/users')" class="rounded-full bg-gray-100 px-6 py-2 text-gray-700 transition hover:bg-gray-200">
            Back to Users
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
