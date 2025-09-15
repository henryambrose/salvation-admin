<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';

defineOptions({
  layout: AppLayout
});

const breadcrumbs = [
  { title: 'Graveyard', href: '#' },
  { title: 'Background Themes', href: route('graveyard.obituary-background-themes.index') },
  { title: 'Create Theme', href: '#' }
];

const form = useForm({
  key: '',
  name: '',
  description: '',
  type: 'color',
  tier: 'basic',
  image_path: '',
  background_color: '#ffffff',
  style_properties: {},
  is_active: true,
  sort_order: 0,
});

// Custom style properties based on type
const customStyle = ref({
  backgroundColor: '#ffffff',
  background: '',
  backgroundImage: '',
  backgroundSize: 'cover',
  backgroundPosition: 'center',
  backgroundRepeat: 'no-repeat',
});

// Watch type changes to reset style properties
watch(() => form.type, (newType) => {
  customStyle.value = {
    backgroundColor: '#ffffff',
    background: '',
    backgroundImage: '',
    backgroundSize: 'cover',
    backgroundPosition: 'center',
    backgroundRepeat: 'no-repeat',
  };

  if (newType === 'gradient') {
    customStyle.value.background = 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)';
  } else if (newType === 'pattern') {
    customStyle.value.backgroundColor = '#f8f9fa';
    customStyle.value.backgroundImage = 'repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.5) 10px, rgba(255,255,255,.5) 20px)';
  }
});

// Watch for style changes and update form
watch(() => customStyle.value, (newStyle) => {
  form.style_properties = { ...newStyle };
}, { deep: true });

// Preview style computed
const previewStyle = computed(() => {
  const style = { ...customStyle.value };

  if (form.type === 'image' && form.image_path) {
    style.backgroundImage = `url('${form.image_path}')`;
  }

  return style;
});

// Generate key from name
watch(() => form.name, (newName) => {
  if (newName && !form.key) {
    form.key = newName.toLowerCase()
      .replace(/[^a-z0-9\s]/g, '')
      .replace(/\s+/g, '_')
      .trim();
  }
});

const submit = () => {
  form.post(route('graveyard.obituary-background-themes.store'));
};
</script>

