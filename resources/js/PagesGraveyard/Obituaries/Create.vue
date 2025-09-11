<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, FileImage, Music, Palette, Upload, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

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

interface Props {
  booking?: PermanentGraveBooking | TemporaryGraveBooking;
  bookingType?: 'permanent' | 'temporary';
}

const props = defineProps<Props>();

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
  premium: { amount: 1500, label: '₹1,500', features: ['All basic features', 'Gallery photos', 'Condolence collection', 'Memory sharing', 'Audio messages', 'Custom themes', 'Priority support'] }
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
  profile_image: null as File | null,
  gallery_images: [] as File[],
  audio_message: null as File | null,
  theme_color: '#6366f1',
  background_style: 'plain' as 'plain' | 'gradient' | 'pattern',
  allow_condolences: false as boolean, // Premium feature - disabled by default
  allow_memory_sharing: false as boolean, // Premium feature - disabled by default
  is_public: true,
});

// Computed properties
const selectedServicePrice = computed(() => obituaryPricing[form.service_type]);

// Watch for service type changes to enable/disable premium features
watch(() => form.service_type, (newType) => {
  if (newType === 'basic') {
    // Disable premium features for basic service
    form.allow_condolences = false;
    form.allow_memory_sharing = false;
    form.gallery_images = [];
    form.audio_message = null;
    form.theme_color = '#6366f1';
    form.background_style = 'plain';
  } else {
    // Enable premium features
    form.allow_condolences = true;
    form.allow_memory_sharing = true;
  }
});

const profileImageRef = ref<HTMLInputElement>();
const galleryImagesRef = ref<HTMLInputElement>();
const audioMessageRef = ref<HTMLInputElement>();

// Image preview states
const profileImagePreview = ref<string | null>(null);
const galleryPreviews = ref<string[]>([]);

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
    const validFiles = files.filter(file => validateImageFile(file));
    form.gallery_images = [...form.gallery_images, ...validFiles];
    
    // Generate previews for new files
    validFiles.forEach(file => {
      generateImagePreview(file, (preview) => {
        galleryPreviews.value.push(preview);
      });
    });
  }
};

