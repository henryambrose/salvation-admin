<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { computed } from 'vue';

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
});

const isEditing = computed(() => !!props.user);

const form = useForm({
  name: props.user?.name || '',
  email: props.user?.email || '',
  password: '',
});

const breadcrumbs = [
  { title: 'Users', href: '/users' },
  { title: isEditing.value ? 'Edit User' : 'Create User', href: '#' },
];

function submit() {
  if (isEditing.value) {
    form.put(`/users/${props.user.id}`, {
      onSuccess: () => {
        // Redirect to users index
      },
    });
  } else {
    form.post('/users', {
      onSuccess: () => {
        // Redirect to users index
      },
    });
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="isEditing ? 'Edit User' : 'Create User'" />
    
    <div class="mx-auto max-w-2xl">
      <div class="rounded-2xl bg-[#ffffff] p-6 shadow-xl border border-gray-100">
        <h2 class="mb-6 text-2xl font-bold text-gray-900">
          {{ isEditing ? 'Edit User' : 'Create User' }}
        </h2>
        
        <form @submit.prevent="submit">
          <div class="space-y-4">
            <div>
              <Label for="name">Name</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                :class="{ 'border-red-500': form.errors.name }"
              />
              <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">
                {{ form.errors.name }}
              </div>
            </div>
            
            <div>
              <Label for="email">Email</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                :class="{ 'border-red-500': form.errors.email }"
              />
              <div v-if="form.errors.email" class="mt-1 text-sm text-red-500">
                {{ form.errors.email }}
              </div>
            </div>
            
            <div>
              <Label for="password">
                {{ isEditing ? 'Password (leave blank to keep current)' : 'Password' }}
              </Label>
              <Input
                id="password"
                v-model="form.password"
                type="password"
                :class="{ 'border-red-500': form.errors.password }"
              />
              <div v-if="form.errors.password" class="mt-1 text-sm text-red-500">
                {{ form.errors.password }}
              </div>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end space-x-3">
            <Button
              type="button"
              variant="secondary"
              @click="router.visit('/users')"
              class="rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition px-6 py-2"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="form.processing"
              class="rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition px-6 py-2"
            >
              {{ form.processing ? (isEditing ? 'Saving...' : 'Creating...') : (isEditing ? 'Save' : 'Create') }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template> 