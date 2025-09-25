<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, FileImage, Music, Palette, Upload, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface ObituaryPage {
  id: number;
  uuid: string;
  service_type: 'basic' | 'premium';
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
  permanent_grave_booking?: {
    valid_member: {
      first_name: string;
      last_name: string;
    };
  };
  temporary_grave_booking?: {
    dead_first_name: string;
    dead_last_name: string;
  };
}

interface BackgroundOption {
  value: string;
  label: string;
  description: string;
  image?: string;
  tier: string;
}

interface Props {
  obituary: ObituaryPage;
  backgrounds: BackgroundOption[];
}

defineOptions({
  layout: AppLayout,
});

const props = defineProps<Props>();

const { success, error } = useToast();

// Get deceased person's name
const deceasedName = computed(() => {
  if (props.obituary.permanent_grave_booking) {
    const member = props.obituary.permanent_grave_booking.valid_member;
    return `${member.first_name} ${member.last_name}`;
  }
  if (props.obituary.temporary_grave_booking) {
    const booking = props.obituary.temporary_grave_booking;
    return `${booking.dead_first_name} ${booking.dead_last_name}`;
  }
  return 'Unknown';
});

// Form setup
const form = useForm({
  biography: props.obituary.biography || '',
  favorite_memory: props.obituary.favorite_memory || '',
  achievements: props.obituary.achievements || '',
  hobbies_interests: props.obituary.hobbies_interests || '',
  notes: props.obituary.notes || '',
  profile_image: null as File | null,
  gallery_images: [] as File[],
  audio_message: null as File | null,
  theme_color: props.obituary.theme_color || '#6366f1',
  background_style: props.obituary.background_style || 'plain',
  allow_condolences: props.obituary.allow_condolences,
  allow_memory_sharing: props.obituary.allow_memory_sharing,
  is_public: props.obituary.is_public,
});

// File input refs
const profileImageRef = ref<HTMLInputElement>();
const galleryImagesRef = ref<HTMLInputElement>();
const audioMessageRef = ref<HTMLInputElement>();

// Image preview states
const profileImagePreview = ref<string | null>(null);
const galleryPreviews = ref<string[]>([]);

// Available backgrounds computed property
const availableBackgrounds = computed(() => {
  if (props.obituary.service_type === 'premium') {
    // Combine both arrays and remove duplicates based on 'value' property
    const combined = [...props.backgrounds];
    const unique = combined.filter((bg, index, self) => index === self.findIndex((item) => item.value === bg.value));
    return unique;
  } else {
    return props.backgrounds;
  }
});

