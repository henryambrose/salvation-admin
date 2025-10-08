<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Upload, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

defineOptions({
  layout: AppLayout,
});

const props = defineProps({
  theme: Object,
});

const breadcrumbs = [
  { title: 'Graveyard', href: '#' },
  { title: 'Background Themes', href: route('graveyard.obituary-background-themes.index') },
  { title: `Edit ${props.theme?.name}`, href: '#' },
];

const form = useForm({
  key: props.theme?.key || '',
  name: props.theme?.name || '',
  description: props.theme?.description || '',
  type: props.theme?.type || 'color',
  tier: props.theme?.tier || 'basic',
  image_path: props.theme?.image_path || '',
  background_color: props.theme?.background_color || '#ffffff',
  style_properties: props.theme?.style_properties || {},
  is_active: props.theme?.is_active ?? true,
  sort_order: props.theme?.sort_order || 0,
  image_file: null as File | null,
});

// Image upload state
const imagePreview = ref<string | null>(null);
const imageInputRef = ref<HTMLInputElement>();

// Custom style properties based on type
const customStyle = ref({
  backgroundColor: props.theme?.style_properties?.backgroundColor || props.theme?.background_color || '#ffffff',
  background: props.theme?.style_properties?.background || '',
  backgroundImage: props.theme?.style_properties?.backgroundImage || '',
  backgroundSize: props.theme?.style_properties?.backgroundSize || 'cover',
  backgroundPosition: props.theme?.style_properties?.backgroundPosition || 'center',
  backgroundRepeat: props.theme?.style_properties?.backgroundRepeat || 'no-repeat',
});

// Watch type changes to reset style properties
watch(
  () => form.type,
  (newType) => {
    if (newType === 'color') {
      customStyle.value = {
        backgroundColor: customStyle.value.backgroundColor,
        background: '',
        backgroundImage: '',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
      };
    } else if (newType === 'gradient') {
      customStyle.value.background = customStyle.value.background || 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)';
    } else if (newType === 'pattern') {
      customStyle.value.backgroundColor = customStyle.value.backgroundColor || '#f8f9fa';
      customStyle.value.backgroundImage =
        customStyle.value.backgroundImage ||
        'repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.5) 10px, rgba(255,255,255,.5) 20px)';
    }
  },
);

// Watch for style changes and update form
watch(
  () => customStyle.value,
  (newStyle) => {
    form.style_properties = { ...newStyle };
  },
  { deep: true },
);

// Preview style computed
const previewStyle = computed(() => {
  const style = { ...customStyle.value };

  if (form.type === 'image') {
    if (imagePreview.value) {
      // Use uploaded image preview
      style.backgroundImage = `url('${imagePreview.value}')`;
    } else if (form.image_path) {
      // Use existing image path
      style.backgroundImage = `url('${form.image_path}')`;
    }
  }

  return style;
});

// Image upload functions
const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (file) {
    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('Please select a valid image file.');
      return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('Image size must be less than 5MB.');
      return;
    }

    form.image_file = file;

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = () => {
  form.image_file = null;
  imagePreview.value = null;
  if (imageInputRef.value) {
    imageInputRef.value.value = '';
  }
};

const submit = () => {
  if (form.image_file) {
    // When file upload is involved, we need to transform the form data
    const transformedData = {
      ...form.data(),
      is_active: form.is_active ? 1 : 0,
      _method: 'PUT',
    };

    // Submit with form data transformation
    form
      .transform(() => transformedData)
      .post(route('graveyard.obituary-background-themes.update', props.theme?.id), {
        forceFormData: true,
      });
  } else {
    // Regular form submission
    form.put(route('graveyard.obituary-background-themes.update', props.theme?.id));
  }
};
</script>

