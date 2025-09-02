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
      <!-- <div class="ml-auto">
        <AppSwitcher />
      </div> -->
    </SidebarHeader>

    <SidebarContent>
      <NavMainGrouped :groups="filteredFundNavigationGroups" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
</template>

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
  IndianRupee, 
  BarChart3, 
  Settings, 
  FolderOpen, 
  FileText,
  Crown, 
  Bot,
  Clock
} from 'lucide-vue-next';
import { computed, watch } from 'vue';
import AppLogo from './AppLogo.vue';

const { can } = permissionHelpers();

const resolveIcon = (iconName: string) => {
  return (Icons as any)[iconName] || Icons.HelpCircle; // fallback icon
};

// Get current page for debugging
const page = usePage();

// Fund-specific navigation groups - make them reactive
const fundNavigationGroups = computed(() => [
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
        icon: IndianRupee,
        show: can('read-annual-contributions') || true, // Default to true for now
      },
      {
        title: 'Mass Intentions',
        href: '/fund/mass-intentions',
        icon: FileText,
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
        title: 'Categories',
        href: '/fund/categories',
        icon: FolderOpen,
        show: can('read-fund-category') || true,
      },
      {
        title: 'Mass Intention Types',
        href: '/fund/mass-intention-types',
        icon: FileText,
        show: can('read-mass-intention-types') || true,
      },
      {
        title: 'Mass Types',
        href: '/fund/mass-types',
        icon: Clock,
        show: can('read-mass-types') || true,
      },
      {
        title: 'Payment Methods',
        href: '/fund/payment-methods',
        icon: IndianRupee,
        show: can('read-payment-methods') || true,
      },
      {
        title: 'Settings',
        href: '/settings/profile',
        icon: Settings,
        show: true,
      },
    ]
  }
]);

const filteredFundNavigationGroups = computed(() => 
  fundNavigationGroups.value.map(group => ({
    ...group,
    items: group.items.filter(item => item.show)
  })).filter(group => group.items.length > 0)
);

// Watch for route changes
watch(() => page.url, (newUrl) => {
  // console.log('🔄 FundSidebar - Route changed to:', newUrl);
}, { immediate: true });
</script>
