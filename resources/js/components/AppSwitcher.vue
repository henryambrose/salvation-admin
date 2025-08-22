<template>
  <div class="app-switcher">
    <!-- App Switcher Dropdown -->
    <div class="relative">
      <button
        @click="isOpen = !isOpen"
        class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <component :is="currentApp.icon" class="w-4 h-4" />
        <span>{{ currentApp.name }}</span>
        <ChevronDownIcon class="w-4 h-4" />
      </button>

      <!-- Dropdown Menu -->
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
      >
        <div class="py-1">
          <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
            Switch Application
          </div>
          
          <Link
            v-for="app in availableApps"
            :key="app.id"
            :href="app.href"
            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150"
            @click="isOpen = false"
          >
            <component :is="app.icon" class="w-5 h-5 mr-3 text-gray-400" />
            <div>
              <div class="font-medium">{{ app.name }}</div>
              <div class="text-xs text-gray-500">{{ app.description }}</div>
            </div>
            <CheckIcon v-if="app.id === currentApp.id" class="w-4 h-4 ml-auto text-green-500" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Click outside to close -->
    <div
      v-if="isOpen"
      class="fixed inset-0 z-40"
      @click="isOpen = false"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
  Users, 
  DollarSign, 
  ChevronDownIcon, 
  CheckIcon 
} from 'lucide-vue-next';

const isOpen = ref(false);

// Get current page info
const page = usePage();
const currentPath = page.url;

// Define available apps
const availableApps = [
  {
    id: 'members',
    name: 'Members',
    description: 'Parish member management',
    href: '/',
    icon: Users,
    color: 'text-blue-600'
  },
  {
    id: 'fund',
    name: 'Fund',
    description: 'Financial management & mass intentions',
    href: '/fund',
    icon: DollarSign,
    color: 'text-green-600'
  }
];

// Determine current app based on URL
const currentApp = computed(() => {
  if (currentPath.startsWith('/fund')) {
    return availableApps.find(app => app.id === 'fund') || availableApps[0];
  }
  return availableApps.find(app => app.id === 'members') || availableApps[0];
});

// Close dropdown when route changes
const closeDropdown = () => {
  isOpen.value = false;
};

// Watch for route changes
import { watch } from 'vue';
watch(() => page.url, closeDropdown);
</script>

<style scoped>
.app-switcher {
  position: relative;
}
</style>