<template>
  <Head :title="`Edit ${theme?.name} - Background Theme`" />

  <div class="py-6">
    <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <Link :href="route('graveyard.obituary-background-themes.index')" class="text-gray-600 hover:text-gray-800">
              <ArrowLeft class="h-5 w-5" />
            </Link>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Edit Background Theme</h1>
              <p class="mt-1 text-gray-600">Update the background theme for obituary pages</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Form -->
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Basic Info -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>

                <div>
                  <Label for="name">Theme Name *</Label>
                  <Input id="name" v-model="form.name" type="text" required placeholder="e.g., Memorial Sunset" />
                  <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
                  </div>
                </div>

                <div>
                  <Label for="key">Theme Key *</Label>
                  <Input id="key" v-model="form.key" type="text" required placeholder="e.g., memorial_sunset" />
                  <p class="mt-1 text-xs text-gray-500">Unique identifier for the theme</p>
                  <div v-if="form.errors.key" class="mt-1 text-sm text-red-600">
                    {{ form.errors.key }}
                  </div>
                </div>

                <div>
                  <Label for="description">Description</Label>
                  <Textarea id="description" v-model="form.description" placeholder="Brief description of the theme" :rows="3" />
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
                    <select id="type" v-model="form.type" class="w-full rounded-md border-gray-300" required>
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
                    <select id="tier" v-model="form.tier" class="w-full rounded-md border-gray-300" required>
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
                      <Input id="background_color" v-model="customStyle.backgroundColor" type="color" class="w-16" />
                      <Input v-model="customStyle.backgroundColor" type="text" placeholder="#ffffff" class="flex-1" />
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
                      :rows="2"
                    />
                  </div>
                </div>

                <div v-if="form.type === 'pattern'" class="space-y-4">
                  <div>
                    <Label for="pattern_bg">Base Color</Label>
                    <div class="flex gap-3">
                      <Input v-model="customStyle.backgroundColor" type="color" class="w-16" />
                      <Input v-model="customStyle.backgroundColor" type="text" placeholder="#f8f9fa" class="flex-1" />
                    </div>
                  </div>
                  <div>
                    <Label for="pattern_image">Pattern CSS</Label>
                    <Textarea id="pattern_image" v-model="customStyle.backgroundImage" placeholder="repeating-linear-gradient(...)" :rows="2" />
                  </div>
                </div>

                <div v-if="form.type === 'image'" class="space-y-4">
                  <div>
                    <Label for="image_upload">Upload Background Image</Label>
                    <div class="mt-2">
                      <div v-if="!imagePreview && !form.image_path" class="rounded-lg border-2 border-dashed border-gray-300 p-6 text-center">
                        <Upload class="mx-auto h-12 w-12 text-gray-400" />
                        <div class="mt-4">
                          <label for="image_upload" class="cursor-pointer">
                            <span class="mt-2 block text-sm font-medium text-gray-900"> Click to upload background image </span>
                            <span class="mt-1 block text-xs text-gray-500"> PNG, JPG, GIF up to 5MB </span>
                          </label>
                          <input id="image_upload" ref="imageInputRef" type="file" accept="image/*" class="sr-only" @change="handleImageUpload" />
                        </div>
                      </div>

                      <div v-else class="relative inline-block">
                        <img :src="imagePreview || form.image_path" alt="Background preview" class="h-32 w-48 rounded-lg border object-cover" />
                        <button
                          type="button"
                          @click="removeImage"
                          class="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600"
                        >
                          <X class="h-4 w-4" />
                        </button>
                        <div class="mt-2">
                          <label for="image_upload_replace" class="cursor-pointer text-sm text-blue-600 hover:text-blue-800"> Replace image </label>
                          <input id="image_upload_replace" type="file" accept="image/*" class="sr-only" @change="handleImageUpload" />
                        </div>
                      </div>
                    </div>
                    <div v-if="form.errors.image_file" class="mt-1 text-sm text-red-600">
                      {{ form.errors.image_file }}
                    </div>
                  </div>

                  <div>
                    <Label for="image_path">Or Image Path</Label>
                    <Input id="image_path" v-model="form.image_path" type="text" placeholder="/images/backgrounds/memorial-sunset.png" />
                    <p class="mt-1 text-xs text-gray-500">Alternatively, enter a direct path to an existing image</p>
                    <div v-if="form.errors.image_path" class="mt-1 text-sm text-red-600">
                      {{ form.errors.image_path }}
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <Label for="bg_size">Background Size</Label>
                      <select id="bg_size" v-model="customStyle.backgroundSize" class="w-full rounded-md border-gray-300">
                        <option value="cover">Cover</option>
                        <option value="contain">Contain</option>
                        <option value="100% 100%">Stretch</option>
                        <option value="auto">Auto</option>
                      </select>
                    </div>
                    <div>
                      <Label for="bg_position">Background Position</Label>
                      <select id="bg_position" v-model="customStyle.backgroundPosition" class="w-full rounded-md border-gray-300">
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
                      <Input v-model="customStyle.backgroundColor" type="color" class="w-16" />
                      <Input v-model="customStyle.backgroundColor" type="text" placeholder="#ffffff" class="flex-1" />
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
                    <Input id="sort_order" v-model="form.sort_order" type="number" min="0" placeholder="0" />
                  </div>
                  <div class="flex items-center">
                    <input id="is_active" v-model="form.is_active" type="checkbox" class="rounded border-gray-300" />
                    <Label for="is_active" class="ml-2">Active</Label>
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex items-center justify-end gap-4 border-t pt-6">
                <Link
                  :href="route('graveyard.obituary-background-themes.index')"
                  class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                >
                  Cancel
                </Link>
                <Button type="submit" :disabled="form.processing" class="flex items-center gap-2">
                  <Save class="h-4 w-4" />
                  {{ form.processing ? 'Updating...' : 'Update Theme' }}
                </Button>
              </div>
            </form>
          </div>
        </div>

        <!-- Preview -->
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900">Preview</h3>

            <div class="space-y-4">
              <!-- Theme Preview -->
              <div class="rounded-lg border-2 border-gray-200 p-4">
                <div class="h-48 rounded-lg border" :style="previewStyle">
                  <div class="flex h-full items-center justify-center">
                    <div class="rounded bg-white/80 p-4 text-center text-gray-600">
                      <h4 class="font-semibold">{{ form.name || 'Theme Preview' }}</h4>
                      <p class="text-sm">{{ form.description || 'Theme description' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Theme Info -->
              <div class="space-y-2 rounded-lg bg-gray-50 p-4">
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
              <div class="rounded-lg bg-gray-50 p-4">
                <h4 class="mb-2 text-sm font-medium">Generated CSS Properties:</h4>
                <pre class="overflow-auto text-xs text-gray-600">{{ JSON.stringify(previewStyle, null, 2) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
