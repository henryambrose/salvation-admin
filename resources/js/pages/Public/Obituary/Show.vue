<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Heart, MessageCircle, Share2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface ObituaryPage {
  id: number;
  uuid: string;
  // service_type: 'basic' | 'premium';
  is_public: boolean;
  is_active: boolean;
  view_count: number;
  qr_scan_count: number;
  profile_image?: string;
  biography?: string;
  favorite_memory?: string;
  achievements?: string;
  hobbies_interests?: string;
  notes?: string;
  theme_color?: string;
  background_style?: string;
  allow_condolences: boolean;
  allow_memory_sharing: boolean;
  created_at: string;
  gallery_images?: string[];
  audio_message?: string;
  permanent_grave_booking?: {
    id: number;
    booking_reference: string;
    valid_member: {
      first_name: string;
      last_name: string;
      date_of_birth?: string;
      date_of_death?: string;
    };
  };
  temporary_grave_booking?: {
    id: number;
    booking_reference: string;
    dead_first_name: string;
    dead_last_name: string;
    date_of_birth?: string;
    date_of_death?: string;
  };
  condolences?: {
    id: number;
    name: string;
    message: string;
    created_at: string;
  }[];
}

interface Props {
  obituary: ObituaryPage;
  deceasedName: string;
  canSubmitCondolence: boolean;
  canShareMemory: boolean;
  condolences?: any[];
  backgroundStyle?: Record<string, any>;
}

const props = defineProps<Props>();

// Condolence form state
const condolenceForm = ref({
  visitor_name: '',
  visitor_email: '',
  visitor_phone: '',
  relationship: '',
  message: '',
});

const isSubmittingCondolence = ref(false);
const condolenceMessage = ref('');
const condolenceError = ref('');

// Use deceased name from props

// Get dates
const dateOfBirth = computed(() => {
  if (props.obituary.permanent_grave_booking?.valid_member?.date_of_birth) {
    return new Date(props.obituary.permanent_grave_booking.valid_member.date_of_birth).toLocaleDateString();
  }
  if (props.obituary.temporary_grave_booking?.date_of_birth) {
    return new Date(props.obituary.temporary_grave_booking.date_of_birth).toLocaleDateString();
  }
  return null;
});

const dateOfDeath = computed(() => {
  if (props.obituary.permanent_grave_booking?.valid_member?.date_of_death) {
    return new Date(props.obituary.permanent_grave_booking.valid_member.date_of_death).toLocaleDateString();
  }
  if (props.obituary.temporary_grave_booking?.date_of_death) {
    return new Date(props.obituary.temporary_grave_booking.date_of_death).toLocaleDateString();
  }
  return null;
});

const shareUrl = computed(() => window.location.href);

// Submit condolence function
const submitCondolence = async () => {
  if (!props.canSubmitCondolence) {
    condolenceError.value = 'Condolences are not available for this obituary.';
    return;
  }

  if (!condolenceForm.value.visitor_name.trim() || !condolenceForm.value.message.trim() || !condolenceForm.value.visitor_phone.trim()) {
    condolenceError.value = 'Please provide your name, phone number, and message.';
    return;
  }

  // Validate Indian phone number format
  const phoneRegex = /^[6-9]\d{9}$/;
  if (!phoneRegex.test(condolenceForm.value.visitor_phone.trim())) {
    condolenceError.value = 'Please enter a valid Indian mobile number (10 digits starting with 6, 7, 8, or 9).';
    return;
  }

  isSubmittingCondolence.value = true;
  condolenceError.value = '';
  condolenceMessage.value = '';

  try {
    const response = await fetch(`/obituary/${props.obituary.uuid}/condolence`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify(condolenceForm.value),
    });

    const data = await response.json();

    if (response.ok) {
      condolenceMessage.value = data.message || 'Thank you for your condolence. It will be reviewed and published shortly.';
      // Reset form
      condolenceForm.value = {
        visitor_name: '',
        visitor_email: '',
        visitor_phone: '',
        relationship: '',
        message: '',
      };
    } else {
      condolenceError.value = data.error || 'Failed to submit condolence. Please try again.';
      if (data.errors) {
        // Show validation errors
        const errorMessages = Object.values(data.errors).flat();
        condolenceError.value = errorMessages.join(' ');
      }
    }
  } catch (error) {
    console.error('Condolence submission error:', error);
    condolenceError.value = 'Network error. Please check your connection and try again.';
  } finally {
    isSubmittingCondolence.value = false;
  }
};

const getBackgroundStyle = computed(() => {
  // Use the background style from the backend service if available, otherwise fallback to legacy logic
  if (props.backgroundStyle) {
    return props.backgroundStyle;
  }

  // Legacy fallback logic for backward compatibility
  const style: any = {
    backgroundColor: props.obituary.theme_color || '#ffffff',
  };

  if (props.obituary.background_style === 'gradient') {
    style.backgroundImage = `linear-gradient(135deg, ${props.obituary.theme_color || '#ffffff'}, #f8f9fa)`;
  } else if (props.obituary.background_style === 'pattern') {
    style.backgroundImage =
      "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f0f0f0' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")";
  } else if (props.obituary.background_style === 'floral' || props.obituary.background_style === 'memorial') {
    style.backgroundImage = `url('/storage/backgrounds/memorial-sunset.png')`;
    style.backgroundSize = 'cover';
    style.backgroundPosition = 'center';
    style.backgroundRepeat = 'no-repeat';
    style.backgroundColor = '#f8f9fa'; // Fallback color
  }

  return style;
});

