<script setup lang="ts">
import NavMainGrouped from '@/components/NavMainGrouped.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { useScrollRestoration } from '@/composables/useScrollRestoration';
import { Link, usePage } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import {
  Award,
  Baby,
  Bot,
  Building,
  Building2,
  Calendar,
  CheckCircle,
  Church,
  Crown,
  Droplets,
  FileText,
  Globe,
  Heart,
  Home,
  Map,
  MapPin,
  Network,
  Shield,
  Skull,
  UserCheck,
  UserCircle,
  Users,
  UsersRound,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';
import FundSidebar from './FundSidebar.vue';
import GraveyardSidebar from './GraveyardSidebar.vue';

const { can } = permissionHelpers();

// Initialize scroll restoration for the sidebar
useScrollRestoration();

const resolveIcon = (iconName: string) => {
  return (Icons as any)[iconName] || Icons.HelpCircle; // fallback icon
};

const page = usePage();

// Organized navigation groups
const navigationGroups = [
  {
    label: 'Core Management',
    items: [
      {
        title: 'Members',
        href: '/member/index',
        icon: Users,
        show: can('read-member'),
      },
      {
        title: 'External Members',
        href: '/external-members',
        icon: Globe,
        show: can('read-external-member'),
      },
      {
        title: 'Community',
        href: '/community',
        icon: Building2,
        show: can('read-community'),
      },
      {
        title: 'Parishes',
        href: '/parish',
        icon: Church,
        show: can('read-parish'),
      },
    ],
  },
  {
    label: 'Organizational Structure',
    items: [
      {
        title: 'Zone',
        href: '/zone',
        icon: MapPin,
        show: can('read-zone'),
      },
      {
        title: 'Community Clusters',
        href: '/community-clusters',
        icon: Network,
        show: can('read-community-cluster'),
      },
      {
        title: 'Clusters',
        href: '/clusters',
        icon: Network,
        show: can('read-cluster'),
      },
      {
        title: 'Cells and Association',
        href: '/cells-and-association',
        icon: Heart,
        show: can('read-cells-and-association'),
      },
      {
        title: 'Cells Association Members',
        href: '/cells-and-association-members',
        icon: UserCheck,
        show: can('read-cells-and-association-member'),
      },
    ],
  },
  {
    label: 'Certificates',
    items: [
      {
        title: 'View Certificates',
        href: '/certificates',
        icon: FileText,
        show: can('read-certificate'),
      },
      {
        title: 'Generate Certificate',
        href: '/certificates/generate',
        icon: Award,
        show: can('create-certificate'),
      },
      {
        title: 'Templates',
        href: '/certificates/templates',
        icon: FileText,
        show: can('read-certificate-template'),
      },
      {
        title: 'Baptism Records',
        href: '/baptism-records',
        icon: Baby,
        show: can('read-certificate'),
      },
      {
        title: 'Marriage Records',
        href: '/marriage-records',
        icon: UsersRound,
        show: can('read-certificate'),
      },
      {
        title: 'Death Records',
        href: '/death-records',
        icon: Skull,
        show: can('read-certificate'),
      },
    ],
  },
  {
    label: 'Leadership',
    items: [
      {
        title: 'SCC Head',
        href: '/scc-head',
        icon: Crown,
        show: can('read-s-c-c-head'),
      },
      {
        title: 'PPC Head',
        href: '/ppc-head',
        icon: Shield,
        show: can('read-p-p-c-head'),
      },
    ],
  },
  {
    label: 'Member Attributes',
    items: [
      {
        title: 'Relationship',
        href: '/relationship',
        icon: UserCheck,
        show: can('read-relationship'),
      },
      {
        title: 'Designation',
        href: '/designation',
        icon: Crown,
        show: can('read-designation'),
      },
      {
        title: 'Age Group',
        href: '/age-group',
        icon: Calendar,
        show: can('read-age-group'),
      },
      {
        title: 'Blood Group',
        href: '/blood-group',
        icon: Droplets,
        show: can('read-blood-group'),
      },
      {
        title: 'Gender',
        href: '/gender',
        icon: UserCircle,
        show: can('read-gender'),
      },
      {
        title: 'Status',
        href: '/status',
        icon: CheckCircle,
        show: can('read-status'),
      },
      {
        title: 'Income Range',
        href: '/income-range',
        icon: Heart,
        show: can('read-income-range'),
      },
    ],
  },
  {
    label: 'Geographic Data',
    items: [
      {
        title: 'Countries',
        href: '/country',
        icon: Globe,
        show: can('read-country'),
      },
      {
        title: 'State',
        href: '/state',
        icon: Map,
        show: can('read-state'),
      },
      {
        title: 'City',
        href: '/city',
        icon: Building,
        show: can('read-city'),
      },
      {
        title: 'Town',
        href: '/town',
        icon: Home,
        show: can('read-town'),
      },
    ],
  },
  {
    label: 'System Management',
    items: [
      {
        title: 'Users',
        href: route('users.index'),
        icon: UserCircle,
        show: can('read-users') || (page.props.auth as any)?.roles?.includes('super admin'),
      },
      {
        title: 'Role Management',
        href: '/roles-permissions',
        icon: Shield,
        show: can('read-role'),
      },
      {
        title: 'Audit Logs',
        href: route('audit.logs.index'),
        icon: FileText,
        show: (page.props.auth as any)?.is_superadmin || false,
      },
    ],
  },
  {
    label: 'AI Assistance',
    items: [
      {
        title: 'AI Chat',
        href: '/chat',
        icon: Bot,
        show: (page.props.auth as any)?.roles?.includes('super admin') || false,
      },
    ],
  },
];

const filteredNavigationGroups = navigationGroups
  .map((group) => ({
    ...group,
    items: group.items.filter((item) => item.show),
  }))
  .filter((group) => group.items.length > 0);

// Determine which app we're in based on current URL
const currentApp = computed(() => {
  const currentPath = page.url;
  if (currentPath.startsWith('/fund')) {
    return 'fund';
  }
  if (currentPath.startsWith('/graveyard')) {
    return 'graveyard';
  }
  return 'members';
});

// Determine which logo link to use
const logoLink = computed(() => {
  if (currentApp.value === 'fund') {
    return route('fund.dashboard');
  }
  if (currentApp.value === 'graveyard') {
    return route('graveyard.dashboard');
  }
  return route('dashboard');
});
</script>

<template>
  <!-- Render Fund Sidebar when in Fund app -->
  <FundSidebar v-if="currentApp === 'fund'" />
  <GraveyardSidebar v-else-if="currentApp === 'graveyard'" />

  <!-- Render Members Sidebar when in Members app -->
  <Sidebar v-else collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="logoLink">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <!-- <NavMain :items="mainNavItems" /> -->
      <NavMainGrouped :groups="filteredNavigationGroups" :current-page="page.url" :last-page="page.url" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
