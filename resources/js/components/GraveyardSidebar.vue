<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import NavMainGrouped from '@/components/NavMainGrouped.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { useScrollRestoration } from '@/composables/useScrollRestoration';
import { Link, usePage } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import {
  BarChart3,
  BookOpen,
  Box,
  Clock,
  CreditCard,
  Cross,
  FileText,
  Home,
  IndianRupee,
  Palette,
  Settings,
  Tags,
  Trash2,
  UserCheck,
  Users,
} from 'lucide-vue-next';
import { computed, watch } from 'vue';

const { can } = permissionHelpers();

// Initialize scroll restoration for the sidebar
useScrollRestoration();

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
    label: 'Register',
    items: [
      {
        title: 'Permanent Grave Register',
        href: '/graveyard/permanent-grave-bookings',
        icon: Cross,
        show: can('read-permanent-grave-booking') || true,
      },
      {
        title: 'Temporary Grave Register',
        href: '/graveyard/temporary-grave-bookings',
        icon: Clock,
        show: can('read-temporary-grave-booking') || true,
      },
      { title: 'Niche Tra. Register', href: '/graveyard/niche-transfers', icon: BookOpen, show: can('read-niche-transfer') || true },
    ],
  },
  {
    label: 'Payments & Finance',
    items: [{ title: 'All Payments', href: '/graveyard/payments', icon: CreditCard, show: can('read-payment') || true }],
  },
  {
    label: 'Obituary Management',
    items: [
      { title: 'All Obituaries', href: '/graveyard/obituaries', icon: FileText, show: true },
      { title: 'Obituary Plans', href: '/graveyard/obituary-plans', icon: Tags, show: can('access-graveyard') },
      { title: 'Condolences', href: '/graveyard/obituaries/condolences/manage', icon: BookOpen, show: true },
      { title: 'File Cleanup', href: '/graveyard/obituaries/cleanup', icon: Trash2, show: true },
      { title: 'Obituary Managers', href: '/graveyard/obituary-managers', icon: Users, show: can('read-obituary-manager') || true },
      {
        title: 'Background Themes',
        href: '/graveyard/obituary-background-themes',
        icon: Palette,
        show: can('read-obituary-backgroud-theme') || true,
      },
    ],
  },
  {
    label: 'Administration',
    items: [
      { title: 'Permanent Graves', href: '/graveyard/permanent-graves', icon: Cross, show: can('read-permanent-grave') || true },
      { title: 'Temporary Graves', href: '/graveyard/temporary-graves', icon: Clock, show: can('read-temporary-grave') || true },
      { title: 'Niches', href: '/graveyard/niches', icon: Box, show: can('read-niche') || true },
      { title: 'Valid Members', href: '/graveyard/valid-members', icon: UserCheck, show: can('read-valid-member') || true },
      { title: 'Annual Maintenance Fees', href: '/graveyard/annual-maintenance-fees', icon: IndianRupee, show: can('list-annual-maintenance-fees') || true },
      { title: 'Grave Categories', href: '/graveyard/grave-categories', icon: Tags, show: can('read-grave-category') || true },
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
    // Route change handler - currently no action needed
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
    </SidebarHeader>

    <SidebarContent>
      <NavMainGrouped :groups="filteredGraveyardNavigationGroups" :current-page="page.url" :last-page="page.url" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
</template>