// Background preview
const getBackgroundPreview = computed(() => {
  // Find the selected background configuration
  const selectedBackground = availableBackgrounds.value.find((bg) => bg.value === form.background_style);

  if (selectedBackground?.image) {
    // Use image-based background
    return {
      backgroundImage: `url(${selectedBackground.image})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
      backgroundColor: '#f8f9fa',
    };
  } else if (selectedBackground && form.background_style) {
    // For themes without images, create a styled preview based on theme type
    if (form.background_style.includes('gradient') || selectedBackground.label.toLowerCase().includes('gradient')) {
      return {
        backgroundImage: `linear-gradient(135deg, ${form.theme_color || '#667eea'}, ${form.theme_color || '#764ba2'})`,
      };
    } else if (form.background_style.includes('pattern') || selectedBackground.label.toLowerCase().includes('pattern')) {
      return {
        backgroundColor: form.theme_color || '#f8f9fa',
        backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e0e0e0' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
      };
    } else {
      // Plain background with theme color
      return {
        backgroundColor: form.theme_color || '#ffffff',
        backgroundImage: 'none',
      };
    }
  } else {
    // Fallback plain background
    return {
      backgroundColor: form.theme_color || '#ffffff',
    };
  }
});

// File handlers with preview
const handleProfileImageSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    if (validateImageFile(file)) {
      form.profile_image = file;
      generateImagePreview(file, (preview) => {
        profileImagePreview.value = preview;
      });
      success('Profile image selected successfully!');
    }
  }
};

const handleProfileImageDrop = (event: DragEvent) => {
  event.preventDefault();
  const files = event.dataTransfer?.files;
  if (files && files[0]) {
    const file = files[0];
    if (validateImageFile(file)) {
      form.profile_image = file;
      generateImagePreview(file, (preview) => {
        profileImagePreview.value = preview;
      });
      success('Profile image uploaded successfully!');
    }
  }
};

const clearProfileImage = () => {
  form.profile_image = null;
  profileImagePreview.value = null;
  if (profileImageRef.value) {
    profileImageRef.value.value = '';
  }
};

const removeProfileImage = () => {
  if (confirm('Are you sure you want to remove the profile image?')) {
    router.delete(`/graveyard/obituaries/${props.obituary.uuid}/profile-image`, {
      preserveState: false,
    });
  }
};

const removeGalleryImage = (imagePath: string) => {
  if (confirm('Are you sure you want to remove this gallery image?')) {
    router.delete(`/graveyard/obituaries/${props.obituary.uuid}/gallery-image`, {
      data: { image_path: imagePath },
      preserveState: false,
    });
  }
};

const removeAudioMessage = () => {
  if (confirm('Are you sure you want to remove the audio message?')) {
    router.delete(`/graveyard/obituaries/${props.obituary.uuid}/audio-message`, {
      preserveState: false,
    });
  }
};

const handleGalleryImagesSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    const files = Array.from(target.files);
    const validFiles = files.filter((file) => validateImageFile(file));
    form.gallery_images = [...form.gallery_images, ...validFiles];

    // Generate previews for new files
    validFiles.forEach((file) => {
      generateImagePreview(file, (preview) => {
        galleryPreviews.value.push(preview);
      });
    });

    if (validFiles.length > 0) {
      success(`${validFiles.length} gallery image(s) selected successfully!`);
    }
  }
};

const handleGalleryImagesDrop = (event: DragEvent) => {
  event.preventDefault();
  const files = event.dataTransfer?.files;
  if (files) {
    const filesArray = Array.from(files);
    const validFiles = filesArray.filter((file) => validateImageFile(file));
    form.gallery_images = [...form.gallery_images, ...validFiles];

    // Generate previews for new files
    validFiles.forEach((file) => {
      generateImagePreview(file, (preview) => {
        galleryPreviews.value.push(preview);
      });
    });
  }
};

const removeExistingGalleryImage = (index: number) => {
  const imagePath = props.obituary.gallery_images?.[index];
  if (imagePath) {
    removeGalleryImage(imagePath);
  }
};

const removeNewGalleryImage = (index: number) => {
  form.gallery_images.splice(index, 1);
  galleryPreviews.value.splice(index, 1);
};

const handleAudioMessageSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    form.audio_message = target.files[0];
  }
};

// Utility functions
const validateImageFile = (file: File): boolean => {
  const maxSize = 2 * 1024 * 1024; // 2MB
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

  if (!allowedTypes.includes(file.type)) {
    error('Please select a valid image file (JPG, PNG, or GIF)');
    return false;
  }

  if (file.size > maxSize) {
    error('File size must be less than 2MB');
    return false;
  }

  return true;
};

const generateImagePreview = (file: File, callback: (preview: string) => void) => {
  const reader = new FileReader();
  reader.onload = (e) => {
    if (e.target?.result) {
      callback(e.target.result as string);
    }
  };
  reader.readAsDataURL(file);
};

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const submit = () => {
  // Create a manual FormData object to ensure proper data transmission
  const formData = new FormData();

  // Add text fields
  formData.append('biography', form.biography || '');
  formData.append('favorite_memory', form.favorite_memory || '');
  formData.append('achievements', form.achievements || '');
  formData.append('hobbies_interests', form.hobbies_interests || '');
  formData.append('notes', form.notes || '');
  formData.append('theme_color', form.theme_color || '');
  formData.append('background_style', form.background_style || '');

  // Add boolean fields
  formData.append('allow_condolences', form.allow_condolences ? '1' : '0');
  formData.append('allow_memory_sharing', form.allow_memory_sharing ? '1' : '0');
  formData.append('is_public', form.is_public ? '1' : '0');

  // Add files
  if (form.profile_image) {
    formData.append('profile_image', form.profile_image);
  }

  if (form.gallery_images && form.gallery_images.length > 0) {
    form.gallery_images.forEach((file, index) => {
      formData.append(`gallery_images[${index}]`, file);
    });
  }

  if (form.audio_message) {
    formData.append('audio_message', form.audio_message);
  }

  // Add method spoofing for PUT
  formData.append('_method', 'PUT');

  // Submit using native fetch to ensure proper FormData handling
  fetch(route('graveyard.obituaries.update', props.obituary.uuid), {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: formData,
  })
    .then((response) => {
      if (response.redirected) {
        window.location.href = response.url;
      } else if (response.ok) {
        success('Obituary updated successfully!');
        router.visit(route('graveyard.obituaries.show', props.obituary.uuid));
      } else {
        return response.text().then((text) => {
          console.error('Server response:', text);
          throw new Error('Server error');
        });
      }
    })
    .catch((err) => {
      console.error('Form submission failed:', err);
      error('Update failed. Please try again.');
    });
};

const goBack = () => {
  router.visit(route('graveyard.obituaries.show', props.obituary.uuid));
};
</script>

<template>
  <Head :title="`Edit Obituary - ${deceasedName}`" />

  <div class="py-12">
    <div class="mx-auto max-w-4xl sm:px-4 lg:px-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center space-x-4">
          <Button variant="outline" size="sm" @click="goBack">
            <ArrowLeft class="mr-2 h-4 w-4" />
            Back to Obituary
          </Button>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Obituary Page</h1>
            <p class="text-gray-600">Update memorial page for {{ deceasedName }}</p>
          </div>
        </div>
      </div>

      <!-- Service Type Info -->
      <Card class="mb-6">
        <CardContent class="pt-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900">{{ obituary.service_type.charAt(0).toUpperCase() + obituary.service_type.slice(1) }} Service</p>
              <p class="text-sm text-gray-600">Some features may be limited based on your service type</p>
            </div>
            <span
              :class="[
                'rounded-full px-3 py-1 text-sm font-medium',
                obituary.service_type === 'premium' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800',
              ]"
            >
              {{ obituary.service_type.toUpperCase() }}
            </span>
          </div>
        </CardContent>
      </Card>

      <!-- Error Display -->
      <div v-if="form.hasErrors" class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
        <h3 class="mb-2 font-semibold text-red-800">Please fix the following errors:</h3>
        <ul class="space-y-1 text-sm text-red-700">
          <li v-for="(error, field) in form.errors" :key="field">
            <strong>{{ field }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
          </li>
        </ul>
      </div>

      <!-- Main Form -->
      <form @submit.prevent="submit">
        <div class="space-y-6">
          <!-- Content Section -->
          <Card>
            <CardHeader>
              <CardTitle>Memorial Content</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div>
                <Label for="biography">Biography</Label>
                <Textarea id="biography" v-model="form.biography" placeholder="Write a brief biography of the deceased..." :rows="5" />
                <p class="mt-1 text-xs text-gray-500">Share the life story and background</p>
              </div>

              <div>
                <Label for="favorite_memory">Favorite Memory</Label>
                <Textarea id="favorite_memory" v-model="form.favorite_memory" placeholder="Share a favorite memory..." :rows="5" />
                <p class="mt-1 text-xs text-gray-500">A cherished memory to remember them by</p>
              </div>

              <div>
                <Label for="achievements">Achievements</Label>
                <Textarea id="achievements" v-model="form.achievements" placeholder="Notable achievements and accomplishments..." :rows="5" />
                <p class="mt-1 text-xs text-gray-500">Professional achievements, awards, and accomplishments</p>
              </div>

              <div>
                <Label for="hobbies_interests">Hobbies & Interests</Label>
                <Textarea id="hobbies_interests" v-model="form.hobbies_interests" placeholder="Hobbies, interests, and passions..." :rows="5" />
                <p class="mt-1 text-xs text-gray-500">What they loved to do in their free time</p>
              </div>

              <div>
                <Label for="notes">Family Notes & Messages</Label>
                <Textarea
                  id="notes"
                  v-model="form.notes"
                  placeholder="Family thoughts, funeral mass details, months mind mass timing and place, condolence messages from family, etc..."
                  :rows="5"
                  class="resize-y"
                />
                <p class="mt-1 text-xs text-gray-500">
                  Include funeral service details, family messages, special announcements, or any other important information.
                </p>
              </div>
            </CardContent>
          </Card>

          <!-- Media Upload Section -->
          <Card>
            <CardHeader>
              <CardTitle class="flex items-center">
                <FileImage class="mr-2 h-5 w-5" />
                Media & Photos
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-6">
              <!-- Profile Image Section -->
              <div>
                <Label class="text-base font-semibold">Profile Photo</Label>
                <p class="mb-3 text-sm text-gray-600">Main photo for the memorial page</p>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- Current Image Preview -->
                  <div>
                    <div class="aspect-square overflow-hidden rounded-lg border-2 border-dashed border-gray-200 bg-gray-50">
                      <div v-if="profileImagePreview || obituary.profile_image" class="relative h-full">
                        <img
                          :src="
                            profileImagePreview ||
                            (obituary.profile_image?.startsWith('http') ? obituary.profile_image : `/storage/${obituary.profile_image}`)
                          "
                          :alt="deceasedName"
                          class="h-full w-full object-cover"
                        />
                        <button
                          v-if="profileImagePreview"
                          type="button"
                          @click="clearProfileImage"
                          class="absolute top-2 right-2 rounded-full bg-red-600 p-1 text-white shadow-md hover:bg-red-700"
                        >
                          <X class="h-4 w-4" />
                        </button>
                      </div>
                      <div v-else class="flex h-full items-center justify-center">
                        <div class="text-center">
                          <FileImage class="mx-auto h-12 w-12 text-gray-400" />
                          <p class="mt-2 text-sm text-gray-500">No photo selected</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Upload Controls -->
                  <div class="space-y-3">
                    <div class="flex gap-2">
                      <Button type="button" variant="outline" @click="profileImageRef?.click()" class="flex-1 justify-start">
                        <Upload class="mr-2 h-4 w-4" />
                        {{ form.profile_image ? 'Change Photo' : obituary.profile_image ? 'Replace Photo' : 'Choose Photo' }}
                      </Button>

                      <Button
                        v-if="obituary.profile_image && !form.profile_image"
                        type="button"
                        variant="destructive"
                        size="sm"
                        @click="removeProfileImage"
                        title="Remove current profile image"
                      >
                        <X class="h-4 w-4" />
                      </Button>
                    </div>

                    <div
                      class="cursor-pointer rounded-lg border-2 border-dashed border-gray-300 p-4 text-center transition-colors hover:border-gray-400"
                      @drop="handleProfileImageDrop"
                      @dragover.prevent
                      @dragenter.prevent
                      @click="profileImageRef?.click()"
                    >
                      <Upload class="mx-auto mb-2 h-6 w-6 text-gray-400" />
                      <p class="text-sm text-gray-600">Click or drag image here</p>
                      <p class="mt-1 text-xs text-gray-500">Max 2MB • JPG, PNG, GIF</p>
                    </div>

                    <input ref="profileImageRef" type="file" accept="image/*" class="hidden" @change="handleProfileImageSelect" />

                    <div v-if="form.profile_image" class="rounded-lg border border-green-200 bg-green-50 p-3">
                      <div class="flex items-center">
                        <FileImage class="mr-2 h-4 w-4 text-green-600" />
                        <span class="text-sm text-green-700">{{ form.profile_image.name }}</span>
                        <span class="ml-2 text-xs text-green-600">({{ formatFileSize(form.profile_image.size) }})</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Gallery Images (Premium Only) -->
              <div v-if="obituary.service_type === 'premium'" class="border-t pt-6">
                <Label class="flex items-center text-base font-semibold">
                  Gallery Photos
                  <span class="ml-2 rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-700">Premium Feature</span>
                </Label>
                <p class="mb-4 text-sm text-gray-600">Additional photos for the memorial gallery</p>

                <!-- Current Gallery Grid -->
                <div v-if="obituary.gallery_images?.length || galleryPreviews.length" class="mb-4">
                  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 16px">
                    <!-- Existing Images -->
                    <div
                      v-for="(image, index) in obituary.gallery_images || []"
                      :key="`existing-${index}`"
                      style="position: relative; width: 150px; height: 150px"
                      class="group"
                    >
                      <img
                        :src="image.startsWith('http') ? image : `/storage/${image}`"
                        :alt="`Gallery ${index + 1}`"
                        style="
                          width: 150px;
                          height: 150px;
                          object-fit: cover;
                          border-radius: 8px;
                          border: 2px solid #e5e7eb;
                          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                        "
                      />
                      <!-- Delete button positioned at top-right corner -->
                      <button
                        type="button"
                        @click="removeExistingGalleryImage(index)"
                        class="absolute top-1 right-1 rounded-full bg-red-600 p-1 text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100 hover:bg-red-700"
                        style="z-index: 10"
                        title="Remove image"
                      >
                        <X class="h-3 w-3" />
                      </button>
                      <!-- Existing label -->
                      <span class="bg-opacity-60 absolute bottom-1 left-1 rounded bg-black px-2 py-1 text-xs text-white">Existing</span>
                    </div>

                    <!-- New Images Preview -->
                    <div
                      v-for="(preview, index) in galleryPreviews"
                      :key="`new-${index}`"
                      style="position: relative; width: 150px; height: 150px"
                      class="group"
                    >
                      <img
                        :src="preview"
                        :alt="`New Gallery ${index + 1}`"
                        style="
                          width: 150px;
                          height: 150px;
                          object-fit: cover;
                          border-radius: 8px;
                          border: 2px solid #10b981;
                          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                        "
                      />
                      <!-- Delete button positioned at top-right corner -->
                      <button
                        type="button"
                        @click="removeNewGalleryImage(index)"
                        class="absolute top-1 right-1 rounded-full bg-red-600 p-1 text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100 hover:bg-red-700"
                        style="z-index: 10"
                        title="Remove image"
                      >
                        <X class="h-3 w-3" />
                      </button>
                      <!-- New label -->
                      <span class="absolute bottom-1 left-1 rounded bg-green-600 px-2 py-1 text-xs text-white">New</span>
                    </div>
                  </div>
                </div>

                <!-- Upload Area -->
                <div class="space-y-3">
                  <Button type="button" variant="outline" @click="galleryImagesRef?.click()" class="w-full justify-start">
                    <Upload class="mr-2 h-4 w-4" />
                    {{ form.gallery_images.length > 0 ? `Add More Photos (${form.gallery_images.length} selected)` : 'Add Gallery Photos' }}
                  </Button>

                  <div
                    class="cursor-pointer rounded-lg border-2 border-dashed border-purple-300 p-6 text-center transition-colors hover:border-purple-400 hover:bg-purple-50"
                    @drop="handleGalleryImagesDrop"
                    @dragover.prevent
                    @dragenter.prevent
                    @click="galleryImagesRef?.click()"
                  >
                    <Upload class="mx-auto mb-3 h-8 w-8 text-purple-400" />
                    <p class="text-sm font-medium text-gray-600">Drop multiple images here or click to browse</p>
                    <p class="mt-2 text-xs text-gray-500">Max 2MB each • JPG, PNG, GIF • Multiple selection allowed</p>
                  </div>

                  <input ref="galleryImagesRef" type="file" accept="image/*" multiple class="hidden" @change="handleGalleryImagesSelect" />

                  <div v-if="form.gallery_images.length" class="rounded-lg border border-purple-200 bg-purple-50 p-4">
                    <p class="mb-2 text-sm font-medium text-purple-800">{{ form.gallery_images.length }} new photos selected:</p>
                    <div class="space-y-1">
                      <div
                        v-for="(file, index) in form.gallery_images"
                        :key="index"
                        class="flex items-center justify-between text-xs text-purple-700"
                      >
                        <span class="truncate">{{ file.name }}</span>
                        <span class="ml-2 text-purple-600">{{ formatFileSize(file.size) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Audio Message (Premium Only) -->
              <div v-if="obituary.service_type === 'premium'">
                <!-- Current Audio -->
                <div v-if="obituary.audio_message">
                  <div class="flex items-center justify-between">
                    <Label>Current Audio Message</Label>
                    <Button type="button" variant="destructive" size="sm" @click="removeAudioMessage" title="Remove current audio message">
                      <X class="mr-1 h-4 w-4" />
                      Remove
                    </Button>
                  </div>
                  <div class="mt-2 mb-4">
                    <audio controls class="w-full max-w-[448px]">
                      <source
                        :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`"
                        type="audio/mpeg"
                      />
                      Your browser does not support the audio element.
                    </audio>
                  </div>
                </div>

                <div>
                  <Label>{{ obituary.audio_message ? 'Replace Audio Message' : 'Audio Message' }}</Label>
                  <div class="mt-1">
                    <Button type="button" variant="outline" @click="audioMessageRef?.click()" class="w-full justify-start">
                      <Music class="mr-2 h-4 w-4" />
                      {{ form.audio_message ? form.audio_message.name : 'Choose new audio message' }}
                    </Button>
                    <input ref="audioMessageRef" type="file" accept="audio/*" class="hidden" @change="handleAudioMessageSelect" />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">Supported formats: MP3, WAV, M4A (max 10MB)</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Customization Section -->
          <Card>
            <CardHeader>
              <CardTitle class="flex items-center">
                <Palette class="mr-2 h-5 w-5" />
                {{ obituary.service_type === 'premium' ? 'Customization' : 'Background Style' }}
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Theme Color (Premium Only) -->
              <div v-if="obituary.service_type === 'premium'">
                <Label for="theme_color">Theme Color</Label>
                <div class="mt-1 flex items-center space-x-2">
                  <input id="theme_color" v-model="form.theme_color" type="color" class="h-10 w-20 cursor-pointer rounded border border-gray-300" />
                  <Input v-model="form.theme_color" class="flex-1" />
                </div>
              </div>

              <!-- Background Style -->
              <div>
                <Label for="background_style">{{ obituary.service_type === 'premium' ? 'Background Style' : 'Choose Background' }}</Label>

                <!-- Responsive Grid for Background Options -->
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  <div
                    v-for="background in availableBackgrounds"
                    :key="background.value"
                    class="hover:border-primary/50 relative cursor-pointer rounded-lg border p-3 transition-colors"
                    :class="form.background_style === background.value ? 'border-primary bg-primary/5' : 'border-gray-200'"
                    @click="form.background_style = background.value"
                  >
                    <div class="flex items-center space-x-3">
                      <input type="radio" :value="background.value" v-model="form.background_style" class="hidden" />
                      <div
                        v-if="background.image"
                        class="h-12 w-12 flex-shrink-0 overflow-hidden rounded border"
                        :style="{ backgroundImage: `url(${background.image})`, backgroundSize: 'cover', backgroundPosition: 'center' }"
                      ></div>
                      <div v-else class="h-12 w-12 flex-shrink-0 rounded border bg-gray-100"></div>
                      <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-medium">{{ background.label }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ background.description }}</p>
                        <span
                          v-if="background.tier === 'premium'"
                          class="mt-1 inline-flex items-center rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800"
                          >Premium</span
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Background Preview -->
                <div
                  class="mt-3 flex h-20 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-4 text-center text-sm text-gray-600"
                  :style="getBackgroundPreview"
                >
                  Preview: {{ form.background_style || 'plain' }}
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Settings Section -->
          <Card>
            <CardHeader>
              <CardTitle>Page Settings</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Premium Features -->
              <div v-if="obituary.service_type === 'premium'" class="space-y-4">
                <div class="flex items-center justify-between">
                  <div>
                    <Label>Allow Condolences <span class="text-xs font-medium text-purple-600">(Premium Feature)</span></Label>
                    <p class="text-sm text-gray-600">Allow visitors to leave condolence messages</p>
                  </div>
                  <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" v-model="form.allow_condolences" class="peer sr-only" />
                    <div
                      class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-purple-600 peer-focus:ring-4 peer-focus:ring-purple-300 peer-focus:outline-none after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                    ></div>
                  </label>
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <Label>Allow Memory Sharing <span class="text-xs font-medium text-purple-600">(Premium Feature)</span></Label>
                    <p class="text-sm text-gray-600">Allow visitors to share memories</p>
                  </div>
                  <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" v-model="form.allow_memory_sharing" class="peer sr-only" />
                    <div
                      class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-purple-600 peer-focus:ring-4 peer-focus:ring-purple-300 peer-focus:outline-none after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                    ></div>
                  </label>
                </div>
              </div>

              <!-- Basic Features Available to All -->
              <div class="flex items-center justify-between">
                <div>
                  <Label>Public Page</Label>
                  <p class="text-sm text-gray-600">Make the obituary page publicly accessible</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                  <input type="checkbox" v-model="form.is_public" class="peer sr-only" />
                  <div
                    class="peer h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-blue-600 peer-focus:ring-4 peer-focus:ring-blue-300 peer-focus:outline-none after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white"
                  ></div>
                </label>
              </div>
            </CardContent>
          </Card>

          <!-- Submit Section -->
          <div class="flex items-center justify-between">
            <Button type="button" variant="outline" @click="goBack"> Cancel </Button>
            <Button
              type="submit"
              :disabled="form.processing"
              class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-3 text-white hover:from-purple-700 hover:to-blue-700"
            >
              {{ form.processing ? 'Updating...' : 'Update Obituary' }}
            </Button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
