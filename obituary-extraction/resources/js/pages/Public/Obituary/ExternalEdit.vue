<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ArrowLeftIcon, Image, Music, Palette, SaveIcon, Settings, X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
  obituary: {
    uuid: string;
    // service_type: 'basic' | 'premium';
    biography?: string;
    favorite_memory?: string;
    achievements?: string;
    hobbies_interests?: string;
    notes?: string;
    profile_image?: string;
    gallery_images?: string[];
    audio_message?: string;
    theme_color?: string;
    background_style?: string;
    allow_condolences: boolean;
    allow_memory_sharing: boolean;
    is_public: boolean;
  };
  deceasedName: string;
  backgroundOptions?: Array<{
    value: string;
    label: string;
    description: string;
    image?: string;
    tier: string;
  }>;
}

const props = defineProps<Props>();

const form = ref({
  biography: props.obituary.biography || '',
  favorite_memory: props.obituary.favorite_memory || '',
  achievements: props.obituary.achievements || '',
  hobbies_interests: props.obituary.hobbies_interests || '',
  notes: props.obituary.notes || '',
  theme_color: props.obituary.theme_color || '#4338ca',
  background_style: props.obituary.background_style || 'plain',
  allow_condolences: props.obituary.allow_condolences ?? true,
  allow_memory_sharing: props.obituary.allow_memory_sharing ?? true,
  is_public: props.obituary.is_public ?? true,
});

// File handling
const profileImageFile = ref<File | null>(null);
const profileImagePreview = ref<string | null>(null);
const galleryFiles = ref<File[]>([]);
const audioFile = ref<File | null>(null);
const imagesToRemove = ref<string[]>([]);

// Computed
// const isPremium = computed(() => props.obituary.service_type === 'premium');
const availableBackgrounds = props.backgroundOptions || [];

const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const getCharacterCount = (text: string, max: number) => {
  const count = text.length;
  const remaining = max - count;
  return {
    count,
    remaining,
    isNearLimit: remaining <= 50,
    isOverLimit: remaining < 0,
  };
};

// File handling functions
const handleProfileImageChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    profileImageFile.value = file;

    // Create preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
      profileImagePreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const handleGalleryChange = (event: Event) => {
  const files = Array.from((event.target as HTMLInputElement).files || []);
  galleryFiles.value = [...galleryFiles.value, ...files];
};

const removeGalleryImage = (index: number) => {
  galleryFiles.value.splice(index, 1);
};

const removeExistingGalleryImage = (imagePath: string) => {
  imagesToRemove.value.push(imagePath);
};

const handleAudioChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    audioFile.value = file;
  }
};

const removeAudio = () => {
  audioFile.value = null;
};

