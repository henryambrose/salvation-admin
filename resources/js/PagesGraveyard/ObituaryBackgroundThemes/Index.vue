<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash, Eye, Power, Search, Filter } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

defineOptions({
  layout: AppLayout
});

const props = defineProps({
  themes: Object,
  filters: Object,
  stats: Object,
});

const breadcrumbs = [
  { title: 'Graveyard', href: '#' },
  { title: 'Background Themes', href: route('graveyard.obituary-background-themes.index') }
];

// Search and filters
const searchForm = useForm({
  search: props.filters?.search || '',
  tier: props.filters?.tier || '',
  type: props.filters?.type || '',
  status: props.filters?.status || '',
});

const showFilters = ref(false);

// Watch for form changes and submit
watch(() => searchForm.data(), () => {
  searchForm.get(route('graveyard.obituary-background-themes.index'), {
    preserveState: true,
    preserveScroll: true,
  });
}, { deep: true });

// Clear filters
const clearFilters = () => {
  searchForm.reset();
};

// Toggle theme status
const toggleThemeStatus = (themeId: number) => {
  router.patch(route('graveyard.obituary-background-themes.toggle-status', themeId), {}, {
    preserveScroll: true,
  });
};

// Delete theme
const deleteTheme = (themeId: number) => {
  if (confirm('Are you sure you want to delete this theme?')) {
    router.delete(route('graveyard.obituary-background-themes.destroy', themeId), {
      preserveScroll: true,
    });
  }
};

// Get tier badge color
const getTierBadgeColor = (tier: string) => {
  return tier === 'premium' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800';
};

// Get status badge color
const getStatusBadgeColor = (isActive: boolean) => {
  return isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
};

