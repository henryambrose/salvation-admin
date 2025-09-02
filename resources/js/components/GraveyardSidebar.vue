<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import NavMainGrouped from '@/components/NavMainGrouped.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { Link, usePage } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import { BarChart3, Box, Calendar, Clock, Cross, IndianRupee, Grid3X3, Home, Settings, UserCheck, Users } from 'lucide-vue-next';
import { computed, watch } from 'vue';

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
    items: [{ title: 'Dashboard', href: '/graveyard', icon: Home, show: true }],
  },
  {
    label: 'Structures',
    items: [
      { title: 'Graves', href: '/graveyard/graves', icon: Users, show: can('read-grave') || true },
      {
        title: 'Valid Members',
        href: '/graveyard/valid-members',
        icon: UserCheck,
        show: can('read-valid-member') || true,
      },
    ],
  },
  {
    label: 'Administration',
    items: [
      { title: 'Permanent Graves', href: '/graveyard/permanent-graves', icon: Cross, show: can('read-permanent-grave') || true },
      { title: 'Temporary Graves', href: '/graveyard/temporary-graves', icon: Clock, show: can('read-temporary-grave') || true },
      { title: 'Niches', href: '/graveyard/niches', icon: Box, show: can('read-niche') || true },
      { title: 'Service Types', href: '/graveyard/service-types', icon: Settings, show: can('read-service-type') || true },
    ],
  },
  {
    label: 'Reports & Analytics',
    items: [{ title: 'Reports', href: '/graveyard/reports', icon: BarChart3, show: can('view-graveyard-reports') || true }],
  },
]);

const filteredGraveyardNavigationGroups = computed(() =>
  graveyardNavigationGroups.value
    .map((group) => ({
      ...group,
      items: group.items.filter((item) => item.show),
    }))
    .filter((group) => group.items.length > 0),
);

// Watch for route changes
watch(
  () => page.url,
  (newUrl) => {
    // console.log('🔄 GraveyardSidebar - Route changed to:', newUrl);
  },
  { immediate: true },
);
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
