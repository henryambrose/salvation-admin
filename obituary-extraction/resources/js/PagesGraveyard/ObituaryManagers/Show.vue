<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Edit, Eye, EyeOff, Globe, Mail, Shield, Trash2, User } from 'lucide-vue-next';
import { computed } from 'vue';

interface ObituaryManager {
  id: number;
  name: string;
  email: string;
  is_active: boolean;
  access_granted_at: string;
  created_at: string;
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

const externalManagementUrl = computed(() => {
  if (typeof window !== 'undefined') {
    return `${window.location.origin}/obituary/${props.obituaryManager.obituary_page.uuid}/manage/login`;
  }
  return `/obituary/${props.obituaryManager.obituary_page.uuid}/manage/login`;
});

const toggleActive = () => {
  router.post(`/graveyard/obituary-managers/${props.obituaryManager.id}/toggle-active`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      const status = !props.obituaryManager.is_active ? 'activated' : 'deactivated';
      success(`Obituary manager ${status} successfully.`);
      // Refresh the current page to show updated status
      router.reload({ only: ['obituaryManager'] });
    },
    onError: (errors) => {
      console.error('Error toggling obituary manager status:', errors);
      error('Failed to update manager status. Please try again.');
    },
  });
};

const deleteManager = () => {
  if (confirm('Are you sure you want to delete this obituary manager? This action cannot be undone and will revoke all external access to the obituary page.')) {
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

const viewObituaryPage = () => {
  window.open(`/obituary/${props.obituaryManager.obituary_page.uuid}`, '_blank');
};

const viewExternalLogin = () => {
  window.open(`/obituary/${props.obituaryManager.obituary_page.uuid}/manage/login`, '_blank');
};
</script>

<template>
  <Head title="Obituary Manager Details" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-6xl sm:px-4 lg:px-6">
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
                <h1 class="text-2xl font-bold text-gray-900">Obituary Manager Details</h1>
                <p class="text-gray-600">View and manage external access permissions</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Button variant="outline" size="sm" @click="viewObituaryPage">
                <Globe class="mr-2 h-4 w-4" />
                View Obituary Page
              </Button>
              <Button variant="outline" size="sm" @click="viewExternalLogin">
                <Shield class="mr-2 h-4 w-4" />
                External Login
              </Button>
              <Button variant="outline" size="sm" as-child>
                <Link :href="`/graveyard/obituary-managers/${obituaryManager.id}/edit`">
                  <Edit class="mr-2 h-4 w-4" />
                  Edit
                </Link>
              </Button>
            </div>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
          <!-- Manager Information -->
          <div class="lg:col-span-2">
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <User class="h-5 w-5" />
                  <span>Manager Information</span>
                </CardTitle>
                <CardDescription>
                  External access details for {{ deceasedName }}'s obituary page
                </CardDescription>
              </CardHeader>
              <CardContent class="space-y-6">
                <!-- Basic Info -->
                <div class="grid gap-6 md:grid-cols-2">
                  <div>
                    <Label class="text-sm font-medium text-gray-500">Manager Name</Label>
                    <p class="text-lg font-medium">{{ obituaryManager.name }}</p>
                  </div>
                  <div>
                    <Label class="text-sm font-medium text-gray-500">Email Address</Label>
                    <div class="flex items-center space-x-2">
                      <Mail class="h-4 w-4 text-gray-400" />
                      <p class="text-lg">{{ obituaryManager.email }}</p>
                    </div>
                  </div>
                </div>

                <!-- Status -->
                <div>
                  <Label class="text-sm font-medium text-gray-500">Access Status</Label>
                  <div class="mt-2 flex items-center space-x-3">
                    <Badge :class="obituaryManager.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="text-sm">
                      <Eye v-if="obituaryManager.is_active" class="mr-1 h-3 w-3" />
                      <EyeOff v-else class="mr-1 h-3 w-3" />
                      {{ obituaryManager.is_active ? 'Active Access' : 'Access Disabled' }}
                    </Badge>
                  </div>
                  <p v-if="obituaryManager.is_active" class="mt-2 text-sm text-green-700">
                    Manager can log in and manage the obituary page
                  </p>
                  <p v-else class="mt-2 text-sm text-red-700">
                    Manager cannot access the obituary page management interface
                  </p>
                </div>

                <!-- Obituary Page Info -->
                <div>
                  <Label class="text-sm font-medium text-gray-500">Associated Obituary Page</Label>
                  <div class="mt-2 p-4 border rounded-lg bg-gray-50">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="font-medium">{{ deceasedName }}</p>
                        <p class="text-sm text-gray-600">UUID: {{ obituaryManager.obituary_page.uuid }}</p>
                      </div>
                      <Button size="sm" variant="outline" @click="viewObituaryPage">
                        <Globe class="h-3 w-3" />
                      </Button>
                    </div>
                  </div>
                </div>

                <!-- External Login URL -->
                <div>
                  <Label class="text-sm font-medium text-gray-500">External Management URL</Label>
                  <div class="mt-2 p-4 border rounded-lg bg-blue-50">
                    <p class="text-sm text-blue-800 font-mono break-all">
                      {{ externalManagementUrl }}
                    </p>
                    <p class="mt-2 text-sm text-blue-700">
                      Share this URL with the manager for direct access to the management interface
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Quick Actions -->
            <Card>
              <CardHeader>
                <CardTitle>Quick Actions</CardTitle>
              </CardHeader>
              <CardContent class="space-y-3">
                <Button
                  :variant="obituaryManager.is_active ? 'destructive' : 'default'"
                  size="sm"
                  @click="toggleActive"
                  class="w-full"
                >
                  <EyeOff v-if="obituaryManager.is_active" class="mr-2 h-4 w-4" />
                  <Eye v-else class="mr-2 h-4 w-4" />
                  {{ obituaryManager.is_active ? 'Disable Access' : 'Enable Access' }}
                </Button>
                <Button variant="outline" size="sm" @click="viewExternalLogin" class="w-full">
                  <Shield class="mr-2 h-4 w-4" />
                  Test External Login
                </Button>
                <Button variant="outline" size="sm" as-child class="w-full">
                  <Link :href="`/graveyard/obituary-managers/${obituaryManager.id}/edit`">
                    <Edit class="mr-2 h-4 w-4" />
                    Edit Details
                  </Link>
                </Button>
                <Button variant="destructive" size="sm" @click="deleteManager" class="w-full">
                  <Trash2 class="mr-2 h-4 w-4" />
                  Delete Manager
                </Button>
              </CardContent>
            </Card>

            <!-- Access Details -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center space-x-2">
                  <Calendar class="h-4 w-4" />
                  <span>Access Timeline</span>
                </CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div>
                  <Label class="text-sm font-medium text-gray-500">Access Granted</Label>
                  <p class="text-sm">{{ new Date(obituaryManager.access_granted_at).toLocaleDateString() }}</p>
                  <p class="text-xs text-gray-500">{{ new Date(obituaryManager.access_granted_at).toLocaleTimeString() }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-gray-500">Granted By</Label>
                  <p class="text-sm">{{ obituaryManager.access_granted_by.name }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-gray-500">Record Created</Label>
                  <p class="text-sm">{{ new Date(obituaryManager.created_at).toLocaleDateString() }}</p>
                  <p class="text-xs text-gray-500">{{ new Date(obituaryManager.created_at).toLocaleTimeString() }}</p>
                </div>
              </CardContent>
            </Card>

            <!-- Manager Capabilities -->
            <Card>
              <CardHeader>
                <CardTitle>Manager Capabilities</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="text-sm text-gray-600 space-y-2">
                  <div class="flex items-start space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <span>Edit obituary content and biography</span>
                  </div>
                  <div class="flex items-start space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <span>Review and moderate condolences</span>
                  </div>
                  <div class="flex items-start space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <span>Approve or reject public messages</span>
                  </div>
                  <div class="flex items-start space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <span>View engagement statistics</span>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>