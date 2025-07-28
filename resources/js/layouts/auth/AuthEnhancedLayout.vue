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

// Fixed saints data
const fetchSaints = () => {
  saints.value = [
    {
      id: 1,
      name: 'Peter',
      feastDay: 'June 29',
      image: '/images/saints/imgi_12_St.-Peter.jpg',
      description: 'Saint Peter, also known as Simon Peter, was one of the Twelve Apostles of Jesus Christ and the first Pope of the Catholic Church.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 2,
      name: 'Augustine',
      feastDay: 'August 28',
      image: '/images/saints/imgi_8_St.-Augustine.jpg',
      description: 'Saint Augustine of Hippo was a theologian and philosopher who became one of the most important figures in the development of Western Christianity.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 3,
      name: 'Anthony',
      feastDay: 'June 13',
      image: '/images/saints/imgi_9_St.-Anthony.jpg',
      description: 'Saint Anthony of Padua was a Portuguese Catholic priest and friar of the Franciscan Order, known for his powerful preaching and miracles.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 4,
      name: 'Lawrence',
      feastDay: 'August 10',
      image: '/images/saints/imgi_17_St.-Lawrence.jpg',
      description: 'Saint Lawrence was one of the seven deacons of the city of Rome, martyred during the persecution of Emperor Valerian.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 5,
      name: 'Faustina',
      feastDay: 'October 5',
      image: '/images/saints/imgi_10_St.-Faustina.jpg',
      description: 'Saint Faustina Kowalska was a Polish nun and mystic who received visions of Jesus and promoted the Divine Mercy devotion.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 6,
      name: 'Andrew',
      feastDay: 'November 30',
      image: '/images/saints/imgi_14_St.-Andrew.jpg',
      description: 'Saint Andrew was one of the Twelve Apostles of Jesus Christ and the brother of Saint Peter. He is the patron saint of Scotland.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 7,
      name: 'Francis Xavier',
      feastDay: 'December 3',
      image: '/images/saints/imgi_15_St.-Francis-Xavier.jpg',
      description: 'Saint Francis Xavier was a Jesuit missionary who spread Christianity in Asia, particularly in India, Japan, and the East Indies.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 8,
      name: 'Blaise',
      feastDay: 'February 3',
      image: '/images/saints/imgi_16_St.-Blaise.jpg',
      description: 'Saint Blaise was a physician and bishop of Sebastea who is venerated as the patron saint of throat ailments.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 9,
      name: 'Anne',
      feastDay: 'July 26',
      image: '/images/saints/imgi_20_St.-Anne.jpg',
      description: 'Saint Anne is traditionally the mother of the Virgin Mary and grandmother of Jesus Christ, though not mentioned in the canonical gospels.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 10,
      name: 'Gonsalo Garcia',
      feastDay: 'February 6',
      image: '/images/saints/imgi_23_St.-Gonsalo-Garcia.jpg',
      description: 'Saint Gonsalo Garcia was a Franciscan friar and martyr who was crucified in Japan for his Christian faith.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 11,
      name: 'Maria Goretti',
      feastDay: 'July 6',
      image: '/images/saints/imgi_19_St.-Maria-Goretti.jpg',
      description: 'Saint Maria Goretti was an Italian virgin martyr who died defending her chastity and is known as the patron saint of purity.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 12,
      name: 'Sebastian',
      feastDay: 'January 20',
      image: '/images/saints/imgi_26_St.-Sebastian.jpg',
      description: 'Saint Sebastian was a Roman soldier who was martyred for his Christian faith and is often depicted with arrows.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 13,
      name: 'John the Baptist',
      feastDay: 'June 24',
      image: '/images/saints/imgi_27_St.-John-the-Baptist.jpg',
      description: 'Saint John the Baptist was a Jewish preacher who baptized Jesus and is considered a prophet in Christianity.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 14,
      name: 'Thomas',
      feastDay: 'July 3',
      image: '/images/saints/imgi_24_St.-Thomas.jpg',
      description: 'Saint Thomas was one of the Twelve Apostles of Jesus Christ, known for his initial doubt about the Resurrection.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 15,
      name: 'Christopher',
      feastDay: 'July 25',
      image: '/images/saints/imgi_13_St.-Christopher.jpg',
      description: 'Saint Christopher is venerated as a martyr and is considered the patron saint of travelers and motorists.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 16,
      name: 'Paul',
      feastDay: 'June 29',
      image: '/images/saints/imgi_25_St.-Paul.jpg',
      description: 'Saint Paul was an apostle who spread the teachings of Jesus Christ and wrote many of the New Testament epistles.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 17,
      name: 'Theresa of Child Jesus',
      feastDay: 'October 1',
      image: '/images/saints/imgi_11_St.-Theresa-of-Child-Jesus.jpg',
      description: 'Saint Therese of Lisieux, also known as the Little Flower, was a French Carmelite nun known for her "Little Way" of spiritual childhood.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 18,
      name: 'Vincent De Paul',
      feastDay: 'September 27',
      image: '/images/saints/imgi_18_St.-Vincent-De-Paul.jpg',
      description: 'Saint Vincent de Paul was a French priest who dedicated his life to serving the poor and founded the Vincentians.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 19,
      name: 'Michael',
      feastDay: 'September 29',
      image: '/images/saints/imgi_29_St.-Michael.jpg',
      description: 'Saint Michael the Archangel is a powerful angel who is considered the protector of the Church and the patron of soldiers.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 20,
      name: 'Martin',
      feastDay: 'November 11',
      image: '/images/saints/imgi_21_St.-Martin.jpg',
      description: 'Saint Martin of Tours was a bishop who is known for cutting his cloak in half to share with a beggar.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 21,
      name: 'Dominic Savio',
      feastDay: 'March 9',
      image: '/images/saints/imgi_28_St.-Dominic-Savio.jpg',
      description: 'Saint Dominic Savio was a young Italian student of Saint John Bosco who died at the age of 14 and is known for his piety.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 22,
      name: 'Holy Family',
      feastDay: 'December 30',
      image: '/images/saints/imgi_30_St.-Holy-Family.jpg',
      description: 'The Holy Family consists of Jesus, Mary, and Joseph, serving as a model of family life and Christian virtues.',
      loading: false,
      imageLoading: false,
      imageError: false
    },
    {
      id: 23,
      name: 'Jude',
      feastDay: 'October 28',
      image: '/images/saints/imgi_22_St.-Jude.jpg',
      description: 'Saint Jude Thaddeus was one of the Twelve Apostles and is known as the patron saint of lost causes and desperate situations.',
      loading: false,
      imageLoading: false,
      imageError: false
    }
  ];
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
  <div class="flex min-h-screen max-h-screen overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-blue-900 dark:to-indigo-900">
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
      <div class="relative z-10 flex flex-col w-full p-4 lg:p-8 overflow-hidden">
        <!-- Header with Logo -->
        <div class="flex items-center gap-3 lg:gap-6 mb-6 lg:mb-8">
          <div class="flex h-16 w-16 lg:h-20 lg:w-20 items-center justify-center rounded-2xl lg:rounded-3xl bg-gradient-to-br from-blue-600/30 via-purple-600/30 to-indigo-600/30 backdrop-blur-xl border-2 border-white/40 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-110 group relative overflow-hidden">
            <AppLogoIcon class="size-10 lg:size-12 fill-current text-white drop-shadow-lg group-hover:drop-shadow-2xl transition-all duration-500 relative z-10" />
            <!-- Enhanced Glow effect -->
            <div class="absolute inset-0 rounded-2xl lg:rounded-3xl bg-gradient-to-br from-blue-400/30 to-purple-400/30 blur-xl opacity-60 group-hover:opacity-100 transition-opacity duration-500"></div>
            <!-- Animated border glow -->
            <div class="absolute inset-0 rounded-2xl lg:rounded-3xl bg-gradient-to-r from-blue-400 via-purple-400 to-indigo-400 opacity-0 group-hover:opacity-30 blur-sm transition-opacity duration-500"></div>
            <!-- Shimmer effect -->
            <div class="absolute inset-0 rounded-2xl lg:rounded-3xl bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
          </div>
          <div class="flex-1">
            <h1 class="text-xl lg:text-3xl font-bold text-white mb-2 drop-shadow-lg bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
              {{ title }}
            </h1>
            <p class="text-blue-200 text-sm lg:text-base font-medium mb-2">Catholic Community Management</p>
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></div>
              <span class="text-sm text-blue-100 font-medium">Secure • Reliable • Faithful</span>
            </div>
          </div>
        </div>



        <!-- Catholic Calendar Section (without title) -->
        <div class="flex-1 mb-3 lg:mb-4">
          <div class="bg-white/10 backdrop-blur-md rounded-xl lg:rounded-2xl p-3 lg:p-4 border border-white/20 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-[1.02]">
            <div class="text-center mb-3">
              <div class="text-lg lg:text-2xl font-bold text-yellow-300 mb-1 drop-shadow-lg">
                {{ catholicCalendar.date ? new Date(catholicCalendar.date).getDate() : new Date().getDate() }} {{ catholicCalendar.date ? new Date(catholicCalendar.date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) : new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) }}
              </div>
              <div v-if="catholicCalendar.weekday" class="text-sm text-blue-100 font-medium">
                {{ catholicCalendar.weekday }}
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
              <div class="text-center p-3 bg-white/5 rounded-xl border border-white/10">
                <div class="text-xs text-blue-200 mb-1">Liturgical Season</div>
                <div class="text-sm font-semibold text-white">{{ catholicCalendar.liturgicalSeason || 'Ordinary Time' }}</div>
                <div v-if="catholicCalendar.seasonWeek" class="text-xs text-blue-100">
                  Week {{ catholicCalendar.seasonWeek }}
                </div>
              </div>
              
              <div class="text-center p-3 bg-white/5 rounded-xl border border-white/10">
                <div class="text-xs text-blue-200 mb-1">Feast Day</div>
                <div class="text-sm font-semibold text-white">{{ catholicCalendar.feastDay || 'No special feast today' }}</div>
              </div>
              
              <div class="text-center p-3 bg-white/5 rounded-xl border border-white/10">
                <div class="text-xs text-blue-200 mb-1">Saint of the Day</div>
                <div class="text-sm font-semibold text-white">{{ catholicCalendar.saintOfTheDay || 'No saint feast today' }}</div>
              </div>
              
              <div class="text-center p-3 bg-white/5 rounded-xl border border-white/10">
                <div class="text-xs text-blue-200 mb-1">Liturgical Color</div>
                <div class="text-sm font-semibold text-white">{{ catholicCalendar.color || 'Green' }}</div>
              </div>
            </div>
            
            <!-- API Source Indicator -->
            <div v-if="catholicCalendar.source" class="text-center mt-3 pt-2 border-t border-white/20">
              <div class="text-xs text-blue-100 flex items-center justify-center gap-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                Data from external Catholic calendar API
              </div>
            </div>
          </div>
        </div>

        <!-- Saints Carousel Section -->
        <div class="flex-1">
          <h2 class="text-lg lg:text-2xl font-bold mb-3 lg:mb-4 text-center text-white">
            <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
              Saints of the Church Community
            </span>
          </h2>
          <div class="relative bg-white/10 backdrop-blur-md rounded-xl lg:rounded-2xl p-4 lg:p-6 border border-white/20 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:scale-[1.02] h-60 lg:h-72">
            <!-- Fixed Layout Structure -->
            <div class="h-full flex flex-col">
              <!-- Top Section: Image -->
              <div class="flex-shrink-0 flex justify-center mb-2 lg:mb-3">
                <div class="relative w-20 h-20 lg:w-24 lg:h-24">
                  <!-- Loading State -->
                  <div v-if="saints[currentSaintIndex]?.loading" class="w-20 h-20 lg:w-24 lg:h-24 bg-white/20 rounded-full flex items-center justify-center animate-pulse">
                    <div class="w-5 h-5 lg:w-6 lg:h-6 border-3 border-white/50 border-t-white rounded-full animate-spin"></div>
                  </div>
                  
                  <!-- Image Container -->
                  <div v-else class="relative w-20 h-20 lg:w-24 lg:h-24">
                    <img 
                      v-if="saints[currentSaintIndex]?.image && !saints[currentSaintIndex]?.imageError"
                      :src="saints[currentSaintIndex].image" 
                      :alt="saints[currentSaintIndex]?.name"
                      @load="handleImageLoad(currentSaintIndex)"
                      @error="handleImageError(currentSaintIndex)"
                      class="w-20 h-20 lg:w-24 lg:h-24 rounded-full object-cover border-4 border-white/30 shadow-2xl transition-all duration-500 hover:scale-110 hover:shadow-3xl"
                      :class="{ 'opacity-0': saints[currentSaintIndex]?.imageLoading }"
                    />
                    
                    <!-- Fallback Icon -->
                    <div v-else class="w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-yellow-400/20 to-orange-500/20 rounded-full flex items-center justify-center border-4 border-white/30 shadow-2xl">
                      <span class="text-3xl lg:text-4xl">🙏</span>
                    </div>
                  </div>
                  
                  <!-- Image Loading Overlay -->
                  <div v-if="saints[currentSaintIndex]?.imageLoading" class="absolute inset-0 w-20 h-20 lg:w-24 lg:h-24 bg-white bg-opacity-10 rounded-full flex items-center justify-center">
                    <div class="w-4 h-4 lg:w-5 lg:h-5 border-3 border-white border-opacity-50 border-t-white rounded-full animate-spin"></div>
                  </div>
                </div>
              </div>
              
              <!-- Middle Section: Saint Info (Fixed Height) -->
              <div class="flex-1 flex flex-col justify-center text-center min-h-0">
                <h3 class="text-lg lg:text-xl font-bold text-white mb-1 transition-all duration-500">
                  {{ saints[currentSaintIndex]?.name || 'Loading...' }}
                </h3>
                <p class="text-blue-200 mb-1 text-sm lg:text-base font-medium">
                  {{ saints[currentSaintIndex]?.feastDay || '' }}
                </p>
                <div class="h-10 lg:h-12 px-2 lg:px-4 overflow-hidden pb-6 lg:pb-8">
                  <p class="text-xs text-blue-100 leading-tight" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ saints[currentSaintIndex]?.description || '' }}
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Carousel Navigation -->
            <div class="absolute bottom-4 lg:bottom-6 left-1/2 transform -translate-x-1/2 flex items-center gap-1 bg-black/40 backdrop-blur-md px-3 py-2 rounded-full border-2 border-white/30">
              <button
                v-for="(saint, index) in saints"
                :key="saint.id"
                @click="goToSaint(index)"
                :class="[
                  'w-2 h-2 lg:w-3 lg:h-3 rounded-full transition-all duration-300 hover:scale-150 flex-shrink-0 border-2',
                  index === currentSaintIndex 
                    ? 'bg-yellow-300 border-yellow-200 shadow-lg shadow-yellow-300/50 scale-125' 
                    : 'bg-white/80 border-white/60 hover:bg-white hover:border-white'
                ]"
                :disabled="saints.length === 0"
              />
            </div>
            
            <!-- Previous/Next Buttons -->
            <button 
              @click="previousSaint"
              class="absolute left-2 lg:left-4 top-1/2 transform -translate-y-1/2 w-10 h-10 lg:w-12 lg:h-12 bg-white bg-opacity-10 hover:bg-white hover:bg-opacity-20 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110 backdrop-blur-sm border border-white border-opacity-20"
              :disabled="saints.length <= 1"
            >
              <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <button 
              @click="nextSaint"
              class="absolute right-2 lg:right-4 top-1/2 transform -translate-y-1/2 w-10 h-10 lg:w-12 lg:h-12 bg-white bg-opacity-10 hover:bg-white hover:bg-opacity-20 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110 backdrop-blur-sm border border-white border-opacity-20"
              :disabled="saints.length <= 1"
            >
              <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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