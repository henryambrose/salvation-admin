<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { permissionHelpers } from '@/composables/permissionHelpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDateForDisplay } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Cake, Calendar, Church, Gift, Heart, Home, Mail, MapPin, Phone, Users } from 'lucide-vue-next';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import PermissionDenied from './errors/PermissionDenied.vue';

const { can } = permissionHelpers();

// Permission checks
const canViewDashboard = can('read-dashboard');

defineProps({
  statCards: {
    type: Array<Record<string, any>>,
    default: () => [],
  },
  tableCards: {
    type: Array<Record<string, any>>,
    default: () => [],
  },
  ageWiseData: {
    type: Array<Record<string, any>>,
    default: () => [],
  },
  birthdays: {
    type: Array<Record<string, any>>,
    default: () => [],
  },
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

// Get current date for greeting
const currentDate = new Date();
const currentHour = currentDate.getHours();
const greeting = currentHour < 12 ? 'Good Morning' : currentHour < 17 ? 'Good Afternoon' : 'Good Evening';

// Format date to dd/MM/yyyy
const formatDate = (dateString: string) => {
  if (!dateString) return '';
  return formatDateForDisplay(dateString);
};

// Get appropriate icon for each card
const getIconForCard = (title: string) => {
  const iconMap: Record<string, any> = {
    'Gender Wise': Users,
    'Community Wise Members': Users,
    'Community Wise Families': Home,
    'Status Wise Members': Heart,
    'Designation Wise Members': Gift,
    'Latest Qualifications Wise Members': Gift,
    'Relationship Wise Members': Users,
    'Zone-wise Statistics': MapPin,
    'Community-wise Statistics': Users,
  };
  return iconMap[title] || Heart;
};

// Get appropriate gradient for each card
const getGradientForCard = (title: string) => {
  const gradientMap: Record<string, string> = {
    'Gender Wise': 'bg-gradient-to-br from-indigo-500 to-purple-600',
    'Community Wise Members': 'bg-gradient-to-br from-purple-500 to-pink-600',
    'Community Wise Families': 'bg-gradient-to-br from-sky-500 to-blue-600',
    'Status Wise Members': 'bg-gradient-to-br from-green-500 to-emerald-600',
    'Designation Wise Members': 'bg-gradient-to-br from-yellow-500 to-orange-600',
    'Latest Qualifications Wise Members': 'bg-gradient-to-br from-blue-500 to-indigo-600',
    'Relationship Wise Members': 'bg-gradient-to-br from-red-500 to-pink-600',
    'Zone-wise Statistics': 'bg-gradient-to-br from-orange-500 to-red-600',
    'Community-wise Statistics': 'bg-gradient-to-br from-purple-500 to-pink-600',
  };
  return gradientMap[title] || 'bg-gradient-to-br from-green-500 to-emerald-600';
};

// Check if card is a statistics card (has members and families)
const isStatisticsCard = (title: string): boolean => {
  return title === 'Zone-wise Statistics' || title === 'Community-wise Statistics';
};

// Get appropriate column header for statistics cards
const getColumnHeader = (title: string): string => {
  if (title === 'Zone-wise Statistics') return 'Zone';
  if (title === 'Community-wise Statistics') return 'Community';
  return 'Status';
};
</script>

<template>
  <Head title="Dashboard" />

  <!-- Show Permission Denied if user doesn't have access -->
  <div v-if="!canViewDashboard">
    <PermissionDenied message="You do not have permission to view the dashboard. Please contact your administrator to request access." />
  </div>

  <!-- Show Dashboard if user has access -->
  <AppLayout v-else :breadcrumbs="breadcrumbs">
    <!-- Enhanced Dashboard Container -->
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-6 dark:from-slate-900 dark:via-blue-900 dark:to-indigo-900">
      <!-- Welcome Section -->
      <div class="mb-8">
        <div
          class="bg-opacity-80 dark:bg-opacity-80 border-opacity-20 dark:border-opacity-50 rounded-3xl border border-white bg-[#ffffff] p-8 shadow-2xl backdrop-blur-xl dark:border-slate-700 dark:bg-slate-800"
        >
          <div class="flex items-center justify-between">
            <div>
              <h1 class="mb-2 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-4xl font-bold text-transparent">
                {{ greeting }}, Welcome to Our Lady of Salvation
              </h1>
              <p class="text-lg text-gray-600 dark:text-gray-400">Catholic Community Management Dashboard</p>
            </div>
            <div class="hidden items-center gap-4 md:flex">
              <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">Today is</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ currentDate.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                </p>
              </div>
              <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-purple-600 shadow-lg">
                <Church class="h-8 w-8 text-white" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="mb-8 grid justify-center gap-12 md:grid-cols-2 lg:grid-cols-4">
        <Card
          v-for="statCard in statCards"
          class="bg-opacity-80 dark:bg-opacity-80 overflow-hidden rounded-2xl border-0 bg-[#ffffff] shadow-xl backdrop-blur-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl dark:bg-slate-800"
          :key="statCard.title"
        >
          <div class="relative">
            <!-- Gradient Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-purple-600 opacity-10"></div>

            <CardHeader class="p-6 pb-4">
              <div class="flex items-center justify-between">
                <CardTitle class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ statCard.title }}
                </CardTitle>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg">
                  <Users class="h-[1.5rem] w-[1.5rem] text-white" />
                </div>
              </div>
            </CardHeader>

            <CardContent class="p-6 pt-0">
              <div class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-4xl font-bold text-transparent">
                {{ statCard.count }}
              </div>
              <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Total {{ statCard.title.toLowerCase() }}</p>
            </CardContent>
          </div>
        </Card>
      </div>

      <!-- Birthdays Section -->
      <div class="mb-8">
        <Collapsible class="w-full">
          <CollapsibleTrigger
            class="bg-opacity-80 dark:bg-opacity-80 border-opacity-20 dark:border-opacity-50 group w-full rounded-2xl border border-white bg-[#ffffff] p-6 shadow-xl backdrop-blur-xl transition-all duration-300 hover:shadow-2xl dark:border-slate-700 dark:bg-slate-800"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-pink-500 to-red-500 shadow-lg">
                  <Cake class="h-[1.5rem] w-[1.5rem] text-white" />
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white">🎉 Birthdays This Month 🎂</h3>
                  <p class="text-gray-600 dark:text-gray-400">Celebrating our community members</p>
                </div>
              </div>
              <div class="text-2xl transition-transform duration-300 group-hover:scale-110">🎈</div>
            </div>
          </CollapsibleTrigger>

          <CollapsibleContent class="mt-4">
            <div
              class="bg-opacity-80 dark:bg-opacity-80 border-opacity-20 dark:border-opacity-50 rounded-2xl border border-white bg-[#ffffff] p-6 shadow-xl backdrop-blur-xl dark:border-slate-700 dark:bg-slate-800"
            >
              <div class="custom-scrollbar overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                      <th class="p-4 text-left font-semibold text-gray-900 dark:text-white">Community</th>
                      <th class="p-4 text-left font-semibold text-gray-900 dark:text-white">Name</th>
                      <th class="p-4 text-left font-semibold text-gray-900 dark:text-white">Date of Birth</th>
                      <th class="p-4 text-left font-semibold text-gray-900 dark:text-white">Age</th>
                      <th class="p-4 text-left font-semibold text-gray-900 dark:text-white">Contact</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="birthday in birthdays"
                      :key="birthday.id"
                      class="border-b border-gray-100 transition-colors duration-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-700/50"
                    >
                      <td class="p-4">
                        <span
                          class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                        >
                          {{ birthday?.community?.name || 'N/A' }}
                        </span>
                      </td>
                      <td class="p-4 font-medium text-gray-900 dark:text-white">
                        {{ birthday.first_name }} {{ birthday.middle_name || '' }} {{ birthday.last_name }}
                      </td>
                      <td class="p-4 text-gray-600 dark:text-gray-400">{{ formatDate(birthday.date_of_birth) }}</td>
                      <td class="p-4">
                        <span
                          class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200"
                        >
                          {{ birthday.age }} years
                        </span>
                      </td>
                      <td class="p-4">
                        <div class="flex items-center gap-2">
                          <a
                            v-if="birthday.contact_no_1"
                            :href="`tel:${birthday.contact_no_1}`"
                            class="text-blue-600 transition-colors duration-200 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                          >
                            <Phone class="h-[1rem] w-[1rem]" />
                          </a>
                          <a
                            v-if="birthday.email"
                            :href="`mailto:${birthday.email}`"
                            class="text-blue-600 transition-colors duration-200 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                          >
                            <Mail class="h-[1rem] w-[1rem]" />
                          </a>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </CollapsibleContent>
        </Collapsible>
      </div>

      <!-- Detailed Statistics -->
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Zone-wise Statistics -->
        <Card
          v-for="tableCard in tableCards.filter((card) =>
            ['Zone-wise Statistics', 'Community-wise Statistics', 'Gender Wise', 'Relationship Wise Members'].includes(card.title),
          )"
          class="bg-opacity-80 dark:bg-opacity-80 rounded-2xl border-0 bg-[#ffffff] shadow-xl backdrop-blur-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl dark:bg-slate-800"
          :key="tableCard.title"
        >
          <CardHeader class="p-6 pb-4">
            <div class="flex items-center justify-between">
              <CardTitle class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ tableCard.title }}
              </CardTitle>
              <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg"
                :class="getGradientForCard(tableCard.title)"
              >
                <component :is="getIconForCard(tableCard.title)" class="h-5 w-5 text-white" />
              </div>
            </div>
          </CardHeader>

          <CardContent class="p-6 pt-0">
            <div class="space-y-3">
              <!-- Table Header -->
              <div
                class="grid gap-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 p-3 text-sm font-semibold text-gray-900 dark:from-gray-700 dark:to-gray-800 dark:text-white"
                :class="isStatisticsCard(tableCard.title) ? 'grid-cols-3' : 'grid-cols-2'"
              >
                <div>{{ getColumnHeader(tableCard.title) }}</div>
                <div>{{ isStatisticsCard(tableCard.title) ? 'Members' : 'Count' }}</div>
                <div v-if="isStatisticsCard(tableCard.title)">Families</div>
              </div>

              <!-- Table Rows -->
              <div class="custom-scrollbar max-h-64 space-y-2 overflow-y-auto">
                <div
                  v-for="(row, index) in tableCard.data"
                  :key="index"
                  class="grid gap-4 rounded-lg bg-gray-50 p-3 text-sm transition-colors duration-200 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700"
                  :class="isStatisticsCard(tableCard.title) ? 'grid-cols-3' : 'grid-cols-2'"
                >
                  <div class="font-medium text-gray-900 dark:text-white">{{ index || 'Other' }}</div>
                  <div v-if="isStatisticsCard(tableCard.title)" class="font-semibold text-blue-600 dark:text-blue-400">{{ row.members }}</div>
                  <div v-else class="font-semibold text-blue-600 dark:text-blue-400">{{ row }}</div>
                  <div v-if="isStatisticsCard(tableCard.title)" class="font-semibold text-green-600 dark:text-green-400">{{ row.families }}</div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Age Wise Statistics -->
        <Card
          class="bg-opacity-80 dark:bg-opacity-80 rounded-2xl border-0 bg-[#ffffff] shadow-xl backdrop-blur-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl md:col-span-2 lg:col-span-1 dark:bg-slate-800"
        >
          <CardHeader class="p-6 pb-4">
            <div class="flex items-center justify-between">
              <CardTitle class="text-lg font-semibold text-gray-900 dark:text-white"> Age Distribution </CardTitle>
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg">
                <Calendar class="h-5 w-5 text-white" />
              </div>
            </div>
          </CardHeader>

          <CardContent class="p-6 pt-0">
            <div class="space-y-3">
              <!-- Table Header -->
              <div
                class="grid grid-cols-4 gap-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 p-3 text-sm font-semibold text-gray-900 dark:from-gray-700 dark:to-gray-800 dark:text-white"
              >
                <div>Age Group</div>
                <div>Total</div>
                <div>Male</div>
                <div>Female</div>
              </div>

              <!-- Table Rows -->
              <div class="custom-scrollbar max-h-64 space-y-2 overflow-y-auto">
                <div
                  v-for="(row, index) in ageWiseData"
                  :key="index"
                  class="grid grid-cols-4 gap-4 rounded-lg bg-gray-50 p-3 text-sm transition-colors duration-200 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700"
                >
                  <div class="font-medium text-gray-900 dark:text-white">{{ index }}</div>
                  <div class="font-semibold text-blue-600 dark:text-blue-400">{{ row['Male'] + row['Female'] }}</div>
                  <div class="text-blue-500 dark:text-blue-300">{{ row['Male'] }}</div>
                  <div class="text-pink-500 dark:text-pink-300">{{ row['Female'] }}</div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Remaining Table Cards -->
        <Card
          v-for="tableCard in tableCards.filter(
            (card) => !['Zone-wise Statistics', 'Community-wise Statistics', 'Gender Wise', 'Relationship Wise Members'].includes(card.title),
          )"
          class="bg-opacity-80 dark:bg-opacity-80 rounded-2xl border-0 bg-[#ffffff] shadow-xl backdrop-blur-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl dark:bg-slate-800"
          :key="tableCard.title"
        >
          <CardHeader class="p-6 pb-4">
            <div class="flex items-center justify-between">
              <CardTitle class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ tableCard.title }}
              </CardTitle>
              <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg"
                :class="getGradientForCard(tableCard.title)"
              >
                <component :is="getIconForCard(tableCard.title)" class="h-5 w-5 text-white" />
              </div>
            </div>
          </CardHeader>

          <CardContent class="p-6 pt-0">
            <div class="space-y-3">
              <!-- Table Header -->
              <div
                class="grid gap-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 p-3 text-sm font-semibold text-gray-900 dark:from-gray-700 dark:to-gray-800 dark:text-white"
                :class="isStatisticsCard(tableCard.title) ? 'grid-cols-3' : 'grid-cols-2'"
              >
                <div>{{ getColumnHeader(tableCard.title) }}</div>
                <div>{{ isStatisticsCard(tableCard.title) ? 'Members' : 'Count' }}</div>
                <div v-if="isStatisticsCard(tableCard.title)">Families</div>
              </div>

              <!-- Table Rows -->
              <div class="custom-scrollbar max-h-64 space-y-2 overflow-y-auto">
                <div
                  v-for="(row, index) in tableCard.data"
                  :key="index"
                  class="grid gap-4 rounded-lg bg-gray-50 p-3 text-sm transition-colors duration-200 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700"
                  :class="isStatisticsCard(tableCard.title) ? 'grid-cols-3' : 'grid-cols-2'"
                >
                  <div class="font-medium text-gray-900 dark:text-white">{{ index || 'Other' }}</div>
                  <div v-if="isStatisticsCard(tableCard.title)" class="font-semibold text-blue-600 dark:text-blue-400">{{ row.members }}</div>
                  <div v-else class="font-semibold text-blue-600 dark:text-blue-400">{{ row }}</div>
                  <div v-if="isStatisticsCard(tableCard.title)" class="font-semibold text-green-600 dark:text-green-400">{{ row.families }}</div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Placeholder Section -->
      <div class="mt-8">
        <div
          class="bg-opacity-80 dark:bg-opacity-80 border-opacity-20 dark:border-opacity-50 rounded-2xl border border-white bg-[#ffffff] p-8 shadow-xl backdrop-blur-xl dark:border-slate-700 dark:bg-slate-800"
        >
          <div class="text-center">
            <h3 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white">Additional Features Coming Soon</h3>
            <p class="mb-6 text-gray-600 dark:text-gray-400">More dashboard widgets and analytics will be added here</p>
            <PlaceholderPattern />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Custom scrollbar styling */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}

/* Dark mode scrollbar */
.dark .custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #434190 0%, #4c1d95 100%);
}
</style>
