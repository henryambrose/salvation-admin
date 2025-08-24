<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="'/fund'">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
      
      <!-- App Switcher on the right side -->
      <div class="ml-auto">
        <AppSwitcher />
      </div>
    </SidebarHeader>

    <SidebarContent>
      <NavMainGrouped :groups="fundNavigationGroups" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>

<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavMainGrouped from '@/components/NavMainGrouped.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { type NavItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import { 
  DollarSign, 
  Calendar, 
  Users, 
  FileText, 
  BarChart3, 
  Settings,
  Home
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';
import AppSwitcher from '@/components/AppSwitcher.vue';

const { can } = permissionHelpers();

const resolveIcon = (iconName: string) => {
  return (Icons as any)[iconName] || Icons.HelpCircle; // fallback icon
};

const page = usePage();

// Fund-specific navigation groups
const fundNavigationGroups = [
  {
    label: 'Fund Management',
    items: [
      {
        title: 'Dashboard',
        href: '/fund',
        icon: Home,
        show: true,
      },
      {
        title: 'Annual Contributions',
        href: '/fund/annual-contributions',
        icon: DollarSign,
        show: can('read-annual-contributions') || true, // Default to true for now
      },
      {
        title: 'Mass Intentions',
        href: '/fund/mass-intentions',
        icon: Calendar,
        show: can('read-mass-intentions') || true, // Default to true for now
      },
    ]
  },
  {
    label: 'Reports & Analytics',
    items: [
      {
        title: 'Financial Reports',
        href: '/fund/reports/financial',
        icon: BarChart3,
        show: can('read-financial-reports') || true,
      },
      {
        title: 'Contribution History',
        href: '/fund/reports/contributions',
        icon: FileText,
        show: can('read-contribution-reports') || true,
      },
    ]
  },
  {
    label: 'Administration',
    items: [
      {
        title: 'Settings',
        href: '/fund/settings',
        icon: Settings,
        show: can('read-fund-settings') || true,
      },
    ]
  }
];

const filteredFundNavigationGroups = fundNavigationGroups.map(group => ({
  ...group,
  items: group.items.filter(item => item.show)
})).filter(group => group.items.length > 0);
</script>