// Get type badge color
const getTypeBadgeColor = (type: string) => {
  const colors = {
    'color': 'bg-purple-100 text-purple-800',
    'gradient': 'bg-pink-100 text-pink-800',
    'pattern': 'bg-indigo-100 text-indigo-800',
    'image': 'bg-emerald-100 text-emerald-800',
  };
  return colors[type] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
  <Head title="Background Themes Management" />

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Background Themes</h1>
              <p class="mt-2 text-gray-600">Manage obituary background themes and styles</p>
            </div>
            <Link
              :href="route('graveyard.obituary-background-themes.create')"
              class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
            >
              <Plus class="h-4 w-4" />
              Add New Theme
            </Link>
          </div>

          <!-- Stats -->
          <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-gray-900">{{ stats?.total || 0 }}</div>
              <div class="text-sm text-gray-600">Total Themes</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-green-900">{{ stats?.active || 0 }}</div>
              <div class="text-sm text-green-600">Active Themes</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-blue-900">{{ stats?.basic || 0 }}</div>
              <div class="text-sm text-blue-600">Basic Themes</div>
            </div>
            <div class="bg-amber-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-amber-900">{{ stats?.premium || 0 }}</div>
              <div class="text-sm text-amber-600">Premium Themes</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <div class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
              <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                <Input
                  v-model="searchForm.search"
                  type="text"
                  placeholder="Search themes by name, description, or key..."
                  class="pl-10"
                />
              </div>
            </div>

            <!-- Filter Toggle -->
            <Button
              variant="outline"
              @click="showFilters = !showFilters"
              class="flex items-center gap-2"
            >
              <Filter class="h-4 w-4" />
              Filters
            </Button>

            <!-- Clear Filters -->
            <Button
              v-if="filters?.search || filters?.tier || filters?.type || filters?.status"
              variant="outline"
              @click="clearFilters"
            >
              Clear
            </Button>
          </div>

          <!-- Expanded Filters -->
          <div v-if="showFilters" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
              <select v-model="searchForm.tier" class="w-full rounded-md border-gray-300">
                <option value="">All Tiers</option>
                <option value="basic">Basic</option>
                <option value="premium">Premium</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
              <select v-model="searchForm.type" class="w-full rounded-md border-gray-300">
                <option value="">All Types</option>
                <option value="color">Color</option>
                <option value="gradient">Gradient</option>
                <option value="pattern">Pattern</option>
                <option value="image">Image</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select v-model="searchForm.status" class="w-full rounded-md border-gray-300">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Themes List -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
          <div v-if="!themes?.data?.length" class="text-center py-8">
            <div class="text-gray-500">No background themes found</div>
            <Link
              :href="route('graveyard.obituary-background-themes.create')"
              class="mt-4 inline-flex items-center gap-2 text-blue-600 hover:text-blue-800"
            >
              <Plus class="h-4 w-4" />
              Create your first theme
            </Link>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="theme in themes.data"
              :key="theme.id"
              class="border rounded-lg p-4 hover:shadow-md transition-shadow"
            >
              <!-- Theme Preview -->
              <div
                class="h-24 rounded-lg mb-4 border-2 border-gray-200"
                :style="theme.style_properties || { backgroundColor: theme.background_color || '#ffffff' }"
              >
                <div v-if="theme.image_path" class="h-full w-full rounded-lg bg-cover bg-center"
                     :style="{ backgroundImage: `url('${theme.image_path}')` }">
                </div>
              </div>

              <!-- Theme Info -->
              <div class="space-y-3">
                <div>
                  <h3 class="font-semibold text-gray-900">{{ theme.name }}</h3>
                  <p class="text-sm text-gray-600">{{ theme.description }}</p>
                  <p class="text-xs text-gray-500 mt-1">Key: {{ theme.key }}</p>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap gap-2">
                  <Badge :class="getTierBadgeColor(theme.tier)">
                    {{ theme.tier }}
                  </Badge>
                  <Badge :class="getTypeBadgeColor(theme.type)">
                    {{ theme.type }}
                  </Badge>
                  <Badge :class="getStatusBadgeColor(theme.is_active)">
                    {{ theme.is_active ? 'Active' : 'Inactive' }}
                  </Badge>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-3 border-t">
                  <div class="flex gap-2">
                    <Link
                      :href="route('graveyard.obituary-background-themes.show', theme.id)"
                      class="text-blue-600 hover:text-blue-800"
                      title="View"
                    >
                      <Eye class="h-4 w-4" />
                    </Link>
                    <Link
                      :href="route('graveyard.obituary-background-themes.edit', theme.id)"
                      class="text-indigo-600 hover:text-indigo-800"
                      title="Edit"
                    >
                      <Pencil class="h-4 w-4" />
                    </Link>
                    <button
                      @click="deleteTheme(theme.id)"
                      class="text-red-600 hover:text-red-800"
                      title="Delete"
                    >
                      <Trash class="h-4 w-4" />
                    </button>
                  </div>

                  <button
                    @click="toggleThemeStatus(theme.id)"
                    :class="[
                      'p-1 rounded',
                      theme.is_active
                        ? 'text-green-600 hover:bg-green-50'
                        : 'text-gray-400 hover:bg-gray-50'
                    ]"
                    :title="theme.is_active ? 'Deactivate' : 'Activate'"
                  >
                    <Power class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="themes?.links && themes.links.length > 3" class="mt-6">
            <nav class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <span class="text-sm text-gray-700">
                  Showing {{ themes.from }} to {{ themes.to }} of {{ themes.total }} themes
                </span>
              </div>

              <div class="flex items-center gap-1">
                <template v-for="link in themes.links" :key="link.label">
                  <Link
                    v-if="link.url"
                    :href="link.url"
                    :class="[
                      'px-3 py-2 text-sm rounded-md',
                      link.active
                        ? 'bg-blue-600 text-white'
                        : 'text-gray-700 hover:bg-gray-100'
                    ]"
                    v-html="link.label"
                  />
                  <span
                    v-else
                    class="px-3 py-2 text-sm text-gray-400"
                    v-html="link.label"
                  />
                </template>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>