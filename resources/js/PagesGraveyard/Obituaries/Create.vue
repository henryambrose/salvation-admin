<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ArrowLeft, FileImage, Music, Palette, RefreshCw, Sparkles, Upload, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useToast } from '@/composables/useToast';

interface ValidMember {
  id: number;
  first_name: string;
  last_name: string;
  relationship: string;
  member?: {
    first_name: string;
    last_name: string;
    member_no: string;
  };
}

interface PermanentGraveBooking {
  id: number;
  booking_reference: string;
  status: string;
  died_on: string;
  buried_on: string;
  cause_of_death: string;
  minister?: string;
  valid_member: ValidMember;
}

interface TemporaryGraveBooking {
  id: number;
  booking_reference: string;
  status: string;
  dead_first_name: string;
  dead_last_name: string;
  died_on: string;
  buried_on: string;
  cause_of_death: string;
  minister?: string;
}

interface BackgroundOption {
  value: string;
  label: string;
  description: string;
  image?: string;
  tier: string;
}

interface Props {
  booking?: PermanentGraveBooking | TemporaryGraveBooking;
  bookingType?: 'permanent' | 'temporary';
  basicBackgrounds: BackgroundOption[];
  premiumBackgrounds: BackgroundOption[];
}

const props = defineProps<Props>();

const { success, error } = useToast();

// Get deceased person's name
const deceasedName = computed(() => {
  if (!props.booking) return '';

  if (props.bookingType === 'permanent') {
    const permanentBooking = props.booking as PermanentGraveBooking;
    return `${permanentBooking.valid_member.first_name} ${permanentBooking.valid_member.last_name}`;
  } else {
    const temporaryBooking = props.booking as TemporaryGraveBooking;
    return `${temporaryBooking.dead_first_name} ${temporaryBooking.dead_last_name}`;
  }
});

// Pricing structure
const obituaryPricing = {
  basic: { amount: 500, label: '₹500', features: ['Basic obituary page', 'QR code generation', 'Profile photo', 'Biography & content'] },
  premium: {
    amount: 1500,
    label: '₹1,500',
    features: [
      'All basic features',
      'Gallery photos',
      'Condolence collection',
      'Memory sharing',
      'Audio messages',
      'Custom themes',
      'Priority support',
    ],
  },
};

// Form setup
const form = useForm({
  booking_type: props.bookingType || '',
  booking_id: props.booking?.id || null,
  service_type: 'basic' as 'basic' | 'premium',
  biography: '',
  favorite_memory: '',
  achievements: '',
  hobbies_interests: '',
  notes: '',
  profile_image: null as File | null,
  gallery_images: [] as File[],
  audio_message: null as File | null,
  theme_color: '#6366f1',
  background_style: 'plain' as string,
  allow_condolences: false as boolean, // Premium feature - disabled by default
  allow_memory_sharing: false as boolean, // Premium feature - disabled by default
  is_public: true,
});

// Computed properties
const selectedServicePrice = computed(() => obituaryPricing[form.service_type]);

const profileImageRef = ref<HTMLInputElement>();
const galleryImagesRef = ref<HTMLInputElement>();
const audioMessageRef = ref<HTMLInputElement>();

// Image preview states
const profileImagePreview = ref<string | null>(null);
const galleryPreviews = ref<string[]>([]);

// Rephrasing states
const rephraseLoading = ref<Record<string, boolean>>({
  biography: false,
  favorite_memory: false,
  achievements: false,
  hobbies_interests: false,
  notes: false,
});

const showRephraseDialog = ref(false);
const rephraseDialogData = ref<{
  fieldType: string;
  originalText: string;
  rephrasedText: string;
} | null>(null);

// Watch for service type changes to enable/disable premium features
watch(
  () => form.service_type,
  (newType) => {
    if (newType === 'basic') {
      // Disable premium features for basic service
      form.allow_condolences = false;
      form.allow_memory_sharing = false;
      form.gallery_images = [];
      galleryPreviews.value = []; // Clear gallery previews too
      form.audio_message = null;
      form.theme_color = '#6366f1';
      form.background_style = 'plain';
    } else {
      // Enable premium features
      form.allow_condolences = true;
      form.allow_memory_sharing = true;
    }
  },
);

