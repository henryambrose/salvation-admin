<script setup lang="ts">
import NavMainGrouped from '@/components/NavMainGrouped.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import {
  Home,
  MapPin,
  Grid3X3,
  Users,
  Calendar,
  Settings2,
  DollarSign,
  UserCheck,
  BarChart3,
  Settings,
  Cross,
  Clock,
  Box
} from 'lucide-vue-next';
import { computed, watch } from 'vue';
import AppLogo from '@/components/AppLogo.vue';

const { can } = permissionHelpers();

const resolveIcon = (iconName: string) => {
  return (Icons as any)[iconName] || Icons.HelpCircle; // fallback icon
};

// Get current page for debugging
const page = usePage();

// Grouped navigation like Fund sidebar
const graveyardNavigationGroups = computed(() => [
  {
    label: 'Overview',
    items: [
      { title: 'Dashboard', href: '/graveyard', icon: Home, show: true },
    ],
  },
  {
    label: 'Structures',
    items: [
      { title: 'Cemeteries', href: '/graveyard/cemeteries', icon: MapPin, show: can('read-cemetery') || true },
      { title: 'Sections', href: '/graveyard/sections', icon: Grid3X3, show: can('read-section') || true },
      { title: 'Graves', href: '/graveyard/graves', icon: Users, show: can('read-grave') || true },
      { title: 'Niches', href: '/graveyard/niches', icon: Box, show: can('read-niche') || true },
      { title: 'Permanent Valid Members', href: '/graveyard/permanent-valid-members', icon: UserCheck, show: can('read-permanent-valid-member') || true },
    ],
  },
  {
    label: 'Operations',
    items: [
      { title: 'Burials', href: '/graveyard/burials', icon: Calendar, show: can('read-grave-booking') || true },
      { title: 'Maintenance', href: '/graveyard/maintenance', icon: Settings2, show: can('read-maintenance') || true },
      { title: 'Visitors', href: '/graveyard/visitors', icon: UserCheck, show: can('read-visitor') || true },
    ],
  },
  {
    label: 'Finance',
    items: [
      { title: 'Finances', href: '/graveyard/finances', icon: DollarSign, show: can('read-graveyard-finance') || true },
    ],
  },
  {
    label: 'Administration',
    items: [
      { title: 'Permanent Graves', href: '/graveyard/permanent-graves', icon: Cross, show: can('read-permanent-grave') || true },
      { title: 'Temporary Graves', href: '/graveyard/temporary-graves', icon: Clock, show: can('read-temporary-grave') || true },
      { title: 'Niches', href: '/graveyard/niches', icon: Box, show: can('read-niche') || true },
      { title: 'Service Types', href: '/graveyard/service-types', icon: Settings, show: can('read-service-type') || true },
      { title: 'Settings', href: '/settings/profile', icon: Settings, show: true },
    ],
  },
  {
    label: 'Reports & Analytics',
    items: [
      { title: 'Reports', href: '/graveyard/reports', icon: BarChart3, show: can('view-graveyard-reports') || true },
    ],
  },
]);

const filteredGraveyardNavigationGroups = computed(() =>
  graveyardNavigationGroups.value.map(group => ({
    ...group,
    items: group.items.filter(item => item.show)
  })).filter(group => group.items.length > 0)
);

// Watch for route changes
watch(() => page.url, (newUrl) => {
  // console.log('🔄 GraveyardSidebar - Route changed to:', newUrl);
}, { immediate: true });
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="'/graveyard'">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>

      <!-- App Switcher on the right side -->
      <!-- <div class="ml-auto">
        <AppSwitcher />
      </div> -->
    </SidebarHeader>

    <SidebarContent>
      <NavMainGrouped :groups="filteredGraveyardNavigationGroups" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
</template>