const handleGalleryImagesDrop = (event: DragEvent) => {
  event.preventDefault();
  const files = event.dataTransfer?.files;
  if (files) {
    const filesArray = Array.from(files);
    const validFiles = filesArray.filter(file => validateImageFile(file));
    form.gallery_images = [...form.gallery_images, ...validFiles];
    
    // Generate previews for new files
    validFiles.forEach(file => {
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
    alert('Please select a valid image file (JPG, PNG, or GIF)');
    return false;
  }
  
  if (file.size > maxSize) {
    alert('File size must be less than 2MB');
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

const submit = () => {
  form.post(route('graveyard.obituaries.store'), {
    forceFormData: true,
    onSuccess: () => {
      // Form submitted successfully
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
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
              <Button variant="outline" @click="goBack">
                Go Back
              </Button>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div 
                    @click="form.service_type = 'basic'"
                    :class="[
                      'p-6 border rounded-lg cursor-pointer transition-all hover:shadow-md',
                      form.service_type === 'basic' ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-200 hover:border-gray-300'
                    ]"
                  >
                    <div class="flex items-start space-x-3">
                      <input 
                        type="radio" 
                        value="basic" 
                        v-model="form.service_type" 
                        id="basic" 
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 mt-1"
                      />
                      <div class="flex-1">
                        <div class="flex items-center justify-between">
                          <Label for="basic" class="font-semibold text-lg cursor-pointer">Basic Service</Label>
                          <span class="font-bold text-lg text-blue-600">{{ obituaryPricing.basic.label }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Standard obituary page with essential features</p>
                        <ul class="mt-3 space-y-1">
                          <li v-for="feature in obituaryPricing.basic.features" :key="feature" class="text-sm text-gray-700 flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            {{ feature }}
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div 
                    @click="form.service_type = 'premium'"
                    :class="[
                      'p-6 border rounded-lg cursor-pointer transition-all hover:shadow-md relative',
                      form.service_type === 'premium' ? 'border-purple-500 bg-purple-50 shadow-md' : 'border-gray-200 hover:border-gray-300'
                    ]"
                  >
                    <div class="absolute top-0 right-0 bg-purple-600 text-white px-3 py-1 rounded-tr-lg rounded-bl-lg text-xs font-medium">
                      RECOMMENDED
                    </div>
                    <div class="flex items-start space-x-3">
                      <input 
                        type="radio" 
                        value="premium" 
                        v-model="form.service_type" 
                        id="premium" 
                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 mt-1"
                      />
                      <div class="flex-1">
                        <div class="flex items-center justify-between">
                          <Label for="premium" class="font-semibold text-lg cursor-pointer">Premium Service</Label>
                          <span class="font-bold text-lg text-purple-600">{{ obituaryPricing.premium.label }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Enhanced memorial page with premium features</p>
                        <ul class="mt-3 space-y-1">
                          <li v-for="feature in obituaryPricing.premium.features" :key="feature" class="text-sm text-gray-700 flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
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
                  <Label for="biography">Biography</Label>
                  <Textarea
                    id="biography"
                    v-model="form.biography"
                    placeholder="Write a brief biography of the deceased..."
                    rows="4"
                  />
                </div>

                <div>
                  <Label for="favorite_memory">Favorite Memory</Label>
                  <Textarea
                    id="favorite_memory"
                    v-model="form.favorite_memory"
                    placeholder="Share a favorite memory..."
                    rows="3"
                  />
                </div>

                <div>
                  <Label for="achievements">Achievements</Label>
                  <Textarea
                    id="achievements"
                    v-model="form.achievements"
                    placeholder="Notable achievements and accomplishments..."
                    rows="3"
                  />
                </div>

                <div>
                  <Label for="hobbies_interests">Hobbies & Interests</Label>
                  <Textarea
                    id="hobbies_interests"
                    v-model="form.hobbies_interests"
                    placeholder="Hobbies, interests, and passions..."
                    rows="3"
                  />
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
                  <p class="text-sm text-gray-600 mb-3">Main photo for the memorial page</p>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Image Preview -->
                    <div>
                      <div class="aspect-square bg-gray-50 rounded-lg overflow-hidden border-2 border-dashed border-gray-200">
                        <div v-if="profileImagePreview" class="relative h-full">
                          <img 
                            :src="profileImagePreview"
                            alt="Profile preview"
                            class="w-full h-full object-cover"
                          />
                          <button
                            type="button"
                            @click="clearProfileImage"
                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 shadow-md hover:bg-red-700"
                          >
                            <X class="w-3 h-3" />
                          </button>
                        </div>
                        <div 
                          v-else
                          class="h-full flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 transition-colors"
                          @drop="handleProfileImageDrop"
                          @dragover.prevent
                          @dragenter.prevent
                          @click="profileImageRef?.click()"
                        >
                          <Upload class="mx-auto h-6 w-6 text-gray-400 mb-2" />
                          <p class="text-sm text-gray-600">Click or drag image here</p>
                          <p class="text-xs text-gray-500 mt-1">Max 2MB • JPG, PNG, GIF</p>
                        </div>
                      </div>
                      <input
                        ref="profileImageRef"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleProfileImageSelect"
                      />
                      
                      <div v-if="form.profile_image" class="bg-green-50 border border-green-200 rounded-lg p-3 mt-2">
                        <div class="flex items-center">
                          <FileImage class="h-4 w-4 text-green-600 mr-2" />
                          <span class="text-sm text-green-700">{{ form.profile_image.name }}</span>
                          <span class="text-xs text-green-600 ml-auto">{{ formatFileSize(form.profile_image.size) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Gallery Images (Premium Only) -->
                <div v-if="form.service_type === 'premium'" class="border-t pt-6">
                  <Label class="text-base font-semibold flex items-center">
                    Gallery Photos 
                    <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">Premium Feature</span>
                  </Label>
                  <p class="text-sm text-gray-600 mb-4">Additional photos for the memorial gallery</p>
                  
                  <!-- Gallery Previews -->
                  <div v-if="galleryPreviews.length" class="mb-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                      <div 
                        v-for="(preview, index) in galleryPreviews" 
                        :key="`gallery-${index}`"
                        class="relative group aspect-square"
                      >
                        <img 
                          :src="preview"
                          :alt="`Gallery ${index + 1}`"
                          class="w-full h-full object-cover rounded-lg border-2 border-purple-300 shadow-sm"
                        />
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                          <button
                            type="button"
                            @click="removeGalleryImage(index)"
                            class="bg-red-600 text-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg hover:bg-red-700"
                          >
                            <X class="w-4 h-4" />
                          </button>
                        </div>
                        <span class="absolute bottom-1 left-1 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded">New</span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Upload Area -->
                  <div 
                    class="border-2 border-dashed border-purple-300 rounded-lg p-8 text-center hover:border-purple-400 hover:bg-purple-50 transition-all cursor-pointer"
                    @drop="handleGalleryImagesDrop"
                    @dragover.prevent
                    @dragenter.prevent
                    @click="galleryImagesRef?.click()"
                  >
                    <Upload class="mx-auto h-8 w-8 text-purple-400 mb-3" />
                    <p class="text-sm text-gray-600 font-medium">Drop multiple images here or click to browse</p>
                    <p class="text-xs text-gray-500 mt-2">Max 2MB each • JPG, PNG, GIF • Multiple selection allowed</p>
                  </div>
                  <input
                    ref="galleryImagesRef"
                    type="file"
                    accept="image/*"
                    multiple
                    class="hidden"
                    @change="handleGalleryImagesSelect"
                  />
                  <div v-if="form.gallery_images.length" class="bg-purple-50 border border-purple-200 rounded-lg p-4 mt-3">
                    <p class="text-sm font-medium text-purple-800 mb-2">{{ form.gallery_images.length }} photos selected:</p>
                    <div class="space-y-1">
                      <div v-for="(file, index) in form.gallery_images" :key="index" class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                          <FileImage class="h-3 w-3 text-purple-600 mr-2" />
                          <span class="text-purple-700">{{ file.name }}</span>
                        </div>
                        <span class="text-purple-600 text-xs">{{ formatFileSize(file.size) }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Audio Message (Premium Only) -->
                <div v-if="form.service_type === 'premium'">
                  <Label class="text-base font-semibold flex items-center">
                    Audio Message
                    <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">Premium Feature</span>
                  </Label>
                  <div class="mt-2">
                    <Button 
                      type="button" 
                      variant="outline" 
                      @click="audioMessageRef?.click()"
                      class="w-full justify-start"
                    >
                      <Music class="mr-2 h-4 w-4" />
                      {{ form.audio_message ? form.audio_message.name : 'Choose audio message' }}
                    </Button>
                    <input
                      ref="audioMessageRef"
                      type="file"
                      accept="audio/*"
                      class="hidden"
                      @change="handleAudioMessageSelect"
                    />
                  </div>
                  <p class="text-xs text-gray-500 mt-1">Supported formats: MP3, WAV, M4A (max 10MB)</p>
                  
                  <div v-if="form.audio_message" class="bg-purple-50 border border-purple-200 rounded-lg p-3 mt-2">
                    <div class="flex items-center">
                      <Music class="h-4 w-4 text-purple-600 mr-2" />
                      <span class="text-sm text-purple-700">{{ form.audio_message.name }}</span>
                      <span class="text-xs text-purple-600 ml-auto">{{ formatFileSize(form.audio_message.size) }}</span>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Customization Section (Premium only) -->
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
                  <div class="flex items-center space-x-2 mt-1">
                    <input
                      id="theme_color"
                      v-model="form.theme_color"
                      type="color"
                      class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                    />
                    <Input v-model="form.theme_color" class="flex-1" />
                  </div>
                </div>

                <div>
                  <Label for="background_style">Background Style</Label>
                  <Select v-model="form.background_style">
                    <SelectTrigger>
                      <SelectValue placeholder="Choose background style" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="plain">Plain</SelectItem>
                      <SelectItem value="gradient">Gradient</SelectItem>
                      <SelectItem value="pattern">Pattern</SelectItem>
                    </SelectContent>
                  </Select>
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
                      <Label>Allow Condolences <span class="text-purple-600 text-xs font-medium">(Premium Feature)</span></Label>
                      <p class="text-sm text-gray-600">Allow visitors to leave condolence messages</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input 
                        type="checkbox" 
                        v-model="form.allow_condolences" 
                        class="sr-only peer"
                      >
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                  </div>

                  <div class="flex items-center justify-between">
                    <div>
                      <Label>Allow Memory Sharing <span class="text-purple-600 text-xs font-medium">(Premium Feature)</span></Label>
                      <p class="text-sm text-gray-600">Allow visitors to share memories</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input 
                        type="checkbox" 
                        v-model="form.allow_memory_sharing" 
                        class="sr-only peer"
                      >
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                  </div>
                </div>

                <!-- Basic Features Available to All -->
                <div class="flex items-center justify-between">
                  <div>
                    <Label>Public Page</Label>
                    <p class="text-sm text-gray-600">Make the obituary page publicly accessible</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                      type="checkbox" 
                      v-model="form.is_public" 
                      class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                  </label>
                </div>

                <!-- Information for Basic Users -->
                <div v-if="form.service_type === 'basic'" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                  <div class="flex items-start space-x-2">
                    <div class="text-blue-600 mt-0.5">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div class="text-sm">
                      <p class="font-medium text-blue-800">Premium Features</p>
                      <p class="text-blue-700">Upgrade to Premium to enable condolence collection, memory sharing, gallery photos, and audio messages.</p>
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
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="flex justify-between items-center mb-2">
                    <span class="font-medium">{{ form.service_type === 'premium' ? 'Premium' : 'Basic' }} Obituary Service</span>
                    <span class="font-semibold">{{ selectedServicePrice.label }}</span>
                  </div>
                  <div class="text-sm text-gray-600 mb-3">
                    <p>• Immediate obituary page creation</p>
                    <p>• QR code generation for grave placement</p>
                    <p v-if="form.service_type === 'premium'">• Premium features and customization</p>
                  </div>
                  <div class="border-t pt-3 flex justify-between items-center">
                    <span class="font-semibold text-lg">Total Amount:</span>
                    <span class="font-bold text-xl" :class="form.service_type === 'premium' ? 'text-purple-600' : 'text-blue-600'">
                      {{ selectedServicePrice.label }}
                    </span>
                  </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                  <div class="flex items-start space-x-2">
                    <div class="text-blue-600 mt-0.5">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div class="text-sm">
                      <p class="font-medium text-blue-800">Payment Process</p>
                      <p class="text-blue-700">After creating the obituary, you'll be redirected to make the payment. The obituary page will be activated once payment is completed.</p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Submit Section -->
            <div class="flex justify-between items-center">
              <Button type="button" variant="outline" @click="goBack">
                Cancel
              </Button>
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
                  class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 text-lg"
                >
                  {{ form.processing ? 'Creating...' : 'Create & Pay' }}
                </Button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>