<template>
  <Head title="Create Background Theme" />

  <div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <Link
              :href="route('graveyard.obituary-background-themes.index')"
              class="text-gray-600 hover:text-gray-800"
            >
              <ArrowLeft class="h-5 w-5" />
            </Link>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Create Background Theme</h1>
              <p class="mt-1 text-gray-600">Add a new background theme for obituary pages</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Basic Info -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>

                <div>
                  <Label for="name">Theme Name *</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    placeholder="e.g., Memorial Sunset"
                  />
                  <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
                  </div>
                </div>

                <div>
                  <Label for="key">Theme Key *</Label>
                  <Input
                    id="key"
                    v-model="form.key"
                    type="text"
                    required
                    placeholder="e.g., memorial_sunset"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Unique identifier for the theme (auto-generated from name)
                  </p>
                  <div v-if="form.errors.key" class="mt-1 text-sm text-red-600">
                    {{ form.errors.key }}
                  </div>
                </div>

                <div>
                  <Label for="description">Description</Label>
                  <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Brief description of the theme"
                    rows="3"
                  />
                  <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                    {{ form.errors.description }}
                  </div>
                </div>
              </div>

              <!-- Theme Configuration -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Theme Configuration</h3>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <Label for="type">Type *</Label>
                    <select
                      id="type"
                      v-model="form.type"
                      class="w-full rounded-md border-gray-300"
                      required
                    >
                      <option value="color">Solid Color</option>
                      <option value="gradient">Gradient</option>
                      <option value="pattern">Pattern</option>
                      <option value="image">Image</option>
                    </select>
                    <div v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                      {{ form.errors.type }}
                    </div>
                  </div>

                  <div>
                    <Label for="tier">Tier *</Label>
                    <select
                      id="tier"
                      v-model="form.tier"
                      class="w-full rounded-md border-gray-300"
                      required
                    >
                      <option value="basic">Basic</option>
                      <option value="premium">Premium</option>
                    </select>
                    <div v-if="form.errors.tier" class="mt-1 text-sm text-red-600">
                      {{ form.errors.tier }}
                    </div>
                  </div>
                </div>

                <!-- Type-specific fields -->
                <div v-if="form.type === 'color'" class="space-y-4">
                  <div>
                    <Label for="background_color">Background Color</Label>
                    <div class="flex gap-3">
                      <Input
                        id="background_color"
                        v-model="customStyle.backgroundColor"
                        type="color"
                        class="w-16"
                      />
                      <Input
                        v-model="customStyle.backgroundColor"
                        type="text"
                        placeholder="#ffffff"
                        class="flex-1"
                      />
                    </div>
                  </div>
                </div>

                <div v-if="form.type === 'gradient'" class="space-y-4">
                  <div>
                    <Label for="gradient">Gradient CSS</Label>
                    <Textarea
                      id="gradient"
                      v-model="customStyle.background"
                      placeholder="linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)"
                      rows="2"
                    />
                  </div>
                </div>

                <div v-if="form.type === 'pattern'" class="space-y-4">
                  <div>
                    <Label for="pattern_bg">Base Color</Label>
                    <div class="flex gap-3">
                      <Input
                        v-model="customStyle.backgroundColor"
                        type="color"
                        class="w-16"
                      />
                      <Input
                        v-model="customStyle.backgroundColor"
                        type="text"
                        placeholder="#f8f9fa"
                        class="flex-1"
                      />
                    </div>
                  </div>
                  <div>
                    <Label for="pattern_image">Pattern CSS</Label>
                    <Textarea
                      id="pattern_image"
                      v-model="customStyle.backgroundImage"
                      placeholder="repeating-linear-gradient(...)"
                      rows="2"
                    />
                  </div>
                </div>

                <div v-if="form.type === 'image'" class="space-y-4">
                  <div>
                    <Label for="image_path">Image Path</Label>
                    <Input
                      id="image_path"
                      v-model="form.image_path"
                      type="text"
                      placeholder="/images/backgrounds/memorial-sunset.png"
                    />
                    <div v-if="form.errors.image_path" class="mt-1 text-sm text-red-600">
                      {{ form.errors.image_path }}
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <Label for="bg_size">Background Size</Label>
                      <select
                        id="bg_size"
                        v-model="customStyle.backgroundSize"
                        class="w-full rounded-md border-gray-300"
                      >
                        <option value="cover">Cover</option>
                        <option value="contain">Contain</option>
                        <option value="100% 100%">Stretch</option>
                        <option value="auto">Auto</option>
                      </select>
                    </div>
                    <div>
                      <Label for="bg_position">Background Position</Label>
                      <select
                        id="bg_position"
                        v-model="customStyle.backgroundPosition"
                        class="w-full rounded-md border-gray-300"
                      >
                        <option value="center">Center</option>
                        <option value="top">Top</option>
                        <option value="bottom">Bottom</option>
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <Label for="fallback_color">Fallback Color</Label>
                    <div class="flex gap-3">
                      <Input
                        v-model="customStyle.backgroundColor"
                        type="color"
                        class="w-16"
                      />
                      <Input
                        v-model="customStyle.backgroundColor"
                        type="text"
                        placeholder="#ffffff"
                        class="flex-1"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Additional Settings -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Additional Settings</h3>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <Label for="sort_order">Sort Order</Label>
                    <Input
                      id="sort_order"
                      v-model="form.sort_order"
                      type="number"
                      min="0"
                      placeholder="0"
                    />
                  </div>
                  <div class="flex items-center">
                    <input
                      id="is_active"
                      v-model="form.is_active"
                      type="checkbox"
                      class="rounded border-gray-300"
                    />
                    <Label for="is_active" class="ml-2">Active</Label>
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex items-center justify-end gap-4 pt-6 border-t">
                <Link
                  :href="route('graveyard.obituary-background-themes.index')"
                  class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                >
                  Cancel
                </Link>
                <Button
                  type="submit"
                  :disabled="form.processing"
                  class="flex items-center gap-2"
                >
                  <Save class="h-4 w-4" />
                  {{ form.processing ? 'Creating...' : 'Create Theme' }}
                </Button>
              </div>
            </form>
          </div>
        </div>

        <!-- Preview -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>

            <div class="space-y-4">
              <!-- Theme Preview -->
              <div class="border-2 border-gray-200 rounded-lg p-4">
                <div
                  class="h-48 rounded-lg border"
                  :style="previewStyle"
                >
                  <div class="h-full flex items-center justify-center">
                    <div class="text-center text-gray-600 bg-white/80 p-4 rounded">
                      <h4 class="font-semibold">{{ form.name || 'Theme Preview' }}</h4>
                      <p class="text-sm">{{ form.description || 'Theme description' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Theme Info -->
              <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                <div class="flex justify-between">
                  <span class="text-sm font-medium">Key:</span>
                  <span class="text-sm text-gray-600">{{ form.key || 'auto-generated' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium">Type:</span>
                  <span class="text-sm text-gray-600 capitalize">{{ form.type }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium">Tier:</span>
                  <span class="text-sm text-gray-600 capitalize">{{ form.tier }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-sm font-medium">Status:</span>
                  <span class="text-sm text-gray-600">{{ form.is_active ? 'Active' : 'Inactive' }}</span>
                </div>
              </div>

              <!-- Style Properties -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium mb-2">Generated CSS Properties:</h4>
                <pre class="text-xs text-gray-600 overflow-auto">{{ JSON.stringify(previewStyle, null, 2) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>