// Open gallery image in new tab
const openImageInNewTab = (image: string) => {
  const url = image.startsWith('http') ? image : `/storage/${image}`;
  window.open(url, '_blank');
};

const shareObituaryPage = () => {
  if (navigator.share) {
    navigator.share({
      title: `In memory of ${props.deceasedName}`,
      text: `Remember ${props.deceasedName} - Memorial Page`,
      url: shareUrl.value,
    });
  } else {
    navigator.clipboard.writeText(shareUrl.value);
    // Could show a toast message here
  }
};
</script>

<template>
  <Head :title="`In memory of ${props.deceasedName}`" />

  <div class="min-h-screen" :style="getBackgroundStyle">
    <!-- Header Section -->
    <div class="relative px-4 py-16">
      <div class="mx-auto max-w-4xl text-center">
        <!-- Profile Image -->
        <div v-if="obituary.profile_image" class="mb-8">
          <img
            :src="obituary.profile_image.startsWith('http') ? obituary.profile_image : `/storage/${obituary.profile_image}`"
            :alt="props.deceasedName"
            class="mx-auto h-48 w-48 rounded-full border-4 border-white object-cover shadow-2xl"
          />
        </div>

        <!-- Name and Dates -->
        <h1 class="mb-4 font-serif text-4xl font-bold text-gray-900 md:text-6xl">
          {{ props.deceasedName }}
        </h1>

        <div v-if="dateOfBirth || dateOfDeath" class="mb-8 text-xl text-gray-600">
          <span v-if="dateOfBirth">{{ dateOfBirth }}</span>
          <span v-if="dateOfBirth && dateOfDeath"> - </span>
          <span v-if="dateOfDeath">{{ dateOfDeath }}</span>
        </div>

        <!-- Share Button -->
        <button
          @click="shareObituaryPage"
          class="inline-flex items-center rounded-full bg-white/20 px-6 py-3 text-gray-700 shadow-lg backdrop-blur-sm transition-all duration-200 hover:bg-white/30"
        >
          <Share2 class="mr-2 h-5 w-5" />
          Share Memorial
        </button>
      </div>
    </div>

    <!-- Content Section -->
    <div class="mx-auto max-w-4xl px-4 pb-16">
      <div class="rounded-2xl bg-white/80 p-8 shadow-2xl backdrop-blur-sm md:p-12">
        <!-- Biography -->
        <section v-if="obituary.biography" class="mb-12">
          <h2 class="mb-6 flex items-center font-serif text-3xl font-bold text-gray-900">
            <Heart class="mr-3 h-8 w-8 text-red-500" />
            Life Story
          </h2>
          <div class="prose prose-lg max-w-none leading-relaxed text-gray-700">
            <p class="whitespace-pre-line">{{ obituary.biography }}</p>
          </div>
        </section>

        <!-- Favorite Memory -->
        <section v-if="obituary.favorite_memory" class="mb-12">
          <h2 class="mb-6 font-serif text-3xl font-bold text-gray-900">Cherished Memories</h2>
          <div class="prose prose-lg max-w-none leading-relaxed text-gray-700">
            <p class="whitespace-pre-line">{{ obituary.favorite_memory }}</p>
          </div>
        </section>

        <!-- Achievements -->
        <section v-if="obituary.achievements" class="mb-12">
          <h2 class="mb-6 font-serif text-3xl font-bold text-gray-900">Achievements & Legacy</h2>
          <div class="prose prose-lg max-w-none leading-relaxed text-gray-700">
            <p class="whitespace-pre-line">{{ obituary.achievements }}</p>
          </div>
        </section>

        <!-- Hobbies & Interests -->
        <section v-if="obituary.hobbies_interests" class="mb-12">
          <h2 class="mb-6 font-serif text-3xl font-bold text-gray-900">Hobbies & Interests</h2>
          <div class="prose prose-lg max-w-none leading-relaxed text-gray-700">
            <p class="whitespace-pre-line">{{ obituary.hobbies_interests }}</p>
          </div>
        </section>

        <!-- Family Notes & Messages -->
        <section v-if="obituary.notes" class="mb-12">
          <h2 class="mb-6 font-serif text-3xl font-bold text-gray-900">Family Notes & Messages</h2>
          <div class="prose prose-lg max-w-none leading-relaxed text-gray-700">
            <p class="whitespace-pre-line">{{ obituary.notes }}</p>
          </div>
        </section>

        <!-- Gallery -->
        <section v-if="obituary.gallery_images?.length" class="mb-12">
          <h2 class="mb-6 font-serif text-3xl font-bold text-gray-900">Photo Gallery</h2>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <img
              v-for="(image, index) in obituary.gallery_images"
              :key="index"
              :src="image.startsWith('http') ? image : `/storage/${image}`"
              :alt="`Memory ${index + 1}`"
              class="h-48 w-full rounded-lg object-cover shadow-md transition-shadow duration-200 hover:shadow-xl cursor-pointer"
              @click="openImageInNewTab(image)"
            />
          </div>
        </section>

        <!-- Audio Message -->
        <section v-if="obituary.audio_message" class="mb-12">
          <h2 class="mb-6 font-serif text-3xl font-bold text-gray-900">Audio Message</h2>
          <audio controls class="w-full max-w-[448px]">
            <source
              :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`"
              type="audio/mpeg"
            />
            Your browser does not support the audio element.
          </audio>
        </section>

        <!-- Condolences Section -->
        <section v-if="obituary.allow_condolences && canSubmitCondolence" class="mb-12">
          <h2 class="mb-6 flex items-center font-serif text-3xl font-bold text-gray-900">
            <MessageCircle class="mr-3 h-8 w-8 text-blue-500" />
            Messages of Love
          </h2>

          <!-- Condolence Form -->
          <div class="mb-8 rounded-lg bg-gray-50 p-6">
            <h3 class="mb-4 text-xl font-semibold">Leave a Message</h3>

            <!-- Success Message -->
            <div v-if="condolenceMessage" class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
              {{ condolenceMessage }}
            </div>

            <!-- Error Message -->
            <div v-if="condolenceError" class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
              {{ condolenceError }}
            </div>

            <form @submit.prevent="submitCondolence" class="space-y-4">
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Your Name *</label>
                  <input
                    type="text"
                    v-model="condolenceForm.visitor_name"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter your name"
                    required
                    :disabled="isSubmittingCondolence"
                  />
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Email (Optional)</label>
                  <input
                    type="email"
                    v-model="condolenceForm.visitor_email"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                    placeholder="your.email@example.com"
                    :disabled="isSubmittingCondolence"
                  />
                </div>
              </div>

              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Phone Number *</label>
                  <input
                    type="tel"
                    v-model="condolenceForm.visitor_phone"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                    placeholder="9876543210"
                    maxlength="10"
                    pattern="[6-9][0-9]{9}"
                    required
                    :disabled="isSubmittingCondolence"
                  />
                  <div class="mt-1 text-xs text-gray-500">Enter 10-digit Indian mobile number (starting with 6, 7, 8, or 9)</div>
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Relationship (Optional)</label>
                  <select
                    v-model="condolenceForm.relationship"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                    :disabled="isSubmittingCondolence"
                  >
                    <option value="">Select relationship</option>
                    <option value="family">Family</option>
                    <option value="friend">Friend</option>
                    <option value="colleague">Colleague</option>
                    <option value="neighbour">Neighbour</option>
                    <option value="acquaintance">Acquaintance</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Your Message *</label>
                <textarea
                  rows="4"
                  v-model="condolenceForm.message"
                  class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-blue-500"
                  placeholder="Share your memories and condolences..."
                  required
                  maxlength="500"
                  :disabled="isSubmittingCondolence"
                ></textarea>
                <div class="mt-1 text-sm text-gray-500">{{ condolenceForm.message.length }}/500 characters</div>
              </div>

              <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-white transition-colors duration-200 hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="isSubmittingCondolence"
              >
                <span v-if="isSubmittingCondolence">Submitting...</span>
                <span v-else>Share Message</span>
              </button>
            </form>
          </div>

          <!-- Existing Condolences -->
          <div v-if="condolences?.length" class="space-y-6">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Messages of Love ({{ condolences.length }})</h3>
            </div>
            <div
              :class="[
                'space-y-6',
                condolences.length > 5 ? 'scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 max-h-96 overflow-y-auto pr-2' : '',
              ]"
            >
              <div v-for="condolence in condolences" :key="condolence.id" class="rounded-lg border-l-4 border-blue-500 bg-white p-6 shadow-md">
                <div class="mb-3 flex items-start justify-between">
                  <h4 class="font-semibold text-gray-900">{{ condolence.visitor_name }}</h4>
                  <span class="text-sm text-gray-500">
                    {{ new Date(condolence.created_at).toLocaleDateString() }}
                  </span>
                </div>
                <p class="leading-relaxed text-gray-700">{{ condolence.message }}</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Footer -->
        <div class="border-t border-gray-200 pt-12 text-center">
          <p class="text-gray-600 italic">"Those we love never truly leave us. They live on in our hearts and memories forever."</p>
          <div class="mt-6 flex items-center justify-center space-x-4 text-sm text-gray-500">
            <span>Memorial Page</span>
            <span>•</span>
            <!-- <span>{{ obituary.service_type }} Service</span> -->
            <span>•</span>
            <span>{{ obituary.view_count || 0 }} visits</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.prose p {
  margin-bottom: 1rem;
}
</style>
