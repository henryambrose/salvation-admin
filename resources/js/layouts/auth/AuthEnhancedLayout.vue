<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps<{
  title?: string;
  description?: string;
}>();

interface Saint {
  id: number;
  name: string;
  feastDay: string;
  image: string;
  description: string;
  loading?: boolean;
  imageLoading?: boolean;
  imageError?: boolean;
}

interface Celebration {
  title: string;
  colour: string;
  rank: string;
  rank_num: number;
}

interface CatholicCalendar {
  date: string;
  liturgicalSeason: string;
  feastDay: string;
  saintOfTheDay: string;
  color: string;
  reading: string;
  weekday: string;
  seasonWeek: number | null;
  celebrations: Celebration[];
  source: string;
}

// Catholic calendar data
const catholicCalendar = ref<CatholicCalendar>({
  date: new Date().toISOString().split('T')[0],
  liturgicalSeason: '',
  feastDay: '',
  saintOfTheDay: '',
  color: '',
  reading: '',
  weekday: '',
  seasonWeek: null,
  celebrations: [],
  source: ''
});

// Saints carousel data
const saints = ref<Saint[]>([]);

const currentSaintIndex = ref(0);

// Auto-advance carousel
onMounted(() => {
  setInterval(() => {
    if (saints.value.length > 0) {
      nextSaint();
    }
  }, 5000);
});

// Fetch Catholic calendar data
const fetchCatholicCalendar = async () => {
  try {
    const response = await fetch('/api/catholic-calendar');
    if (response.ok) {
      const data = await response.json();
      catholicCalendar.value = data;
    }
  } catch (error) {
    console.error('Error fetching Catholic calendar:', error);
  }
};

// Fetch saints data
const fetchSaints = async () => {
  try {
    const response = await fetch('/api/saints?random=true&count=5');
    if (response.ok) {
      const data = await response.json();
      // Add loading states to saints
      saints.value = data.map((saint: any) => ({
        ...saint,
        loading: false,
        imageLoading: true,
        imageError: false
      }));
    }
  } catch (error) {
    console.error('Error fetching saints:', error);
    // Fallback to default saints if API fails
    saints.value = [
      {
        id: 1,
        name: 'St. Francis of Assisi',
        feastDay: 'October 4',
        image: '/images/saints/francis-assisi.jpg',
        description: 'Patron saint of animals and ecology',
        loading: false,
        imageLoading: false,
        imageError: false
      },
      {
        id: 2,
        name: 'St. Therese of Lisieux',
        feastDay: 'October 1',
        image: '/images/saints/therese-lisieux.jpg',
        description: 'The Little Flower of Jesus',
        loading: false,
        imageLoading: false,
        imageError: false
      },
      {
        id: 3,
        name: 'St. Padre Pio',
        feastDay: 'September 23',
        image: '/images/saints/padre-pio.jpg',
        description: 'Mystic and stigmatist',
        loading: false,
        imageLoading: false,
        imageError: false
      },
      {
        id: 4,
        name: 'St. Mother Teresa',
        feastDay: 'September 5',
        image: '/images/saints/mother-teresa.jpg',
        description: 'Missionary of Charity',
        loading: false,
        imageLoading: false,
        imageError: false
      },
      {
        id: 5,
        name: 'St. John Paul II',
        feastDay: 'October 22',
        image: '/images/saints/john-paul-ii.jpg',
        description: 'The Great Pope',
        loading: false,
        imageLoading: false,
        imageError: false
      }
    ];
  }
};

// Carousel navigation methods
const goToSaint = (index: number) => {
  if (index >= 0 && index < saints.value.length) {
    currentSaintIndex.value = index;
  }
};

const nextSaint = () => {
  if (saints.value.length > 0) {
    currentSaintIndex.value = (currentSaintIndex.value + 1) % saints.value.length;
  }
};

const previousSaint = () => {
  if (saints.value.length > 0) {
    currentSaintIndex.value = currentSaintIndex.value === 0 
      ? saints.value.length - 1 
      : currentSaintIndex.value - 1;
  }
};

// Image handling methods
const handleImageLoad = (index: number) => {
  if (saints.value[index]) {
    saints.value[index].imageLoading = false;
    saints.value[index].imageError = false;
  }
};

const handleImageError = (index: number) => {
  if (saints.value[index]) {
    saints.value[index].imageLoading = false;
    saints.value[index].imageError = true;
  }
};

onMounted(() => {
  fetchCatholicCalendar();
  fetchSaints();
});
</script>

