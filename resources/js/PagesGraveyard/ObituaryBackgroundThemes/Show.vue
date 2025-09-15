<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Trash, Power, Eye } from 'lucide-vue-next';
import { computed } from 'vue';

defineOptions({
  layout: AppLayout
});

const props = defineProps({
  theme: Object,
});

const breadcrumbs = [
  { title: 'Graveyard', href: '#' },
  { title: 'Background Themes', href: route('graveyard.obituary-background-themes.index') },
  { title: props.theme?.name, href: '#' }
];

// Preview style computed
const previewStyle = computed(() => {
  const style = { ...props.theme?.style_properties };

  if (props.theme?.type === 'image' && props.theme?.image_path) {
    style.backgroundImage = `url('${props.theme.image_path}')`;
  }

  return style;
});

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

// Toggle theme status
const toggleThemeStatus = () => {
  router.patch(route('graveyard.obituary-background-themes.toggle-status', props.theme?.id), {}, {
    preserveScroll: true,
  });
};

// Delete theme
const deleteTheme = () => {
  if (confirm('Are you sure you want to delete this theme?')) {
    router.delete(route('graveyard.obituary-background-themes.destroy', props.theme?.id));
  }
};
</script>

<template>
  <Head :title="`${theme?.name} - Background Theme`" />

  <div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <Link
                :href="route('graveyard.obituary-background-themes.index')"
                class="text-gray-600 hover:text-gray-800"
              >
                <ArrowLeft class="h-5 w-5" />
              </Link>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ theme?.name }}</h1>
                <p class="mt-1 text-gray-600">{{ theme?.description || 'No description provided' }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <Link
                :href="route('graveyard.obituary-background-themes.edit', theme?.id)"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white shadow transition hover:bg-blue-700"
              >
                <Edit class="h-4 w-4" />
                Edit Theme
              </Link>

              <Button
                @click="toggleThemeStatus"
                :variant="theme?.is_active ? 'default' : 'outline'"
                class="flex items-center gap-2"
              >
                <Power class="h-4 w-4" />
                {{ theme?.is_active ? 'Deactivate' : 'Activate' }}
              </Button>

              <Button
                @click="deleteTheme"
                variant="destructive"
                class="flex items-center gap-2"
              >
                <Trash class="h-4 w-4" />
                Delete
              </Button>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Theme Details -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Theme Details</h3>

            <div class="space-y-6">
              <!-- Basic Information -->
              <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-800">Basic Information</h4>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ theme?.name }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Key</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ theme?.key }}</p>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Description</label>
                  <p class="mt-1 text-sm text-gray-900">{{ theme?.description || 'No description provided' }}</p>
                </div>
              </div>

              <!-- Configuration -->
              <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-800">Configuration</h4>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <Badge :class="getTypeBadgeColor(theme?.type)" class="mt-1">
                      {{ theme?.type }}
                    </Badge>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Tier</label>
                    <Badge :class="getTierBadgeColor(theme?.tier)" class="mt-1">
                      {{ theme?.tier }}
                    </Badge>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <Badge :class="getStatusBadgeColor(theme?.is_active)" class="mt-1">
                      {{ theme?.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                    <p class="mt-1 text-sm text-gray-900">{{ theme?.sort_order || 0 }}</p>
                  </div>
                </div>

                <div v-if="theme?.image_path">
                  <label class="block text-sm font-medium text-gray-700">Image Path</label>
                  <p class="mt-1 text-sm text-gray-900 font-mono">{{ theme.image_path }}</p>
                </div>
              </div>

              <!-- Style Properties -->
              <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-800">Style Properties</h4>

                <div class="bg-gray-50 rounded-lg p-4">
                  <pre class="text-xs text-gray-600 overflow-auto">{{ JSON.stringify(theme?.style_properties, null, 2) }}</pre>
                </div>
              </div>

              <!-- Timestamps -->
              <div class="space-y-4 pt-4 border-t">
                <h4 class="text-md font-medium text-gray-800">Timestamps</h4>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ new Date(theme?.created_at).toLocaleString() }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ new Date(theme?.updated_at).toLocaleString() }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Preview -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Live Preview</h3>

            <div class="space-y-6">
              <!-- Large Preview -->
              <div class="border-2 border-gray-200 rounded-lg p-4">
                <div
                  class="h-64 rounded-lg border"
                  :style="previewStyle"
                >
                  <div class="h-full flex items-center justify-center">
                    <div class="text-center text-gray-600 bg-white/80 p-6 rounded-lg backdrop-blur-sm">
                      <h4 class="text-xl font-semibold">{{ theme?.name }}</h4>
                      <p class="text-sm mt-2">{{ theme?.description || 'Theme description' }}</p>
                      <div class="mt-3 flex justify-center gap-2">
                        <Badge :class="getTierBadgeColor(theme?.tier)">
                          {{ theme?.tier }}
                        </Badge>
                        <Badge :class="getTypeBadgeColor(theme?.type)">
                          {{ theme?.type }}
                        </Badge>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Small Previews -->
              <div class="grid grid-cols-2 gap-4">
                <!-- Mobile Preview -->
                <div class="border border-gray-200 rounded-lg p-3">
                  <h5 class="text-sm font-medium text-gray-700 mb-2">Mobile View</h5>
                  <div
                    class="h-32 rounded border"
                    :style="previewStyle"
                  >
                    <div class="h-full flex items-center justify-center">
                      <div class="text-center text-gray-600 bg-white/80 p-2 rounded text-xs">
                        <div class="font-semibold">{{ theme?.name }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tablet Preview -->
                <div class="border border-gray-200 rounded-lg p-3">
                  <h5 class="text-sm font-medium text-gray-700 mb-2">Tablet View</h5>
                  <div
                    class="h-32 rounded border"
                    :style="previewStyle"
                  >
                    <div class="h-full flex items-center justify-center">
                      <div class="text-center text-gray-600 bg-white/80 p-3 rounded text-xs">
                        <div class="font-semibold">{{ theme?.name }}</div>
                        <div class="text-xs mt-1">{{ theme?.type }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Usage Info -->
              <div class="bg-blue-50 rounded-lg p-4">
                <h5 class="text-sm font-medium text-blue-900 mb-2">Usage Information</h5>
                <p class="text-sm text-blue-800">
                  This theme can be used on obituary pages and is available to {{ theme?.tier }} tier users.
                  {{ theme?.is_active ? 'It is currently active and available for selection.' : 'It is currently inactive and not available for selection.' }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>