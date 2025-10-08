<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, User } from 'lucide-vue-next';

interface ObituaryPage {
  id: number;
  uuid: string;
  deceased_name: string;
}

interface Props {
  obituaryPages: ObituaryPage[];
}

const props = defineProps<Props>();

const { success, error } = useToast();

const form = useForm({
  obituary_page_id: '',
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post('/graveyard/obituary-managers', {
    onSuccess: () => {
      success('Obituary manager created successfully!');
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
      error('Please check the form for errors and try again.');
    },
  });
};
</script>

<template>
  <Head title="Create Obituary Manager" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-4 lg:px-6">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center space-x-4">
            <Button variant="outline" size="sm" as-child>
              <Link href="/graveyard/obituary-managers">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Back to Managers
              </Link>
            </Button>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Create Obituary Manager</h1>
              <p class="text-gray-600">Grant external access to an obituary page</p>
            </div>
          </div>
        </div>

        <!-- Form -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center space-x-2">
              <User class="h-5 w-5" />
              <span>Manager Details</span>
            </CardTitle>
            <CardDescription>
              Create credentials for external access to manage an obituary page. The manager will be able to edit content and moderate condolences.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Obituary Page Selection -->
              <div class="space-y-2">
                <Label for="obituary_page_id">Obituary Page *</Label>
                <select
                  id="obituary_page_id"
                  v-model="form.obituary_page_id"
                  required
                  class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus:ring-ring flex h-9 w-full items-center justify-between rounded-md border px-3 py-2 text-sm shadow-sm focus:ring-1 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                  :class="form.errors.obituary_page_id ? 'border-red-500' : ''"
                >
                  <option value="">Select an obituary page</option>
                  <option
                    v-for="obituary in obituaryPages"
                    :key="obituary.id"
                    :value="obituary.id"
                  >
                    {{ obituary.deceased_name }} ({{ obituary.uuid }})
                  </option>
                </select>
                <div v-if="form.errors.obituary_page_id" class="text-sm text-red-600">
                  {{ form.errors.obituary_page_id }}
                </div>
                <p class="text-sm text-gray-500">
                  Only obituary pages without existing managers are shown
                </p>
              </div>

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
                <p class="text-sm text-gray-500">
                  This email will be used for login to the external management interface
                </p>
              </div>

              <!-- Password -->
              <div class="space-y-2">
                <Label for="password">Password *</Label>
                <Input
                  id="password"
                  v-model="form.password"
                  type="password"
                  placeholder="Enter password (minimum 8 characters)"
                  required
                  :class="form.errors.password ? 'border-red-500' : ''"
                />
                <div v-if="form.errors.password" class="text-sm text-red-600">
                  {{ form.errors.password }}
                </div>
              </div>

              <!-- Confirm Password -->
              <div class="space-y-2">
                <Label for="password_confirmation">Confirm Password *</Label>
                <Input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  placeholder="Confirm password"
                  required
                  :class="form.errors.password_confirmation ? 'border-red-500' : ''"
                />
                <div v-if="form.errors.password_confirmation" class="text-sm text-red-600">
                  {{ form.errors.password_confirmation }}
                </div>
              </div>

              <!-- Information Box -->
              <div class="rounded-md bg-blue-50 p-4">
                <div class="flex">
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                      What can obituary managers do?
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                      <ul class="list-disc space-y-1 pl-5">
                        <li>Edit obituary content (biography, memories, achievements, etc.)</li>
                        <li>Review and moderate condolence messages</li>
                        <li>Approve or reject public condolences</li>
                        <li>View obituary statistics and engagement metrics</li>
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
                  {{ form.processing ? 'Creating...' : 'Create Manager' }}
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>