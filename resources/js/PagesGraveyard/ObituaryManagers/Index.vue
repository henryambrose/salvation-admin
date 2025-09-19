<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, EyeOff, Plus, Search, Shield, User } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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
    permanent_grave_booking?: {
      booking_reference: string;
      valid_member: {
        first_name: string;
        last_name: string;
      };
    };
    temporary_grave_booking?: {
      booking_reference: string;
      dead_first_name: string;
      dead_last_name: string;
    };
  };
  access_granted_by: {
    id: number;
    name: string;
  };
}

interface Props {
  obituaryManagers?: {
    data: ObituaryManager[];
    links?: any[];
    meta?: any;
  };
}

const props = defineProps<Props>();

const { success, error } = useToast();

// Initialize permission helpers
const { can } = permissionHelpers();

// Permission checks
const canCreateManager = can('create-obituary-manager') ?? true;
const canToggleManager = can('toggle-obituary-manager') ?? true;

const search = ref('');

const performSearch = () => {
  router.get('/graveyard/obituary-managers', {
    search: search.value,
  }, {
    preserveState: true,
    replace: true,
  });
};

// Debounced search function
let searchTimeout: number;

watch(search, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    performSearch();
  }, 500);
});

const getDeceasedName = (obituaryPage: ObituaryManager['obituary_page']): string => {
  if (obituaryPage.permanent_grave_booking) {
    const member = obituaryPage.permanent_grave_booking.valid_member;
    return `${member.first_name} ${member.last_name}`;
  }
  if (obituaryPage.temporary_grave_booking) {
    const booking = obituaryPage.temporary_grave_booking;
    return `${booking.dead_first_name} ${booking.dead_last_name}`;
  }
  return 'Unknown';
};

const getBookingReference = (obituaryPage: ObituaryManager['obituary_page']): string => {
  return obituaryPage.permanent_grave_booking?.booking_reference ||
         obituaryPage.temporary_grave_booking?.booking_reference ||
         'N/A';
};

const toggleActive = (manager: ObituaryManager) => {
  router.post(`/graveyard/obituary-managers/${manager.id}/toggle-active`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      const status = !manager.is_active ? 'activated' : 'deactivated';
      success(`Obituary manager ${status} successfully.`);
      // Refresh the current page to show updated status
      router.reload({ only: ['obituaryManagers'] });
    },
    onError: (errors) => {
      console.error('Error toggling obituary manager status:', errors);
      error('Failed to update manager status. Please try again.');
    },
  });
};

const viewObituaryPage = (uuid: string) => {
  window.open(`/obituary/${uuid}`, '_blank');
};
</script>

<template>
  <Head title="Obituary Managers" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-4 lg:px-6">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <!-- Header -->
          <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base leading-6 font-semibold text-gray-900">Obituary Managers</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage external access permissions for obituary pages</p>
              </div>
              <Button v-if="canCreateManager" as-child class="bg-purple-600 hover:bg-purple-700">
                <Link href="/graveyard/obituary-managers/create">
                  <Plus class="mr-2 h-4 w-4" />
                  Create Manager
                </Link>
              </Button>
            </div>
          </div>

          <!-- Filters -->
          <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
              <div class="flex flex-1 items-center space-x-4">
                <div class="relative max-w-md flex-1">
                  <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                  <Input v-model="search" placeholder="Search by name, email, or deceased person..." class="pl-10" />
                </div>
              </div>
            </div>
          </div>

          <!-- Managers List -->
          <div class="p-6">
            <div v-if="!obituaryManagers?.data?.length" class="py-12 text-center">
              <Shield class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No obituary managers found</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by creating external access for an obituary page.</p>
              <div v-if="canCreateManager" class="mt-6">
                <Button as-child class="bg-purple-600 hover:bg-purple-700">
                  <Link href="/graveyard/obituary-managers/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Manager
                  </Link>
                </Button>
              </div>
            </div>

            <div v-else class="overflow-hidden rounded-lg border border-gray-200">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Manager</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Obituary Page</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Access Info</th>
                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="manager in obituaryManagers?.data || []" :key="manager.id" class="hover:bg-gray-50">
                    <!-- Manager Info -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                          <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                            <User class="h-4 w-4 text-purple-600" />
                          </div>
                        </div>
                        <div class="ml-3">
                          <div class="text-sm font-medium text-gray-900">{{ manager.name }}</div>
                          <div class="text-sm text-gray-600">{{ manager.email }}</div>
                        </div>
                      </div>
                    </td>

                    <!-- Obituary Page -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ getDeceasedName(manager.obituary_page) }}</div>
                        <div class="text-sm text-gray-600">{{ getBookingReference(manager.obituary_page) }}</div>
                      </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <Badge :class="manager.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        <Eye v-if="manager.is_active" class="mr-1 h-3 w-3" />
                        <EyeOff v-else class="mr-1 h-3 w-3" />
                        {{ manager.is_active ? 'Active' : 'Inactive' }}
                      </Badge>
                    </td>

                    <!-- Access Info -->
                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                      <div class="space-y-1">
                        <div>Granted by: {{ manager.access_granted_by.name }}</div>
                        <div>On: {{ new Date(manager.access_granted_at).toLocaleDateString() }}</div>
                      </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex space-x-2">
                        <Button size="sm" variant="outline" as-child title="View details">
                          <Link :href="`/graveyard/obituary-managers/${manager.id}`">
                            <Eye class="h-3 w-3" />
                          </Link>
                        </Button>
                        <Button size="sm" variant="outline" as-child title="Edit manager">
                          <Link :href="`/graveyard/obituary-managers/${manager.id}/edit`">
                            <User class="h-3 w-3" />
                          </Link>
                        </Button>
                        <Button
                          size="sm"
                          variant="outline"
                          @click="viewObituaryPage(manager.obituary_page.uuid)"
                          title="View obituary page"
                        >
                          <Shield class="h-3 w-3" />
                        </Button>
                        <Button
                          v-if="canToggleManager"
                          size="sm"
                          :variant="manager.is_active ? 'destructive' : 'default'"
                          @click="toggleActive(manager)"
                          :title="manager.is_active ? 'Deactivate manager' : 'Activate manager'"
                        >
                          <EyeOff v-if="manager.is_active" class="h-3 w-3" />
                          <Eye v-else class="h-3 w-3" />
                        </Button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="obituaryManagers?.meta?.last_page && obituaryManagers.meta.last_page > 1" class="mt-6 flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ obituaryManagers?.meta?.from }} to {{ obituaryManagers?.meta?.to }} of {{ obituaryManagers?.meta?.total }} results
              </div>
              <div class="flex space-x-1">
                <Button
                  v-for="link in obituaryManagers?.links || []"
                  :key="link.label"
                  size="sm"
                  :variant="link.active ? 'default' : 'outline'"
                  :disabled="!link.url"
                  @click="link.url && router.visit(link.url)"
                  v-html="link.label"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>