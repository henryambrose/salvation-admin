<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

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
  source: '',
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
      description:
        'Saint Peter, also known as Simon Peter, was one of the Twelve Apostles of Jesus Christ and the first Pope of the Catholic Church.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 2,
      name: 'Augustine',
      feastDay: 'August 28',
      image: '/images/saints/imgi_8_St.-Augustine.jpg',
      description:
        'Saint Augustine of Hippo was a theologian and philosopher who became one of the most important figures in the development of Western Christianity.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 3,
      name: 'Anthony',
      feastDay: 'June 13',
      image: '/images/saints/imgi_9_St.-Anthony.jpg',
      description:
        'Saint Anthony of Padua was a Portuguese Catholic priest and friar of the Franciscan Order, known for his powerful preaching and miracles.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 4,
      name: 'Lawrence',
      feastDay: 'August 10',
      image: '/images/saints/imgi_17_St.-Lawrence.jpg',
      description: 'Saint Lawrence was one of the seven deacons of the city of Rome, martyred during the persecution of Emperor Valerian.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 5,
      name: 'Faustina',
      feastDay: 'October 5',
      image: '/images/saints/imgi_10_St.-Faustina.jpg',
      description: 'Saint Faustina Kowalska was a Polish nun and mystic who received visions of Jesus and promoted the Divine Mercy devotion.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 6,
      name: 'Andrew',
      feastDay: 'November 30',
      image: '/images/saints/imgi_14_St.-Andrew.jpg',
      description: 'Saint Andrew was one of the Twelve Apostles of Jesus Christ and the brother of Saint Peter. He is the patron saint of Scotland.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 7,
      name: 'Francis Xavier',
      feastDay: 'December 3',
      image: '/images/saints/imgi_15_St.-Francis-Xavier.jpg',
      description: 'Saint Francis Xavier was a Jesuit missionary who spread Christianity in Asia, particularly in India, Japan, and the East Indies.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 8,
      name: 'Blaise',
      feastDay: 'February 3',
      image: '/images/saints/imgi_16_St.-Blaise.jpg',
      description: 'Saint Blaise was a physician and bishop of Sebastea who is venerated as the patron saint of throat ailments.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 9,
      name: 'Anne',
      feastDay: 'July 26',
      image: '/images/saints/imgi_20_St.-Anne.jpg',
      description:
        'Saint Anne is traditionally the mother of the Virgin Mary and grandmother of Jesus Christ, though not mentioned in the canonical gospels.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 10,
      name: 'Gonsalo Garcia',
      feastDay: 'February 6',
      image: '/images/saints/imgi_23_St.-Gonsalo-Garcia.jpg',
      description: 'Saint Gonsalo Garcia was a Franciscan friar and martyr who was crucified in Japan for his Christian faith.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 11,
      name: 'Maria Goretti',
      feastDay: 'July 6',
      image: '/images/saints/imgi_19_St.-Maria-Goretti.jpg',
      description: 'Saint Maria Goretti was an Italian virgin martyr who died defending her chastity and is known as the patron saint of purity.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 12,
      name: 'Sebastian',
      feastDay: 'January 20',
      image: '/images/saints/imgi_26_St.-Sebastian.jpg',
      description: 'Saint Sebastian was a Roman soldier who was martyred for his Christian faith and is often depicted with arrows.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 13,
      name: 'John the Baptist',
      feastDay: 'June 24',
      image: '/images/saints/imgi_27_St.-John-the-Baptist.jpg',
      description: 'Saint John the Baptist was a Jewish preacher who baptized Jesus and is considered a prophet in Christianity.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 14,
      name: 'Thomas',
      feastDay: 'July 3',
      image: '/images/saints/imgi_24_St.-Thomas.jpg',
      description: 'Saint Thomas was one of the Twelve Apostles of Jesus Christ, known for his initial doubt about the Resurrection.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 15,
      name: 'Christopher',
      feastDay: 'July 25',
      image: '/images/saints/imgi_13_St.-Christopher.jpg',
      description: 'Saint Christopher is venerated as a martyr and is considered the patron saint of travelers and motorists.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 16,
      name: 'Paul',
      feastDay: 'June 29',
      image: '/images/saints/imgi_25_St.-Paul.jpg',
      description: 'Saint Paul was an apostle who spread the teachings of Jesus Christ and wrote many of the New Testament epistles.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 17,
      name: 'Theresa of Child Jesus',
      feastDay: 'October 1',
      image: '/images/saints/imgi_11_St.-Theresa-of-Child-Jesus.jpg',
      description:
        'Saint Therese of Lisieux, also known as the Little Flower, was a French Carmelite nun known for her "Little Way" of spiritual childhood.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 18,
      name: 'Vincent De Paul',
      feastDay: 'September 27',
      image: '/images/saints/imgi_18_St.-Vincent-De-Paul.jpg',
      description: 'Saint Vincent de Paul was a French priest who dedicated his life to serving the poor and founded the Vincentians.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 19,
      name: 'Michael',
      feastDay: 'September 29',
      image: '/images/saints/imgi_29_St.-Michael.jpg',
      description: 'Saint Michael the Archangel is a powerful angel who is considered the protector of the Church and the patron of soldiers.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 20,
      name: 'Martin',
      feastDay: 'November 11',
      image: '/images/saints/imgi_21_St.-Martin.jpg',
      description: 'Saint Martin of Tours was a bishop who is known for cutting his cloak in half to share with a beggar.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 21,
      name: 'Dominic Savio',
      feastDay: 'March 9',
      image: '/images/saints/imgi_28_St.-Dominic-Savio.jpg',
      description: 'Saint Dominic Savio was a young Italian student of Saint John Bosco who died at the age of 14 and is known for his piety.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 22,
      name: 'Holy Family',
      feastDay: 'December 30',
      image: '/images/saints/imgi_30_St.-Holy-Family.jpg',
      description: 'The Holy Family consists of Jesus, Mary, and Joseph, serving as a model of family life and Christian virtues.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
    {
      id: 23,
      name: 'Jude',
      feastDay: 'October 28',
      image: '/images/saints/imgi_22_St.-Jude.jpg',
      description: 'Saint Jude Thaddeus was one of the Twelve Apostles and is known as the patron saint of lost causes and desperate situations.',
      loading: false,
      imageLoading: false,
      imageError: false,
    },
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
    currentSaintIndex.value = currentSaintIndex.value === 0 ? saints.value.length - 1 : currentSaintIndex.value - 1;
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
  <div
    class="flex max-h-screen min-h-screen overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-blue-900 dark:to-indigo-900"
  >
    <!-- Left Side - Catholic Content -->
    <div class="relative hidden overflow-hidden lg:flex lg:w-1/2">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
        <!-- Floating Elements -->
        <div class="absolute top-20 left-20 h-32 w-32 animate-pulse rounded-full bg-[#ffffff]/5 blur-xl"></div>
        <div class="absolute top-40 right-32 h-24 w-24 animate-bounce rounded-full bg-yellow-400/10 blur-lg"></div>
        <div class="absolute bottom-32 left-32 h-40 w-40 animate-pulse rounded-full bg-purple-400/10 blur-xl"></div>
        <div class="absolute right-20 bottom-20 h-28 w-28 animate-bounce rounded-full bg-blue-400/10 blur-lg"></div>
      </div>

      <!-- Content Overlay -->
      <div class="relative z-10 flex w-full flex-col overflow-hidden p-4 lg:p-8">
        <!-- Header with Logo -->
        <div class="mb-4 flex items-center gap-3 lg:mb-6 lg:gap-6">
          <div
            class="hover:shadow-3xl group relative flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl border-2 border-white bg-[#ffffff]/90 shadow-2xl backdrop-blur-xl transition-all duration-500 hover:scale-110 lg:h-20 lg:w-20 lg:rounded-3xl"
          >
            <AppLogoIcon
              class="relative z-10 size-10 fill-current text-blue-600 drop-shadow-lg transition-all duration-500 group-hover:drop-shadow-2xl lg:size-12"
            />
            <!-- Enhanced Glow effect -->
            <div
              class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-400/30 to-purple-400/30 opacity-60 blur-xl transition-opacity duration-500 group-hover:opacity-100 lg:rounded-3xl"
            ></div>
            <!-- Animated border glow -->
            <div
              class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-400 via-purple-400 to-indigo-400 opacity-0 blur-sm transition-opacity duration-500 group-hover:opacity-30 lg:rounded-3xl"
            ></div>
            <!-- Shimmer effect -->
            <div
              class="absolute inset-0 -translate-x-full rounded-2xl bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-1000 group-hover:translate-x-full lg:rounded-3xl"
            ></div>
          </div>
          <div class="flex-1">
            <h1 class="mb-2 bg-gradient-to-r from-white to-blue-100 bg-clip-text text-xl font-bold text-transparent drop-shadow-lg lg:text-3xl">
              {{ title }}
            </h1>
            <p class="mb-2 text-sm font-medium text-blue-200 lg:text-base">Catholic Community Management</p>
            <div class="flex items-center gap-3">
              <div class="h-2 w-2 animate-pulse rounded-full bg-green-400 shadow-lg shadow-green-400/50"></div>
              <span class="text-sm font-medium text-blue-100">Secure • Reliable • Faithful</span>
            </div>
          </div>
        </div>

        <!-- Catholic Calendar Section (without title) -->
        <div class="mb-3 flex-1 lg:mb-4">
          <div
            class="hover:shadow-3xl rounded-xl border border-white/20 bg-[#ffffff]/10 p-3 shadow-2xl backdrop-blur-md transition-all duration-500 hover:scale-[1.02] lg:rounded-2xl lg:p-4"
          >
            <div class="mb-3 text-center">
              <div class="mb-1 text-lg font-bold text-yellow-300 drop-shadow-lg lg:text-2xl">
                {{ catholicCalendar.date ? new Date(catholicCalendar.date).getDate() : new Date().getDate() }}
                {{
                  catholicCalendar.date
                    ? new Date(catholicCalendar.date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
                    : new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
                }}
              </div>
              <div v-if="catholicCalendar.weekday" class="text-sm font-medium text-blue-100">
                {{ catholicCalendar.weekday }}
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div class="rounded-xl border border-white/10 bg-[#ffffff]/5 p-3 text-center">
                <div class="mb-1 text-xs text-blue-200">Liturgical Season</div>
                <div class="text-sm font-semibold text-white">{{ catholicCalendar.liturgicalSeason || 'Ordinary Time' }}</div>
                <div v-if="catholicCalendar.seasonWeek" class="text-xs text-blue-100">Week {{ catholicCalendar.seasonWeek }}</div>
              </div>

              <div class="rounded-xl border border-white/10 bg-[#ffffff]/5 p-3 text-center">
                <div class="mb-1 text-xs text-blue-200">Feast Day</div>
                <div class="text-sm font-semibold text-white">{{ catholicCalendar.feastDay || 'No special feast today' }}</div>
              </div>

              <div class="rounded-xl border border-white/10 bg-[#ffffff]/5 p-3 text-center">
                <div class="mb-1 text-xs text-blue-200">Saint of the Day</div>
                <div class="text-sm font-semibold text-white">{{ catholicCalendar.saintOfTheDay || 'No saint feast today' }}</div>
              </div>

              <div class="rounded-xl border border-white/10 bg-[#ffffff]/5 p-3 text-center">
                <div class="mb-1 text-xs text-blue-200">Liturgical Color</div>
                <div class="text-sm font-semibold text-white">{{ catholicCalendar.color || 'Green' }}</div>
              </div>
            </div>

            <!-- API Source Indicator -->
            <div v-if="catholicCalendar.source" class="mt-3 border-t border-white/20 pt-2 text-center">
              <div class="flex items-center justify-center gap-2 text-xs text-blue-100">
                <div class="h-2 w-2 animate-pulse rounded-full bg-green-400"></div>
                Data from external Catholic calendar API
              </div>
            </div>
          </div>
        </div>

        <!-- Saints Carousel Section -->
        <div class="flex flex-1 flex-col">
          <h2 class="mb-2 text-center text-lg font-bold text-white lg:mb-3 lg:text-2xl">
            <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent"> Saints of the Church Community </span>
          </h2>
          <div
            class="hover:shadow-3xl relative min-h-0 flex-1 rounded-xl border border-white/20 bg-[#ffffff]/10 p-4 shadow-2xl backdrop-blur-md transition-all duration-500 hover:scale-[1.02] lg:rounded-2xl lg:p-6"
          >
            <!-- Fixed Layout Structure -->
            <div class="flex h-full flex-col">
              <!-- Top Section: Image -->
              <div class="mb-2 flex flex-shrink-0 justify-center lg:mb-3">
                <div class="relative h-20 w-20 lg:h-24 lg:w-24">
                  <!-- Loading State -->
                  <div
                    v-if="saints[currentSaintIndex]?.loading"
                    class="flex h-20 w-20 animate-pulse items-center justify-center rounded-full bg-[#ffffff]/20 lg:h-24 lg:w-24"
                  >
                    <div class="h-5 w-5 animate-spin rounded-full border-3 border-white/50 border-t-white lg:h-[1.5rem] lg:w-[1.5rem]"></div>
                  </div>

                  <!-- Image Container -->
                  <div v-else class="relative h-20 w-20 lg:h-24 lg:w-24">
                    <img
                      v-if="saints[currentSaintIndex]?.image && !saints[currentSaintIndex]?.imageError"
                      :src="saints[currentSaintIndex].image"
                      :alt="saints[currentSaintIndex]?.name"
                      @load="handleImageLoad(currentSaintIndex)"
                      @error="handleImageError(currentSaintIndex)"
                      class="hover:shadow-3xl h-20 w-20 rounded-full border-4 border-white/30 object-cover shadow-2xl transition-all duration-500 hover:scale-110 lg:h-24 lg:w-24"
                      :class="{ 'opacity-0': saints[currentSaintIndex]?.imageLoading }"
                    />

                    <!-- Fallback Icon -->
                    <div
                      v-else
                      class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-white/30 bg-gradient-to-br from-yellow-400/20 to-orange-500/20 shadow-2xl lg:h-24 lg:w-24"
                    >
                      <span class="text-3xl lg:text-4xl">🙏</span>
                    </div>
                  </div>

                  <!-- Image Loading Overlay -->
                  <div
                    v-if="saints[currentSaintIndex]?.imageLoading"
                    class="bg-opacity-10 absolute inset-0 flex h-20 w-20 items-center justify-center rounded-full bg-[#ffffff] lg:h-24 lg:w-24"
                  >
                    <div
                      class="border-opacity-50 h-[1rem] w-[1rem] animate-spin rounded-full border-3 border-white border-t-white lg:h-5 lg:w-5"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- Middle Section: Saint Info (Fixed Height) -->
              <div class="flex min-h-0 flex-1 flex-col justify-center text-center">
                <h3 class="mb-1 text-lg font-bold text-white transition-all duration-500 lg:text-xl">
                  {{ saints[currentSaintIndex]?.name || 'Loading...' }}
                </h3>
                <p class="mb-1 text-sm font-medium text-blue-200 lg:text-base">
                  {{ saints[currentSaintIndex]?.feastDay || '' }}
                </p>
                <div class="h-[1.5rem] overflow-hidden px-2 pb-10 lg:h-8 lg:px-4 lg:pb-12">
                  <p
                    class="text-xs leading-tight text-blue-100"
                    style="display: -webkit-box; -webkit-line-clamp: 1; line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden"
                  >
                    {{ saints[currentSaintIndex]?.description || '' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Carousel Navigation -->
            <div
              class="absolute bottom-4 left-1/2 flex -translate-x-1/2 transform items-center gap-1 rounded-full border-2 border-white/30 bg-black/40 px-3 py-2 backdrop-blur-md lg:bottom-6"
            >
              <button
                v-for="(saint, index) in saints"
                :key="saint.id"
                @click="goToSaint(index)"
                :class="[
                  'h-2 w-2 flex-shrink-0 rounded-full border-2 transition-all duration-300 hover:scale-150 lg:h-3 lg:w-3',
                  index === currentSaintIndex
                    ? 'scale-125 border-yellow-200 bg-yellow-300 shadow-lg shadow-yellow-300/50'
                    : 'border-white/60 bg-[#ffffff]/80 hover:border-white hover:bg-[#ffffff]',
                ]"
                :disabled="saints.length === 0"
              />
            </div>

            <!-- Previous/Next Buttons -->
            <button
              @click="previousSaint"
              class="bg-opacity-10 hover:bg-opacity-20 border-opacity-20 absolute top-1/2 left-2 flex h-10 w-10 -translate-y-1/2 transform items-center justify-center rounded-full border border-white bg-[#ffffff] backdrop-blur-sm transition-all duration-300 hover:scale-110 hover:bg-[#ffffff] lg:left-4 lg:h-12 lg:w-12"
              :disabled="saints.length <= 1"
            >
              <svg class="h-5 w-5 text-white lg:h-[1.5rem] lg:w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>

            <button
              @click="nextSaint"
              class="bg-opacity-10 hover:bg-opacity-20 border-opacity-20 absolute top-1/2 right-2 flex h-10 w-10 -translate-y-1/2 transform items-center justify-center rounded-full border border-white bg-[#ffffff] backdrop-blur-sm transition-all duration-300 hover:scale-110 hover:bg-[#ffffff] lg:right-4 lg:h-12 lg:w-12"
              :disabled="saints.length <= 1"
            >
              <svg class="h-5 w-5 text-white lg:h-[1.5rem] lg:w-[1.5rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="relative flex flex-1 items-start justify-center p-8 pt-20">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-800 dark:to-slate-900">
        <div class="bg-dots-pattern absolute inset-0 opacity-30"></div>
      </div>

      <div class="relative z-10 w-full max-w-md">
        <!-- Mobile Logo -->
        <div class="mb-12 flex flex-col items-center gap-8 lg:hidden">
          <Link :href="route('home')" class="group flex flex-col items-center gap-6">
            <div
              class="group-hover:shadow-3xl relative flex h-28 w-28 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 to-purple-600 shadow-2xl transition-all duration-500 group-hover:scale-110"
            >
              <AppLogoIcon class="size-20 fill-current text-white drop-shadow-lg transition-all duration-500 group-hover:drop-shadow-2xl" />
              <!-- Glow effect for mobile -->
              <div
                class="absolute inset-0 rounded-3xl bg-gradient-to-br from-blue-400/30 to-purple-400/30 opacity-0 blur-xl transition-opacity duration-500 group-hover:opacity-100"
              ></div>
            </div>
            <div class="text-center">
              <h1 class="mb-2 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-4xl font-bold text-transparent">{{ title }}</h1>
              <p class="text-lg font-medium text-gray-600 dark:text-gray-400">{{ description }}</p>
              <div class="mt-3 flex items-center justify-center gap-2">
                <div class="h-2 w-2 animate-pulse rounded-full bg-green-400"></div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Secure • Reliable • Faithful</span>
              </div>
            </div>
          </Link>
        </div>

        <!-- Login Form -->
        <div
          class="bg-opacity-80 dark:bg-opacity-80 border-opacity-20 dark:border-opacity-50 rounded-3xl border border-white bg-[#ffffff] p-8 shadow-2xl backdrop-blur-xl dark:border-slate-700 dark:bg-slate-800"
        >
          <div class="mb-8 text-center">
            <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">Welcome Back</h2>
            <p class="text-gray-600 dark:text-gray-400">Sign in to your account to continue</p>
          </div>

          <slot />
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
          <p class="text-sm text-gray-500 dark:text-gray-400">© 2025 {{ title }}. All rights reserved.</p>
        </div>
      </div>
    </div>
  </div>
</template>