// Update save function to handle file uploads
const saveChanges = async () => {
  isSubmitting.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    const formData = new FormData();

    // Add method spoofing for PUT request
    formData.append('_method', 'PUT');

    // Add text fields with proper boolean handling
    Object.entries(form.value).forEach(([key, value]) => {
      if (typeof value === 'boolean') {
        formData.append(key, value ? '1' : '0');
      } else {
        formData.append(key, String(value));
      }
    });

    // Add files
    if (profileImageFile.value) {
      formData.append('profile_image', profileImageFile.value);
    }

    galleryFiles.value.forEach((file, index) => {
      formData.append(`gallery_images[${index}]`, file);
    });

    if (audioFile.value) {
      formData.append('audio_message', audioFile.value);
    }

    // Add images to remove
    if (imagesToRemove.value.length > 0) {
      formData.append('remove_gallery_images', JSON.stringify(imagesToRemove.value));
    }

    const response = await fetch(`/obituary/${props.obituary.uuid}/manage/update`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: formData,
    });

    const data = await response.json();

    if (response.ok && data.success) {
      successMessage.value = data.message || 'Changes saved successfully!';
      // Auto-dismiss success message after 5 seconds
      setTimeout(() => {
        successMessage.value = '';
      }, 5000);
      // Reset file inputs
      profileImageFile.value = null;
      profileImagePreview.value = null;
      galleryFiles.value = [];
      audioFile.value = null;
      imagesToRemove.value = [];
    } else {
      errorMessage.value = data.error || 'Failed to save changes. Please try again.';
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat();
        errorMessage.value = errorMessages.join(' ');
      }
    }
  } catch (error) {
    console.error('Save error:', error);
    errorMessage.value = 'Network error. Please check your connection and try again.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <Head :title="`Editing ${deceasedName} - Obituary Content`" />

  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="border-b bg-white shadow-sm">
      <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <a :href="`/obituary/${obituary.uuid}/manage/dashboard`" class="mr-4 inline-flex items-center text-gray-600 hover:text-gray-900">
              <ArrowLeftIcon class="h-5 w-5" />
            </a>
            <div>
              <h1 class="text-xl font-semibold text-gray-900">Editing: {{ deceasedName }}</h1>
              <p class="text-sm text-gray-500">Update obituary content</p>
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <a
              :href="`/obituary/${obituary.uuid}`"
              target="_blank"
              class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm leading-4 font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
            >
              Preview
            </a>
          </div>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">
      <!-- Floating Success Message -->
      <div v-if="successMessage" class="fixed top-4 left-1/2 z-50 w-full max-w-[448px] -translate-x-1/2 transform">
        <div class="mx-4 rounded-lg border border-green-200 bg-green-50 p-4 shadow-lg">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                ></path>
              </svg>
            </div>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
            </div>
            <div class="ml-auto pl-3">
              <div class="-mx-1.5 -my-1.5">
                <button
                  @click="successMessage = ''"
                  class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 transition-colors hover:bg-green-100 focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 focus:outline-none"
                >
                  <X class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Floating Error Message -->
      <div v-if="errorMessage" class="fixed top-4 left-1/2 z-50 w-full max-w-[448px] -translate-x-1/2 transform">
        <div class="mx-4 rounded-lg border border-red-200 bg-red-50 p-4 shadow-lg">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
                ></path>
              </svg>
            </div>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
            </div>
            <div class="ml-auto pl-3">
              <div class="-mx-1.5 -my-1.5">
                <button
                  @click="errorMessage = ''"
                  class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 transition-colors hover:bg-red-100 focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50 focus:outline-none"
                >
                  <X class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Form -->
      <div class="rounded-lg bg-white shadow">
        <form @submit.prevent="saveChanges" class="space-y-8 p-6">
          <!-- Biography -->
          <div>
            <label for="biography" class="mb-2 block text-sm font-medium text-gray-700"> Life Story / Biography </label>
            <textarea
              id="biography"
              v-model="form.biography"
              rows="6"
              class="resize-vertical w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
              placeholder="Tell the story of their life, their journey, and what made them special..."
              :disabled="isSubmitting"
              maxlength="2000"
            ></textarea>
            <div class="mt-1 flex justify-between text-xs text-gray-500">
              <span>Share their life journey, achievements, and what made them special</span>
              <span
                :class="{
                  'text-red-500': getCharacterCount(form.biography, 2000).isOverLimit,
                  'text-yellow-500': getCharacterCount(form.biography, 2000).isNearLimit,
                }"
              >
                {{ getCharacterCount(form.biography, 2000).count }}/2000
              </span>
            </div>
          </div>

          <!-- Favorite Memory -->
          <div>
            <label for="favorite_memory" class="mb-2 block text-sm font-medium text-gray-700"> Cherished Memories </label>
            <textarea
              id="favorite_memory"
              v-model="form.favorite_memory"
              rows="4"
              class="resize-vertical w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
              placeholder="Share a special memory, story, or moment that captures who they were..."
              :disabled="isSubmitting"
              maxlength="1000"
            ></textarea>
            <div class="mt-1 flex justify-between text-xs text-gray-500">
              <span>A special memory or story that captures their spirit</span>
              <span
                :class="{
                  'text-red-500': getCharacterCount(form.favorite_memory, 1000).isOverLimit,
                  'text-yellow-500': getCharacterCount(form.favorite_memory, 1000).isNearLimit,
                }"
              >
                {{ getCharacterCount(form.favorite_memory, 1000).count }}/1000
              </span>
            </div>
          </div>

          <!-- Achievements -->
          <div>
            <label for="achievements" class="mb-2 block text-sm font-medium text-gray-700"> Achievements & Legacy </label>
            <textarea
              id="achievements"
              v-model="form.achievements"
              rows="4"
              class="resize-vertical w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
              placeholder="List their accomplishments, awards, career highlights, or contributions..."
              :disabled="isSubmitting"
              maxlength="1000"
            ></textarea>
            <div class="mt-1 flex justify-between text-xs text-gray-500">
              <span>Professional achievements, awards, and lasting contributions</span>
              <span
                :class="{
                  'text-red-500': getCharacterCount(form.achievements, 1000).isOverLimit,
                  'text-yellow-500': getCharacterCount(form.achievements, 1000).isNearLimit,
                }"
              >
                {{ getCharacterCount(form.achievements, 1000).count }}/1000
              </span>
            </div>
          </div>

          <!-- Hobbies & Interests -->
          <div>
            <label for="hobbies_interests" class="mb-2 block text-sm font-medium text-gray-700"> Hobbies & Interests </label>
            <textarea
              id="hobbies_interests"
              v-model="form.hobbies_interests"
              rows="3"
              class="resize-vertical w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
              placeholder="What did they love to do? Sports, arts, reading, traveling, gardening..."
              :disabled="isSubmitting"
              maxlength="1000"
            ></textarea>
            <div class="mt-1 flex justify-between text-xs text-gray-500">
              <span>Activities, hobbies, and passions they enjoyed</span>
              <span
                :class="{
                  'text-red-500': getCharacterCount(form.hobbies_interests, 1000).isOverLimit,
                  'text-yellow-500': getCharacterCount(form.hobbies_interests, 1000).isNearLimit,
                }"
              >
                {{ getCharacterCount(form.hobbies_interests, 1000).count }}/1000
              </span>
            </div>
          </div>

          <!-- Family Notes & Messages -->
          <div>
            <label for="notes" class="mb-2 block text-sm font-medium text-gray-700"> Family Notes & Messages </label>
            <textarea
              id="notes"
              v-model="form.notes"
              rows="4"
              class="resize-vertical w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
              placeholder="Special messages from family, funeral arrangements, or other important information..."
              :disabled="isSubmitting"
              maxlength="1000"
            ></textarea>
            <div class="mt-1 flex justify-between text-xs text-gray-500">
              <span>Special messages, arrangements, or family communications</span>
              <span
                :class="{
                  'text-red-500': getCharacterCount(form.notes, 1000).isOverLimit,
                  'text-yellow-500': getCharacterCount(form.notes, 1000).isNearLimit,
                }"
              >
                {{ getCharacterCount(form.notes, 1000).count }}/1000
              </span>
            </div>
          </div>

          <!-- Profile Image Section -->
          <div class="border-t pt-8">
            <div class="mb-4 flex items-center space-x-2">
              <Image class="h-5 w-5 text-gray-600" />
              <h3 class="text-lg font-medium text-gray-900">Profile Photo</h3>
            </div>

            <div class="space-y-4">
              <!-- Current/Preview Profile Image -->
              <div v-if="profileImagePreview || obituary.profile_image" class="flex items-center space-x-4">
                <img
                  :src="
                    profileImagePreview ||
                    (obituary.profile_image?.startsWith('http') ? obituary.profile_image : `/storage/${obituary.profile_image}`)
                  "
                  :alt="deceasedName"
                  class="h-20 w-20 rounded-lg border object-cover"
                />
                <div>
                  <p class="text-sm text-gray-600">
                    {{ profileImagePreview ? 'New profile photo preview' : 'Current profile photo' }}
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ profileImagePreview ? 'This will replace the current image when saved' : 'Upload a new image to replace this one' }}
                  </p>
                </div>
              </div>

              <!-- Upload New Profile Image -->
              <div>
                <label for="profile_image" class="mb-2 block text-sm font-medium text-gray-700">
                  {{ obituary.profile_image ? 'Upload New Profile Photo' : 'Upload Profile Photo' }}
                </label>
                <div class="flex items-center space-x-4">
                  <input
                    id="profile_image"
                    type="file"
                    accept="image/*"
                    @change="handleProfileImageChange"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100"
                    :disabled="isSubmitting"
                  />
                </div>
                <p v-if="profileImageFile" class="mt-2 text-sm text-green-600">New image selected: {{ profileImageFile.name }}</p>
                <p class="mt-1 text-xs text-gray-500">Recommended: Square image, at least 400x400 pixels. Formats: JPG, PNG, GIF (max 5MB)</p>
              </div>
            </div>
          </div>

          <!-- Gallery Images Section (Premium Only) -->
          <div class="border-t pt-8">
            <div class="mb-4 flex items-center space-x-2">
              <Image class="h-5 w-5 text-gray-600" />
              <h3 class="text-lg font-medium text-gray-900">Photo Gallery</h3>
              <!-- <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800"> Premium </span> -->
            </div>

            <div class="space-y-4">
              <!-- Current Gallery Images -->
              <div v-if="obituary.gallery_images && obituary.gallery_images.length > 0" class="space-y-3">
                <h4 class="text-sm font-medium text-gray-700">Current Gallery Images</h4>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                  <div v-for="(image, index) in obituary.gallery_images" :key="index" class="group relative" v-show="!imagesToRemove.includes(image)">
                    <img
                      :src="image.startsWith('http') ? image : `/storage/${image}`"
                      :alt="`Gallery image ${index + 1}`"
                      class="h-24 w-full rounded-lg border object-cover"
                    />
                    <button
                      type="button"
                      @click="removeExistingGalleryImage(image)"
                      class="absolute top-1 right-1 rounded-full bg-red-500 p-1 text-white opacity-0 transition-opacity group-hover:opacity-100 hover:bg-red-600"
                      title="Remove this image"
                    >
                      <X class="h-3 w-3" />
                    </button>
                  </div>
                </div>
                <div v-if="imagesToRemove.length > 0" class="rounded-lg bg-red-50 p-3">
                  <p class="text-sm text-red-800">{{ imagesToRemove.length }} image(s) will be removed when you save changes.</p>
                </div>
              </div>

              <!-- Upload New Gallery Images -->
              <div>
                <label for="gallery_images" class="mb-2 block text-sm font-medium text-gray-700"> Add Gallery Photos </label>
                <input
                  id="gallery_images"
                  type="file"
                  accept="image/*"
                  multiple
                  @change="handleGalleryChange"
                  class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100"
                  :disabled="isSubmitting"
                />

                <!-- Preview Selected Files -->
                <div v-if="galleryFiles.length > 0" class="mt-4">
                  <p class="mb-2 text-sm font-medium text-gray-700">New images to add:</p>
                  <div class="flex flex-wrap gap-2">
                    <div v-for="(file, index) in galleryFiles" :key="index" class="relative inline-block">
                      <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                        {{ file.name }}
                        <button type="button" @click="removeGalleryImage(index)" class="ml-2 text-green-600 hover:text-green-800">
                          <X class="h-3 w-3" />
                        </button>
                      </span>
                    </div>
                  </div>
                </div>

                <p class="mt-1 text-xs text-gray-500">Select multiple images to create a photo gallery. Formats: JPG, PNG, GIF (max 5MB each)</p>
              </div>
            </div>
          </div>

          <!-- Audio Message Section (Premium Only) -->
          <div class="border-t pt-8">
            <div class="mb-4 flex items-center space-x-2">
              <Music class="h-5 w-5 text-gray-600" />
              <h3 class="text-lg font-medium text-gray-900">Audio Message</h3>
              <!-- <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800"> Premium </span> -->
            </div>

            <div class="space-y-4">
              <!-- Current Audio -->
              <div v-if="obituary.audio_message" class="rounded-lg bg-gray-50 p-4">
                <p class="mb-2 text-sm text-gray-600">Current audio message</p>
                <audio controls class="w-full">
                  <source
                    :src="obituary.audio_message?.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`"
                    type="audio/mpeg"
                  />
                  Your browser does not support the audio element.
                </audio>
                <p class="mt-2 text-xs text-gray-500">Upload a new audio file to replace this one</p>
              </div>

              <!-- Upload New Audio -->
              <div>
                <label for="audio_message" class="mb-2 block text-sm font-medium text-gray-700">
                  {{ obituary.audio_message ? 'Upload New Audio Message' : 'Upload Audio Message' }}
                </label>
                <input
                  id="audio_message"
                  type="file"
                  accept="audio/*"
                  @change="handleAudioChange"
                  class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100"
                  :disabled="isSubmitting"
                />

                <div v-if="audioFile" class="mt-2 flex items-center justify-between rounded-lg bg-green-50 p-3">
                  <span class="text-sm text-green-800">New audio selected: {{ audioFile.name }}</span>
                  <button type="button" @click="removeAudio" class="text-green-600 hover:text-green-800">
                    <X class="h-4 w-4" />
                  </button>
                </div>

                <p class="mt-1 text-xs text-gray-500">
                  Record or upload a personal message, eulogy, or favorite song. Formats: MP3, WAV, M4A (max 25MB)
                </p>
              </div>
            </div>
          </div>

          <!-- Theme & Background Section (Premium Only) -->
          <div class="border-t pt-8">
            <div class="mb-4 flex items-center space-x-2">
              <Palette class="h-5 w-5 text-gray-600" />
              <h3 class="text-lg font-medium text-gray-900">Theme & Appearance</h3>
              <!-- <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800"> Premium </span> -->
            </div>

            <div class="space-y-6">
              <!-- Theme Color -->
              <div>
                <label for="theme_color" class="mb-2 block text-sm font-medium text-gray-700"> Theme Color </label>
                <div class="flex items-center space-x-4">
                  <input
                    id="theme_color"
                    type="color"
                    v-model="form.theme_color"
                    class="h-10 w-20 cursor-pointer rounded border border-gray-300"
                    :disabled="isSubmitting"
                  />
                  <input
                    type="text"
                    v-model="form.theme_color"
                    class="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm"
                    placeholder="#4338ca"
                    :disabled="isSubmitting"
                  />
                </div>
                <p class="mt-1 text-xs text-gray-500">Choose a color that reflects their personality or favorite color</p>
              </div>

              <!-- Background Style -->
              <div v-if="availableBackgrounds.length > 0">
                <label class="mb-3 block text-sm font-medium text-gray-700"> Background Style </label>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                  <div
                    v-for="background in availableBackgrounds"
                    :key="background.value"
                    class="relative cursor-pointer"
                    @click="form.background_style = background.value"
                  >
                    <div
                      :class="[
                        'rounded-lg border-2 p-4 transition-all',
                        form.background_style === background.value ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300',
                      ]"
                    >
                      <div
                        v-if="background.image"
                        class="mb-2 h-16 w-full rounded bg-gray-100 bg-cover bg-center"
                        :style="{ backgroundImage: `url(${background.image})` }"
                      ></div>
                      <div v-else class="mb-2 h-16 w-full rounded bg-gradient-to-br from-gray-100 to-gray-200"></div>
                      <h4 class="text-sm font-medium text-gray-900">{{ background.label }}</h4>
                      <p class="mt-1 text-xs text-gray-500">{{ background.description }}</p>
                      <!-- <div v-if="background.tier === 'premium'" class="absolute top-2 right-2">
                        <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800">
                          Premium
                        </span>
                      </div> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Settings Section -->
          <div class="border-t pt-8">
            <div class="mb-4 flex items-center space-x-2">
              <Settings class="h-5 w-5 text-gray-600" />
              <h3 class="text-lg font-medium text-gray-900">Page Settings</h3>
            </div>

            <div class="space-y-4">
              <!-- Privacy Setting -->
              <div>
                <label class="flex items-start space-x-3">
                  <input
                    type="checkbox"
                    v-model="form.is_public"
                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    :disabled="isSubmitting"
                  />
                  <div>
                    <span class="text-sm font-medium text-gray-700">Make page publicly visible</span>
                    <p class="text-xs text-gray-500">When enabled, anyone with the link can view this obituary page</p>
                  </div>
                </label>
              </div>

              <!-- Condolences Setting (Premium Only) -->
              <div>
                <label class="flex items-start space-x-3">
                  <input
                    type="checkbox"
                    v-model="form.allow_condolences"
                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    :disabled="isSubmitting"
                  />
                  <div>
                    <span class="text-sm font-medium text-gray-700">Allow condolence messages</span>
                    <p class="text-xs text-gray-500">Visitors can leave condolence messages that you can review and approve</p>
                  </div>
                </label>
              </div>

              <!-- Memory Sharing Setting (Premium Only) -->
              <div>
                <label class="flex items-start space-x-3">
                  <input
                    type="checkbox"
                    v-model="form.allow_memory_sharing"
                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    :disabled="isSubmitting"
                  />
                  <div>
                    <span class="text-sm font-medium text-gray-700">Allow memory sharing</span>
                    <p class="text-xs text-gray-500">Visitors can share their own memories and stories</p>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-between border-t border-gray-200 pt-6">
            <a
              :href="`/obituary/${obituary.uuid}/manage/dashboard`"
              class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
            >
              Cancel
            </a>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            >
              <SaveIcon v-if="!isSubmitting" class="mr-2 h-4 w-4" />
              <svg
                v-if="isSubmitting"
                class="mr-2 -ml-1 h-4 w-4 animate-spin text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Info Card -->
      <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              ></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-blue-700">
              <strong>Tip:</strong> Take your time to craft meaningful content. These details help visitors understand and remember
              {{ deceasedName }}'s life and legacy.
            </p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