<template>
  <div class="flex min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-blue-900 dark:to-indigo-900">
    <!-- Left Side - Catholic Content -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
        <!-- Floating Elements -->
        <div class="absolute top-20 left-20 w-32 h-32 bg-white/5 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute top-40 right-32 w-24 h-24 bg-yellow-400/10 rounded-full blur-lg animate-bounce"></div>
        <div class="absolute bottom-32 left-32 w-40 h-40 bg-purple-400/10 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-28 h-28 bg-blue-400/10 rounded-full blur-lg animate-bounce"></div>
      </div>
      
      <!-- Content Overlay -->
      <div class="relative z-10 flex flex-col w-full p-8">
        <!-- Header with Logo -->
        <div class="flex items-center gap-6 mb-12">
          <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-white/20 to-white/10 backdrop-blur-xl border border-white/30 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-110 group">
            <AppLogoIcon class="size-16 fill-current text-white drop-shadow-lg group-hover:drop-shadow-2xl transition-all duration-500" />
            <!-- Glow effect -->
            <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-blue-400/20 to-purple-400/20 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          </div>
          <div class="flex-1">
            <h1 class="text-4xl font-bold text-white mb-2 drop-shadow-lg">
              {{ title }}
            </h1>
            <p class="text-blue-200 text-lg font-medium">Catholic Community Management</p>
            <div class="flex items-center gap-2 mt-2">
              <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
              <span class="text-sm text-blue-100">Secure • Reliable • Faithful</span>
            </div>
          </div>
        </div>

        <!-- Catholic Calendar Section -->
        <div class="flex-1 mb-8">
          <h2 class="text-3xl font-bold mb-8 text-center text-white">
            <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
              Catholic Calendar
            </span>
          </h2>
          <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-[1.02]">
            <div class="text-center mb-6">
              <div class="text-6xl font-bold text-yellow-300 mb-2 drop-shadow-lg">
                {{ catholicCalendar.date ? new Date(catholicCalendar.date).getDate() : new Date().getDate() }}
              </div>
              <div class="text-xl text-blue-200 font-medium">
                {{ catholicCalendar.date ? new Date(catholicCalendar.date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) : new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) }}
              </div>
              <div v-if="catholicCalendar.weekday" class="text-sm text-blue-100 mt-2 font-medium">
                {{ catholicCalendar.weekday }}
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
              <div class="text-center p-4 bg-white/5 rounded-xl border border-white/10">
                <div class="text-sm text-blue-200 mb-2">Liturgical Season</div>
                <div class="text-lg font-semibold text-white">{{ catholicCalendar.liturgicalSeason || 'Ordinary Time' }}</div>
                <div v-if="catholicCalendar.seasonWeek" class="text-xs text-blue-100 mt-1">
                  Week {{ catholicCalendar.seasonWeek }}
                </div>
              </div>
              
              <div class="text-center p-4 bg-white/5 rounded-xl border border-white/10">
                <div class="text-sm text-blue-200 mb-2">Feast Day</div>
                <div class="text-lg font-semibold text-white">{{ catholicCalendar.feastDay || 'No special feast today' }}</div>
              </div>
              
              <div class="text-center p-4 bg-white/5 rounded-xl border border-white/10">
                <div class="text-sm text-blue-200 mb-2">Saint of the Day</div>
                <div class="text-lg font-semibold text-white">{{ catholicCalendar.saintOfTheDay || 'No saint feast today' }}</div>
              </div>
              
              <div class="text-center p-4 bg-white/5 rounded-xl border border-white/10">
                <div class="text-sm text-blue-200 mb-2">Liturgical Color</div>
                <div class="text-lg font-semibold text-white">{{ catholicCalendar.color || 'Green' }}</div>
              </div>
            </div>
            
            <!-- Additional Celebrations -->
            <div v-if="catholicCalendar.celebrations && catholicCalendar.celebrations.length > 1" class="mt-6 p-4 bg-white/5 rounded-xl border border-white/10">
              <div class="text-sm text-blue-200 mb-3 text-center">Other Celebrations</div>
              <div class="text-xs text-blue-100 space-y-1">
                <div v-for="celebration in catholicCalendar.celebrations.slice(1)" :key="celebration.title" class="text-center">
                  {{ celebration.title }} ({{ celebration.rank }})
                </div>
              </div>
            </div>
            
            <!-- API Source Indicator -->
            <div v-if="catholicCalendar.source" class="text-center mt-6 pt-4 border-t border-white/20">
              <div class="text-xs text-blue-100 flex items-center justify-center gap-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                Data from external Catholic calendar API
              </div>
            </div>
          </div>
        </div>

        <!-- Saints Carousel Section -->
        <div class="flex-1">
          <h2 class="text-3xl font-bold mb-8 text-center text-white">
            <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
              Saints of the Church
            </span>
          </h2>
          <div class="relative bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-[1.02] h-80">
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="text-center w-full">
                <!-- Saint Image -->
                <div class="relative w-32 h-32 mx-auto mb-6">
                  <!-- Loading State -->
                  <div v-if="saints[currentSaintIndex]?.loading" class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center animate-pulse">
                    <div class="w-8 h-8 border-3 border-white/50 border-t-white rounded-full animate-spin"></div>
                  </div>
                  
                  <!-- Image Container -->
                  <div v-else class="relative w-32 h-32">
                    <img 
                      v-if="saints[currentSaintIndex]?.image && !saints[currentSaintIndex]?.imageError"
                      :src="saints[currentSaintIndex].image" 
                      :alt="saints[currentSaintIndex]?.name"
                      @load="handleImageLoad(currentSaintIndex)"
                      @error="handleImageError(currentSaintIndex)"
                      class="w-32 h-32 rounded-full object-cover border-4 border-white/30 shadow-2xl transition-all duration-500 hover:scale-110 hover:shadow-3xl"
                      :class="{ 'opacity-0': saints[currentSaintIndex]?.imageLoading }"
                    />
                    
                    <!-- Fallback Icon -->
                    <div v-else class="w-32 h-32 bg-gradient-to-br from-yellow-400/20 to-orange-500/20 rounded-full flex items-center justify-center border-4 border-white/30 shadow-2xl">
                      <span class="text-5xl">🙏</span>
                    </div>
                  </div>
                  
                  <!-- Image Loading Overlay -->
                  <div v-if="saints[currentSaintIndex]?.imageLoading" class="absolute inset-0 w-32 h-32 bg-white bg-opacity-10 rounded-full flex items-center justify-center">
                    <div class="w-6 h-6 border-3 border-white border-opacity-50 border-t-white rounded-full animate-spin"></div>
                  </div>
                </div>
                
                <!-- Saint Information -->
                <div class="space-y-3">
                  <h3 class="text-2xl font-bold text-white mb-3 transition-all duration-500">
                    {{ saints[currentSaintIndex]?.name || 'Loading...' }}
                  </h3>
                  <p class="text-blue-200 mb-3 text-lg font-medium">
                    {{ saints[currentSaintIndex]?.feastDay || '' }}
                  </p>
                  <p class="text-sm text-blue-100 leading-relaxed max-w-xs mx-auto">
                    {{ saints[currentSaintIndex]?.description || '' }}
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Carousel Navigation -->
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3">
              <button
                v-for="(saint, index) in saints"
                :key="saint.id"
                @click="goToSaint(index)"
                :class="[
                  'w-3 h-3 rounded-full transition-all duration-300 hover:scale-150',
                  index === currentSaintIndex ? 'bg-yellow-300 shadow-lg shadow-yellow-300 shadow-opacity-50' : 'bg-white bg-opacity-50 hover:bg-white hover:bg-opacity-70'
                ]"
                :disabled="saints.length === 0"
              />
            </div>
            
            <!-- Previous/Next Buttons -->
            <button 
              @click="previousSaint"
              class="absolute left-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-white bg-opacity-10 hover:bg-white hover:bg-opacity-20 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110 backdrop-blur-sm border border-white border-opacity-20"
              :disabled="saints.length <= 1"
            >
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <button 
              @click="nextSaint"
              class="absolute right-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-white bg-opacity-10 hover:bg-white hover:bg-opacity-20 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110 backdrop-blur-sm border border-white border-opacity-20"
              :disabled="saints.length <= 1"
            >
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="flex-1 flex items-start justify-center p-8 relative pt-20">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-800 dark:to-slate-900">
        <div class="absolute inset-0 opacity-30 bg-dots-pattern"></div>
      </div>
      
      <div class="relative z-10 w-full max-w-md">
        <!-- Mobile Logo -->
        <div class="lg:hidden flex flex-col items-center gap-8 mb-12">
          <Link :href="route('home')" class="flex flex-col items-center gap-6 group">
            <div class="flex h-28 w-28 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 to-purple-600 shadow-2xl group-hover:shadow-3xl transition-all duration-500 group-hover:scale-110 relative">
              <AppLogoIcon class="size-20 fill-current text-white drop-shadow-lg group-hover:drop-shadow-2xl transition-all duration-500" />
              <!-- Glow effect for mobile -->
              <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-blue-400/30 to-purple-400/30 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            </div>
            <div class="text-center">
              <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">{{ title }}</h1>
              <p class="text-gray-600 dark:text-gray-400 text-lg font-medium">{{ description }}</p>
              <div class="flex items-center justify-center gap-2 mt-3">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Secure • Reliable • Faithful</span>
              </div>
            </div>
          </Link>
        </div>

        <!-- Login Form -->
        <div class="bg-white bg-opacity-80 dark:bg-slate-800 dark:bg-opacity-80 backdrop-blur-xl rounded-3xl p-8 shadow-2xl border border-white border-opacity-20 dark:border-slate-700 dark:border-opacity-50">
          <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome Back</h2>
            <p class="text-gray-600 dark:text-gray-400">Sign in to your account to continue</p>
          </div>

          <slot />
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-8">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            © 2025 {{ title }}. All rights reserved.
          </p>
        </div>
      </div>
    </div>
  </div>
</template> 