// Enhanced file handlers with preview and validation
const handleProfileImageSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    if (validateImageFile(file)) {
      form.profile_image = file;
      generateImagePreview(file, (preview) => {
        profileImagePreview.value = preview;
      });
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

const handleGalleryImagesSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;

  if (target.files) {
    const files = Array.from(target.files);
    const validFiles = files.filter((file) => validateImageFile(file));

    // Replace existing files instead of adding to them for cleaner behavior
    form.gallery_images = validFiles;
    galleryPreviews.value = [];

    // Generate previews for new files
    validFiles.forEach((file) => {
      generateImagePreview(file, (preview) => {
        galleryPreviews.value.push(preview);
      });
    });

    // Clear the file input to allow re-selection of same files
    target.value = '';
  }
};

const handleGalleryImagesDrop = (event: DragEvent) => {
  event.preventDefault();
  const files = event.dataTransfer?.files;
  if (files) {
    const filesArray = Array.from(files);
    const validFiles = filesArray.filter((file) => validateImageFile(file));

    // Replace existing files instead of adding to them for cleaner behavior
    form.gallery_images = validFiles;
    galleryPreviews.value = [];

    // Generate previews for new files
    validFiles.forEach((file) => {
      generateImagePreview(file, (preview) => {
        galleryPreviews.value.push(preview);
      });
    });
  }
};

