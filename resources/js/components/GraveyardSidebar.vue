<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { 
  LayoutDashboard, 
  MapPin, 
  Grid3X3, 
  Users, 
  Calendar, 
  Settings2, 
  DollarSign, 
  UserCheck, 
  BarChart3,
  Settings 
} from 'lucide-vue-next';

const { can } = permissionHelpers();
const page = usePage();

// Get current URL for active link detection
const currentUrl = computed(() => {
  return (page.props.ziggy as any)?.location || '';
});

const isActive = (href: string) => {
  return currentUrl.value.includes(href);
};

const navItems = [
  {
    title: 'Dashboard',
    href: '/graveyard',
    icon: LayoutDashboard,
    show: true, // TODO: Add permission check
  },
  {
    title: 'Cemeteries',
    href: '/graveyard/cemeteries',
    icon: MapPin,
    show: can('read-graveyard-cemetery') || true,
  },
  {
    title: 'Sections',
    href: '/graveyard/sections',
    icon: Grid3X3,
    show: can('read-graveyard-section') || true,
  },
  {
    title: 'Graves',
    href: '/graveyard/graves',
    icon: Users,
    show: can('read-graveyard-grave') || true,
  },
  {
    title: 'Burials',
    href: '/graveyard/burials',
    icon: Calendar,
    show: can('read-graveyard-burial') || true,
  },
  {
    title: 'Maintenance',
    href: '/graveyard/maintenance',
    icon: Settings2,
    show: can('read-graveyard-maintenance') || true,
  },
  {
    title: 'Finances',
    href: '/graveyard/finances',
    icon: DollarSign,
    show: can('read-graveyard-finance') || true,
  },
  {
    title: 'Visitors',
    href: '/graveyard/visitors',
    icon: UserCheck,
    show: can('read-graveyard-visitor') || true,
  },
  {
    title: 'Reports',
    href: '/graveyard/reports',
    icon: BarChart3,
    show: can('read-graveyard-report') || true,
  },
  {
    title: 'Settings',
    href: '/settings/profile',
    icon: Settings,
    show: true,
  },
];
</script>

<template>
  <nav class="space-y-1 px-2 py-4">
    <!-- Graveyard Header -->
    <div class="mb-4 px-3">
      <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
        Graveyard Management
      </h3>
    </div>

    <!-- Navigation Links -->
    <template v-for="item in navItems" :key="item.href">
      <Link
        v-if="item.show"
        :href="item.href"
        :class="[
          'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200',
          isActive(item.href)
            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200'
            : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white'
        ]"
      >
        <component
          :is="item.icon"
          :class="[
            'mr-3 h-5 w-5 flex-shrink-0',
            isActive(item.href)
              ? 'text-blue-500 dark:text-blue-300'
              : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300'
          ]"
        />
        {{ item.title }}
      </Link>
    </template>
  </nav>
</template>
