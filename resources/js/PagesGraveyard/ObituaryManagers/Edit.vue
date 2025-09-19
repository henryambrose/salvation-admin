<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Eye, Save, Trash2, User } from 'lucide-vue-next';

interface ObituaryManager {
  id: number;
  name: string;
  email: string;
  is_active: boolean;
  access_granted_at: string;
  obituary_page: {
    id: number;
    uuid: string;
  };
  access_granted_by: {
    id: number;
    name: string;
  };
}

interface Props {
  obituaryManager: ObituaryManager;
  deceasedName: string;
}

const props = defineProps<Props>();

const { success, error } = useToast();

const form = useForm({
  name: props.obituaryManager.name,
  email: props.obituaryManager.email,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.put(`/graveyard/obituary-managers/${props.obituaryManager.id}`, {
    onSuccess: () => {
      success('Obituary manager updated successfully!');
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
      error('Please check the form for errors and try again.');
    },
  });
};

const deleteManager = () => {
  if (confirm('Are you sure you want to delete this obituary manager? This action cannot be undone.')) {
    router.delete(`/graveyard/obituary-managers/${props.obituaryManager.id}`, {
      onSuccess: () => {
        success('Obituary manager deleted successfully!');
      },
      onError: (errors) => {
        console.error('Delete error:', errors);
        error('Failed to delete obituary manager. Please try again.');
      },
    });
  }
};
</script>

<template>
  <Head title="Edit Obituary Manager" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-4 lg:px-6">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <Button variant="outline" size="sm" as-child>
                <Link href="/graveyard/obituary-managers">
                  <ArrowLeft class="mr-2 h-4 w-4" />
                  Back to Managers
                </Link>
              </Button>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Obituary Manager</h1>
                <p class="text-gray-600">Update manager details and permissions</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Button variant="outline" size="sm" as-child>
                <Link :href="`/graveyard/obituary-managers/${obituaryManager.id}`">
                  <Eye class="mr-2 h-4 w-4" />
                  View Details
                </Link>
              </Button>
              <Button variant="destructive" size="sm" @click="deleteManager">
                <Trash2 class="mr-2 h-4 w-4" />
                Delete
              </Button>
            </div>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
          <!-- Manager Info -->
          <div class="lg:col-span-1">
            <Card>
              <CardHeader>
                <CardTitle>Manager Information</CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div>
                  <Label class="text-sm font-medium text-gray-500">Obituary Page</Label>
                  <p class="text-sm font-medium">{{ deceasedName }}</p>
                  <p class="text-xs text-gray-500">{{ obituaryManager.obituary_page.uuid }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-gray-500">Status</Label>
                  <div class="mt-1">
                    <Badge :class="obituaryManager.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                      {{ obituaryManager.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                  </div>
                </div>
                <div>
                  <Label class="text-sm font-medium text-gray-500">Access Granted By</Label>
                  <p class="text-sm">{{ obituaryManager.access_granted_by.name }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-gray-500">Access Granted On</Label>
                  <p class="text-sm">{{ new Date(obituaryManager.access_granted_at).toLocaleDateString() }}</p>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Edit Form -->
          <div class="lg:col-span-2">
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <User class="h-5 w-5" />
                  <span>Edit Manager Details</span>
                </CardTitle>
                <CardDescription>
                  Update the manager's credentials and contact information.
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                  <!-- Manager Name -->
                  <div class="space-y-2">
                    <Label for="name">Manager Name *</Label>
                    <Input
                      id="name"
                      v-model="form.name"
                      type="text"
                      placeholder="Enter manager's full name"
                      required
                      :class="form.errors.name ? 'border-red-500' : ''"
                    />
                    <div v-if="form.errors.name" class="text-sm text-red-600">
                      {{ form.errors.name }}
                    </div>
                  </div>

                  <!-- Email -->
                  <div class="space-y-2">
                    <Label for="email">Email Address *</Label>
                    <Input
                      id="email"
                      v-model="form.email"
                      type="email"
                      placeholder="Enter email address"
                      required
                      :class="form.errors.email ? 'border-red-500' : ''"
                    />
                    <div v-if="form.errors.email" class="text-sm text-red-600">
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <!-- Password (Optional) -->
                  <div class="space-y-2">
                    <Label for="password">New Password</Label>
                    <Input
                      id="password"
                      v-model="form.password"
                      type="password"
                      placeholder="Enter new password (leave blank to keep current)"
                      :class="form.errors.password ? 'border-red-500' : ''"
                    />
                    <div v-if="form.errors.password" class="text-sm text-red-600">
                      {{ form.errors.password }}
                    </div>
                    <p class="text-sm text-gray-500">
                      Leave blank to keep the current password
                    </p>
                  </div>

                  <!-- Confirm Password -->
                  <div v-if="form.password" class="space-y-2">
                    <Label for="password_confirmation">Confirm New Password *</Label>
                    <Input
                      id="password_confirmation"
                      v-model="form.password_confirmation"
                      type="password"
                      placeholder="Confirm new password"
                      :class="form.errors.password_confirmation ? 'border-red-500' : ''"
                    />
                    <div v-if="form.errors.password_confirmation" class="text-sm text-red-600">
                      {{ form.errors.password_confirmation }}
                    </div>
                  </div>

                  <!-- Warning Box -->
                  <div class="rounded-md bg-yellow-50 p-4">
                    <div class="flex">
                      <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                          Important Notice
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                          <ul class="list-disc space-y-1 pl-5">
                            <li>Changing email or password will require the manager to use new credentials for login</li>
                            <li>The manager will need to be notified of any credential changes</li>
                            <li>Use the "Toggle Active" button in the managers list to disable/enable access</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Submit Button -->
                  <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <Button variant="outline" as-child>
                      <Link href="/graveyard/obituary-managers">Cancel</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing" class="bg-purple-600 hover:bg-purple-700">
                      <Save class="mr-2 h-4 w-4" />
                      {{ form.processing ? 'Updating...' : 'Update Manager' }}
                    </Button>
                  </div>
                </form>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>