const removeGalleryImage = (index: number) => {
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
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Rephrase functions
const rephraseText = async (fieldType: string) => {
  const fieldValue = form[fieldType as keyof typeof form] as string;

  if (!fieldValue || fieldValue.trim().length < 10) {
    error('Please enter at least 10 characters of text to rephrase.');
    return;
  }

  rephraseLoading.value[fieldType] = true;

  try {
    const response = await axios.post(route('graveyard.obituaries.rephrase-text'), {
      text: fieldValue,
      field_type: fieldType,
    });

    if (response.data.rephrased_text) {
      rephraseDialogData.value = {
        fieldType,
        originalText: response.data.original_text,
        rephrasedText: response.data.rephrased_text,
      };
      showRephraseDialog.value = true;
      success('Text rephrased successfully! Review the suggestion below.');
    }
  } catch (err: any) {
    console.error('Rephrase error:', err);
    const errorMessage = err.response?.data?.error || 'Failed to rephrase text. Please try again.';
    error(errorMessage);
  } finally {
    rephraseLoading.value[fieldType] = false;
  }
};

const acceptRephrasedText = () => {
  if (rephraseDialogData.value) {
    const { fieldType, rephrasedText } = rephraseDialogData.value;
    (form as any)[fieldType] = rephrasedText;
    showRephraseDialog.value = false;
    rephraseDialogData.value = null;
  }
};

const rejectRephrasedText = () => {
  showRephraseDialog.value = false;
  rephraseDialogData.value = null;
};

const submit = () => {
  form.post(route('graveyard.obituaries.store'), {
    forceFormData: true,
    onSuccess: () => {
      success('Obituary page created successfully!');
      // Form submitted successfully
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
      error('Please check the form for errors and try again.');
    },
  });
};

const goBack = () => {
  window.history.back();
};
</script>

<template>
  <Head title="Create Obituary" />

  <AppLayout>
    <div class="py-12">
      <div class="mx-auto max-w-4xl sm:px-4 lg:px-6">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center space-x-4">
            <Button variant="outline" size="sm" @click="goBack">
              <ArrowLeft class="mr-2 h-4 w-4" />
              Back
            </Button>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Create Obituary Page</h1>
              <p class="text-gray-600">Create a memorial page for {{ deceasedName || 'the deceased' }}</p>
            </div>
          </div>
        </div>

        <!-- Booking Information Card -->
        <Card v-if="booking" class="mb-6">
          <CardHeader>
            <CardTitle class="text-lg">Booking Information</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
              <div>
                <p class="font-medium text-gray-900">Deceased: {{ deceasedName }}</p>
                <p class="text-gray-600">Booking: {{ booking.booking_reference }}</p>
                <p class="text-gray-600">Type: {{ bookingType }} grave booking</p>
              </div>
              <div>
                <p class="text-gray-600">Date of Death: {{ new Date(booking.died_on).toLocaleDateString() }}</p>
                <p class="text-gray-600">Burial Date: {{ new Date(booking.buried_on).toLocaleDateString() }}</p>
                <p class="text-gray-600">Status: {{ booking.status }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Error message if no booking -->
        <Card v-else class="mb-6 border-yellow-200 bg-yellow-50">
          <CardContent class="pt-6">
            <div class="flex items-center space-x-2">
              <div class="flex-1">
                <p class="text-yellow-800">No booking selected. Please access this page from a confirmed booking.</p>
              </div>
              <Button variant="outline" @click="goBack"> Go Back </Button>
            </div>
          </CardContent>
        </Card>

        <!-- Main Form -->
        <form @submit.prevent="submit" v-if="booking">
          <div class="space-y-6">
            <!-- Service Type Selection -->
            <Card>
              <CardHeader>
                <CardTitle>Service Type</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <div
                    @click="form.service_type = 'basic'"
                    :class="[
                      'cursor-pointer rounded-lg border p-6 transition-all hover:shadow-md',
                      form.service_type === 'basic' ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-200 hover:border-gray-300',
                    ]"
                  >
                    <div class="flex items-start space-x-3">
                      <input
                        type="radio"
                        value="basic"
                        v-model="form.service_type"
                        id="basic"
                        class="mt-1 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                      <div class="flex-1">
                        <div class="flex items-center justify-between">
                          <Label for="basic" class="cursor-pointer text-lg font-semibold">Basic Service</Label>
                          <span class="text-lg font-bold text-blue-600">{{ obituaryPricing.basic.label }}</span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">Standard obituary page with essential features</p>
                        <ul class="mt-3 space-y-1">
                          <li v-for="feature in obituaryPricing.basic.features" :key="feature" class="flex items-center text-sm text-gray-700">
                            <span class="mr-2 text-green-500">✓</span>
                            {{ feature }}
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div
                    @click="form.service_type = 'premium'"
                    :class="[
                      'relative cursor-pointer rounded-lg border p-6 transition-all hover:shadow-md',
                      form.service_type === 'premium' ? 'border-purple-500 bg-purple-50 shadow-md' : 'border-gray-200 hover:border-gray-300',
                    ]"
                  >
                    <div class="absolute top-0 right-0 rounded-tr-lg rounded-bl-lg bg-purple-600 px-3 py-1 text-xs font-medium text-white">
                      RECOMMENDED
                    </div>
                    <div class="flex items-start space-x-3">
                      <input
                        type="radio"
                        value="premium"
                        v-model="form.service_type"
                        id="premium"
                        class="mt-1 h-4 w-4 border-gray-300 text-purple-600 focus:ring-purple-500"
                      />
                      <div class="flex-1">
                        <div class="flex items-center justify-between">
                          <Label for="premium" class="cursor-pointer text-lg font-semibold">Premium Service</Label>
                          <span class="text-lg font-bold text-purple-600">{{ obituaryPricing.premium.label }}</span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">Enhanced memorial page with premium features</p>
                        <ul class="mt-3 space-y-1">
                          <li v-for="feature in obituaryPricing.premium.features" :key="feature" class="flex items-center text-sm text-gray-700">
                            <span class="mr-2 text-green-500">✓</span>
                            {{ feature }}
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Content Section -->
            <Card>
              <CardHeader>
                <CardTitle>Memorial Content</CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div>
                  <div class="flex items-center justify-between">
                    <Label for="biography">Biography</Label>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="rephraseText('biography')"
                      :disabled="rephraseLoading.biography || !form.biography || form.biography.trim().length < 10"
                      class="h-7 px-2 text-xs"
                    >
                      <RefreshCw v-if="rephraseLoading.biography" class="mr-1 h-3 w-3 animate-spin" />
                      <Sparkles v-else class="mr-1 h-3 w-3" />
                      {{ rephraseLoading.biography ? 'Rephrasing...' : 'Rephrase' }}
                    </Button>
                  </div>
                  <Textarea id="biography" v-model="form.biography" placeholder="Write a brief biography of the deceased..." :rows="5" />
                </div>

                <div>
                  <div class="flex items-center justify-between">
                    <Label for="favorite_memory">Favorite Memory</Label>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="rephraseText('favorite_memory')"
                      :disabled="rephraseLoading.favorite_memory || !form.favorite_memory || form.favorite_memory.trim().length < 10"
                      class="h-7 px-2 text-xs"
                    >
                      <RefreshCw v-if="rephraseLoading.favorite_memory" class="mr-1 h-3 w-3 animate-spin" />
                      <Sparkles v-else class="mr-1 h-3 w-3" />
                      {{ rephraseLoading.favorite_memory ? 'Rephrasing...' : 'Rephrase' }}
                    </Button>
                  </div>
                  <Textarea id="favorite_memory" v-model="form.favorite_memory" placeholder="Share a favorite memory..." :rows="5" />
                </div>

                <div>
                  <div class="flex items-center justify-between">
                    <Label for="achievements">Achievements</Label>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="rephraseText('achievements')"
                      :disabled="rephraseLoading.achievements || !form.achievements || form.achievements.trim().length < 10"
                      class="h-7 px-2 text-xs"
                    >
                      <RefreshCw v-if="rephraseLoading.achievements" class="mr-1 h-3 w-3 animate-spin" />
                      <Sparkles v-else class="mr-1 h-3 w-3" />
                      {{ rephraseLoading.achievements ? 'Rephrasing...' : 'Rephrase' }}
                    </Button>
                  </div>
                  <Textarea id="achievements" v-model="form.achievements" placeholder="Notable achievements and accomplishments..." :rows="5" />
                </div>

                <div>
                  <div class="flex items-center justify-between">
                    <Label for="hobbies_interests">Hobbies & Interests</Label>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="rephraseText('hobbies_interests')"
                      :disabled="rephraseLoading.hobbies_interests || !form.hobbies_interests || form.hobbies_interests.trim().length < 10"
                      class="h-7 px-2 text-xs"
                    >
                      <RefreshCw v-if="rephraseLoading.hobbies_interests" class="mr-1 h-3 w-3 animate-spin" />
                      <Sparkles v-else class="mr-1 h-3 w-3" />
                      {{ rephraseLoading.hobbies_interests ? 'Rephrasing...' : 'Rephrase' }}
                    </Button>
                  </div>
                  <Textarea id="hobbies_interests" v-model="form.hobbies_interests" placeholder="Hobbies, interests, and passions..." :rows="5" />
                </div>

                <div>
                  <div class="flex items-center justify-between">
                    <Label for="notes">Family Notes & Messages</Label>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="rephraseText('notes')"
                      :disabled="rephraseLoading.notes || !form.notes || form.notes.trim().length < 10"
                      class="h-7 px-2 text-xs"
                    >
                      <RefreshCw v-if="rephraseLoading.notes" class="mr-1 h-3 w-3 animate-spin" />
                      <Sparkles v-else class="mr-1 h-3 w-3" />
                      {{ rephraseLoading.notes ? 'Rephrasing...' : 'Rephrase' }}
                    </Button>
                  </div>
                  <Textarea
                    id="notes"
                    v-model="form.notes"
                    placeholder="Family thoughts, funeral mass details, months mind mass timing and place, condolence messages from family, etc..."
                    :rows="5"
                    class="resize-y"
                  />
                  <p class="text-muted-foreground mt-1 text-sm">
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
                    <!-- Image Preview -->
                    <div>
                      <div class="aspect-square overflow-hidden rounded-lg border-2 border-dashed border-gray-200 bg-gray-50">
                        <div v-if="profileImagePreview" class="relative h-full">
                          <img :src="profileImagePreview" alt="Profile preview" class="h-full w-full object-cover" />
                          <button
                            type="button"
                            @click="clearProfileImage"
                            class="absolute top-2 right-2 rounded-full bg-red-600 p-1 text-white shadow-md hover:bg-red-700"
                          >
                            <X class="h-3 w-3" />
                          </button>
                        </div>
                        <div
                          v-else
                          class="flex h-full cursor-pointer flex-col items-center justify-center transition-colors hover:bg-gray-100"
                          @drop="handleProfileImageDrop"
                          @dragover.prevent
                          @dragenter.prevent
                          @click="profileImageRef?.click()"
                        >
                          <Upload class="mx-auto mb-2 h-6 w-6 text-gray-400" />
                          <p class="text-sm text-gray-600">Click or drag image here</p>
                          <p class="mt-1 text-xs text-gray-500">Max 2MB • JPG, PNG, GIF</p>
                        </div>
                      </div>
                      <input ref="profileImageRef" type="file" accept="image/*" class="hidden" @change="handleProfileImageSelect" />

                      <div v-if="form.profile_image" class="mt-2 rounded-lg border border-green-200 bg-green-50 p-3">
                        <div class="flex items-center">
                          <FileImage class="mr-2 h-4 w-4 text-green-600" />
                          <span class="text-sm text-green-700">{{ form.profile_image.name }}</span>
                          <span class="ml-auto text-xs text-green-600">{{ formatFileSize(form.profile_image.size) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Gallery Images (Premium Only) -->
                <div v-if="form.service_type === 'premium'" class="border-t pt-6">
                  <Label class="flex items-center text-base font-semibold">
                    Gallery Photos
                    <span class="ml-2 rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-700">Premium Feature</span>
                  </Label>
                  <p class="mb-4 text-sm text-gray-600">Additional photos for the memorial gallery</p>

                  <!-- Gallery Previews -->
                  <div v-if="galleryPreviews.length" style="margin-bottom: 16px">
                    <p style="font-size: 14px; font-weight: 600; color: #7c3aed; margin-bottom: 12px">
                      {{ galleryPreviews.length }} photo{{ galleryPreviews.length > 1 ? 's' : '' }} selected:
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; max-width: 100%">
                      <div
                        v-for="(preview, index) in galleryPreviews"
                        :key="`gallery-${index}`"
                        style="
                          position: relative;
                          aspect-ratio: 1;
                          border-radius: 8px;
                          overflow: hidden;
                          border: 2px solid #a855f7;
                          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                        "
                      >
                        <img :src="preview" :alt="`Gallery ${index + 1}`" style="width: 100%; height: 100%; object-fit: cover; display: block" />
                        <button
                          type="button"
                          @click="removeGalleryImage(index)"
                          style="
                            position: absolute;
                            top: 4px;
                            right: 4px;
                            background: rgba(239, 68, 68, 0.9);
                            color: white;
                            border: none;
                            border-radius: 50%;
                            width: 24px;
                            height: 24px;
                            cursor: pointer;
                            font-size: 14px;
                            line-height: 1;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                          "
                          title="Remove image"
                        >
                          ×
                        </button>
                        <span
                          style="
                            position: absolute;
                            bottom: 4px;
                            left: 4px;
                            background: rgba(0, 0, 0, 0.7);
                            color: white;
                            padding: 2px 6px;
                            font-size: 10px;
                            border-radius: 4px;
                          "
                        >
                          New
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- No previews message -->
                  <div v-else-if="form.gallery_images.length" class="mb-4 rounded border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-800">
                    Files selected but no previews generated. Check console for errors.
                  </div>

                  <!-- Upload Area -->
                  <div
                    class="cursor-pointer rounded-lg border-2 border-dashed border-purple-300 p-8 text-center transition-all hover:border-purple-400 hover:bg-purple-50"
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
                  <div v-if="form.gallery_images.length" class="mt-3 rounded-lg border border-purple-200 bg-purple-50 p-4">
                    <p class="mb-2 text-sm font-medium text-purple-800">{{ form.gallery_images.length }} photos selected:</p>
                    <div class="space-y-1">
                      <div v-for="(file, index) in form.gallery_images" :key="index" class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                          <FileImage class="mr-2 h-3 w-3 text-purple-600" />
                          <span class="text-purple-700">{{ file.name }}</span>
                        </div>
                        <span class="text-xs text-purple-600">{{ formatFileSize(file.size) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Audio Message (Premium Only) -->
                <div v-if="form.service_type === 'premium'">
                  <Label class="flex items-center text-base font-semibold">
                    Audio Message
                    <span class="ml-2 rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-700">Premium Feature</span>
                  </Label>
                  <div class="mt-2">
                    <Button type="button" variant="outline" @click="audioMessageRef?.click()" class="w-full justify-start">
                      <Music class="mr-2 h-4 w-4" />
                      {{ form.audio_message ? form.audio_message.name : 'Choose audio message' }}
                    </Button>
                    <input ref="audioMessageRef" type="file" accept="audio/*" class="hidden" @change="handleAudioMessageSelect" />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">Supported formats: MP3, WAV, M4A (max 10MB)</p>

                  <div v-if="form.audio_message" class="mt-2 rounded-lg border border-purple-200 bg-purple-50 p-3">
                    <div class="flex items-center">
                      <Music class="mr-2 h-4 w-4 text-purple-600" />
                      <span class="text-sm text-purple-700">{{ form.audio_message.name }}</span>
                      <span class="ml-auto text-xs text-purple-600">{{ formatFileSize(form.audio_message.size) }}</span>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Basic Background Options (Available for all) -->
            <Card v-if="form.service_type === 'basic'">
              <CardHeader>
                <CardTitle class="flex items-center">
                  <Palette class="mr-2 h-5 w-5" />
                  Background Style
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div>
                  <Label for="background_style">Choose Background</Label>
                  <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div
                      v-for="background in props.basicBackgrounds"
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
                        </div>
                      </div>
                    </div>
                  </div>
                  <p class="mt-3 text-xs text-gray-500">Choose from our selection of respectful backgrounds</p>

                </div>
              </CardContent>
            </Card>

            <!-- Premium Customization Section -->
            <Card v-if="form.service_type === 'premium'">
              <CardHeader>
                <CardTitle class="flex items-center">
                  <Palette class="mr-2 h-5 w-5" />
                  Customization
                </CardTitle>
              </CardHeader>
              <CardContent class="space-y-4">
                <div>
                  <Label for="theme_color">Theme Color</Label>
                  <div class="mt-1 flex items-center space-x-2">
                    <input id="theme_color" v-model="form.theme_color" type="color" class="h-10 w-20 cursor-pointer rounded border border-gray-300" />
                    <Input v-model="form.theme_color" class="flex-1" />
                  </div>
                </div>

                <div>
                  <Label for="background_style">Background Style</Label>
                  <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div
                      v-for="background in props.premiumBackgrounds"
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
                <div v-if="form.service_type === 'premium'" class="space-y-4">
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

                <!-- Information for Basic Users -->
                <div v-if="form.service_type === 'basic'" class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-3">
                  <div class="flex items-start space-x-2">
                    <div class="mt-0.5 text-blue-600">
                      <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                          fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                          clip-rule="evenodd"
                        ></path>
                      </svg>
                    </div>
                    <div class="text-sm">
                      <p class="font-medium text-blue-800">Premium Features Available</p>
                      <p class="text-blue-700">
                        Premium service includes condolence collection, memory sharing, gallery photos, and audio messages.
                      </p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Payment Summary -->
            <Card>
              <CardHeader>
                <CardTitle class="flex items-center justify-between">
                  <span>Payment Summary</span>
                  <span class="text-2xl font-bold" :class="form.service_type === 'premium' ? 'text-purple-600' : 'text-blue-600'">
                    {{ selectedServicePrice.label }}
                  </span>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="rounded-lg bg-gray-50 p-4">
                  <div class="mb-2 flex items-center justify-between">
                    <span class="font-medium">{{ form.service_type === 'premium' ? 'Premium' : 'Basic' }} Obituary Service</span>
                    <span class="font-semibold">{{ selectedServicePrice.label }}</span>
                  </div>
                  <div class="mb-3 text-sm text-gray-600">
                    <p>• Immediate obituary page creation</p>
                    <p>• QR code generation for grave placement</p>
                    <p v-if="form.service_type === 'premium'">• Premium features and customization</p>
                  </div>
                  <div class="flex items-center justify-between border-t pt-3">
                    <span class="text-lg font-semibold">Total Amount:</span>
                    <span class="text-xl font-bold" :class="form.service_type === 'premium' ? 'text-purple-600' : 'text-blue-600'">
                      {{ selectedServicePrice.label }}
                    </span>
                  </div>
                </div>
                <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-3">
                  <div class="flex items-start space-x-2">
                    <div class="mt-0.5 text-blue-600">
                      <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                          fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                          clip-rule="evenodd"
                        ></path>
                      </svg>
                    </div>
                    <div class="text-sm">
                      <p class="font-medium text-blue-800">Payment Process</p>
                      <p class="text-blue-700">
                        After creating the obituary, you'll be redirected to make the payment. The obituary page will be activated once payment is
                        completed.
                      </p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Submit Section -->
            <div class="flex items-center justify-between">
              <Button type="button" variant="outline" @click="goBack"> Cancel </Button>
              <div class="flex items-center space-x-4">
                <div class="text-right">
                  <p class="text-sm text-gray-600">You will pay</p>
                  <p class="text-lg font-bold" :class="form.service_type === 'premium' ? 'text-purple-600' : 'text-blue-600'">
                    {{ selectedServicePrice.label }}
                  </p>
                </div>
                <Button
                  type="submit"
                  :disabled="form.processing"
                  class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-3 text-lg text-white hover:from-purple-700 hover:to-blue-700"
                >
                  {{ form.processing ? 'Creating...' : 'Create & Pay' }}
                </Button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Rephrase Confirmation Dialog -->
    <Dialog :open="showRephraseDialog" @update:open="showRephraseDialog = $event">
      <DialogContent class="max-h-[80vh] max-w-2xl overflow-y-auto">
        <DialogHeader>
          <DialogTitle class="flex items-center">
            <Sparkles class="mr-2 h-5 w-5 text-purple-600" />
            Text Rephrasing Result
          </DialogTitle>
          <DialogDescription> Review the rephrased text and choose whether to use it or keep your original text. </DialogDescription>
        </DialogHeader>

        <div v-if="rephraseDialogData" class="space-y-4">
          <!-- Original Text -->
          <div>
            <Label class="text-sm font-semibold text-gray-700">Original Text:</Label>
            <div class="mt-1 rounded-lg border bg-gray-50 p-3">
              <p class="text-sm whitespace-pre-wrap text-gray-800">{{ rephraseDialogData.originalText }}</p>
            </div>
          </div>

          <!-- Rephrased Text -->
          <div>
            <Label class="text-sm font-semibold text-green-700">Rephrased Text:</Label>
            <div class="mt-1 rounded-lg border border-green-200 bg-green-50 p-3">
              <p class="text-sm whitespace-pre-wrap text-gray-800">{{ rephraseDialogData.rephrasedText }}</p>
            </div>
          </div>
        </div>

        <DialogFooter class="flex justify-between sm:justify-between">
          <Button type="button" variant="outline" @click="rejectRephrasedText"> Keep Original </Button>
          <Button type="button" @click="acceptRephrasedText" class="bg-green-600 hover:bg-green-700"> Use Rephrased Text </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
