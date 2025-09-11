<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Heart, Share2, MessageCircle } from 'lucide-vue-next';

interface ObituaryPage {
  id: number;
  uuid: string;
  service_type: 'basic' | 'premium';
  is_public: boolean;
  is_active: boolean;
  view_count: number;
  qr_scan_count: number;
  profile_image?: string;
  biography?: string;
  favorite_memory?: string;
  achievements?: string;
  hobbies_interests?: string;
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
  condolences?: any[];
}

const props = defineProps<Props>();

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
  return 'In Loving Memory';
});

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

const getBackgroundStyle = computed(() => {
  const style: any = {
    backgroundColor: props.obituary.theme_color || '#ffffff'
  };

  if (props.obituary.background_style === 'gradient') {
    style.backgroundImage = `linear-gradient(135deg, ${props.obituary.theme_color || '#ffffff'}, #f8f9fa)`;
  } else if (props.obituary.background_style === 'pattern') {
    style.backgroundImage = 'url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23f0f0f0\' fill-opacity=\'0.1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")';
  }

  return style;
});

const shareObituaryPage = () => {
  if (navigator.share) {
    navigator.share({
      title: `In memory of ${deceasedName.value}`,
      text: `Remember ${deceasedName.value} - Memorial Page`,
      url: shareUrl.value,
    });
  } else {
    navigator.clipboard.writeText(shareUrl.value);
    // Could show a toast message here
  }
};
</script>

<template>
  <Head :title="`In memory of ${deceasedName}`" />

  <div 
    class="min-h-screen"
    :style="getBackgroundStyle"
  >
    <!-- Header Section -->
    <div class="relative py-16 px-4">
      <div class="max-w-4xl mx-auto text-center">
        <!-- Profile Image -->
        <div v-if="obituary.profile_image" class="mb-8">
          <img 
            :src="obituary.profile_image.startsWith('http') ? obituary.profile_image : `/storage/${obituary.profile_image}`" 
            :alt="deceasedName"
            class="w-48 h-48 rounded-full mx-auto object-cover shadow-2xl border-4 border-white"
          />
        </div>
        
        <!-- Name and Dates -->
        <h1 class="text-4xl md:text-6xl font-serif font-bold text-gray-900 mb-4">
          {{ deceasedName }}
        </h1>
        
        <div v-if="dateOfBirth || dateOfDeath" class="text-xl text-gray-600 mb-8">
          <span v-if="dateOfBirth">{{ dateOfBirth }}</span>
          <span v-if="dateOfBirth && dateOfDeath"> - </span>
          <span v-if="dateOfDeath">{{ dateOfDeath }}</span>
        </div>
        
        <!-- Share Button -->
        <button 
          @click="shareObituaryPage"
          class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm rounded-full text-gray-700 hover:bg-white/30 transition-all duration-200 shadow-lg"
        >
          <Share2 class="w-5 h-5 mr-2" />
          Share Memorial
        </button>
      </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-4xl mx-auto px-4 pb-16">
      <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-2xl p-8 md:p-12">
        
        <!-- Biography -->
        <section v-if="obituary.biography" class="mb-12">
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-6 flex items-center">
            <Heart class="w-8 h-8 mr-3 text-red-500" />
            Life Story
          </h2>
          <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
            <p class="whitespace-pre-line">{{ obituary.biography }}</p>
          </div>
        </section>

        <!-- Favorite Memory -->
        <section v-if="obituary.favorite_memory" class="mb-12">
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-6">
            Cherished Memories
          </h2>
          <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
            <p class="whitespace-pre-line">{{ obituary.favorite_memory }}</p>
          </div>
        </section>

        <!-- Achievements -->
        <section v-if="obituary.achievements" class="mb-12">
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-6">
            Achievements & Legacy
          </h2>
          <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
            <p class="whitespace-pre-line">{{ obituary.achievements }}</p>
          </div>
        </section>

        <!-- Hobbies & Interests -->
        <section v-if="obituary.hobbies_interests" class="mb-12">
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-6">
            Hobbies & Interests
          </h2>
          <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
            <p class="whitespace-pre-line">{{ obituary.hobbies_interests }}</p>
          </div>
        </section>

        <!-- Gallery -->
        <section v-if="obituary.gallery_images?.length" class="mb-12">
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-6">
            Photo Gallery
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <img 
              v-for="(image, index) in obituary.gallery_images" 
              :key="index"
              :src="image.startsWith('http') ? image : `/storage/${image}`" 
              :alt="`Memory ${index + 1}`"
              class="w-full h-48 object-cover rounded-lg shadow-md hover:shadow-xl transition-shadow duration-200"
            />
          </div>
        </section>

        <!-- Audio Message -->
        <section v-if="obituary.audio_message" class="mb-12">
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-6">
            Audio Message
          </h2>
          <audio controls class="w-full max-w-md">
            <source :src="obituary.audio_message.startsWith('http') ? obituary.audio_message : `/storage/${obituary.audio_message}`" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>
        </section>

        <!-- Condolences Section -->
        <section v-if="obituary.allow_condolences" class="mb-12">
          <h2 class="text-3xl font-serif font-bold text-gray-900 mb-6 flex items-center">
            <MessageCircle class="w-8 h-8 mr-3 text-blue-500" />
            Messages of Love
          </h2>
          
          <!-- Condolence Form -->
          <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h3 class="text-xl font-semibold mb-4">Leave a Message</h3>
            <form class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                <input 
                  type="text" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your name"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                <textarea 
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Share your memories and condolences..."
                ></textarea>
              </div>
              <button 
                type="submit"
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
              >
                Share Message
              </button>
            </form>
          </div>

          <!-- Existing Condolences -->
          <div v-if="condolences?.length" class="space-y-6">
            <div 
              v-for="condolence in condolences" 
              :key="condolence.id"
              class="bg-white rounded-lg p-6 shadow-md border-l-4 border-blue-500"
            >
              <div class="flex justify-between items-start mb-3">
                <h4 class="font-semibold text-gray-900">{{ condolence.name }}</h4>
                <span class="text-sm text-gray-500">
                  {{ new Date(condolence.created_at).toLocaleDateString() }}
                </span>
              </div>
              <p class="text-gray-700 leading-relaxed">{{ condolence.message }}</p>
            </div>
          </div>
        </section>

        <!-- Footer -->
        <div class="text-center pt-12 border-t border-gray-200">
          <p class="text-gray-600 italic">
            "Those we love never truly leave us. They live on in our hearts and memories forever."
          </p>
          <div class="mt-6 flex justify-center items-center space-x-4 text-sm text-gray-500">
            <span>Memorial Page</span>
            <span>•</span>
            <span>{{ obituary.service_type }} Service</span>
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