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
  BookOpen, 
  UserCircle, 
  MessageSquare, 
  Users, 
  MapPin, 
  Network, 
  Building2, 
  Heart, 
  UserCheck, 
  Calendar, 
  Church, 
  Droplets, 
  CheckCircle, 
  Globe, 
  Map, 
  Building, 
  Home,
  Crown,
  Shield,
  Bot
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const { can } = permissionHelpers();

const resolveIcon = (iconName: string) => {
  return (Icons as any)[iconName] || Icons.HelpCircle; // fallback icon
};

const page = usePage();

const modules = computed(() => {
  return (page.props.modules as Array<any>).map((module: any) => ({
    title: module.name,
    href: '/' + module.slug,
    icon: resolveIcon(module.icon),
    show: can(module.slug),
  }));
});

const mainNavItems: NavItem[] = modules.value;

const filteredMainNavItems = mainNavItems.filter((item) => item.show);

// Organized navigation groups
const navigationGroups = [
  {
    label: 'Core Management',
    items: [
      {
        title: 'Members',
        href: '/member/index',
        icon: Users,
        show: can('view-member'),
      },
      {
        title: 'Community',
        href: '/community',
        icon: Building2,
        show: can('view-community'),
      },
      {
        title: 'Parishes',
        href: '/parish',
        icon: Church,
        show: can('view-parish'),
      },
    ]
  },
  {
    label: 'Organizational Structure',
    items: [
      {
        title: 'Zone',
        href: '/zone',
        icon: MapPin,
        show: can('view-zone'),
      },
      {
        title: 'Community Clusters',
        href: '/community-clusters',
        icon: Network,
        show: (page.props.auth as any)?.roles?.includes('superadmin') || (page.props.auth as any)?.roles?.includes('admin'),
      },
      {
        title: 'Clusters',
        href: '/clusters',
        icon: Network,
        show: (page.props.auth as any)?.roles?.includes('superadmin') || (page.props.auth as any)?.roles?.includes('admin'),
      },
      {
        title: 'Cells and Association',
        href: '/cells-and-association',
        icon: Heart,
        show: can('view-cells-and-association'),
      },
      {
        title: 'Cells Association Members',
        href: '/cells-and-association-members',
        icon: UserCheck,
        show: can('view-cells-and-association-member'),
      },
      
    ]
  },
  {
    label: 'Leadership',
    items: [
      {
        title: 'SCC Head',
        href: '/scc-head',
        icon: Crown,
        show: can('view-s-c-c-head'),
      },
      {
        title: 'PPC Head',
        href: '/ppc-head',
        icon: Shield,
        show: can('view-p-p-c-head'),
      },
    ]
  },
  {
    label: 'Member Attributes',
    items: [
      {
        title: 'Relationship',
        href: '/relationship',
        icon: UserCheck,
        show: can('view-relationship'),
      },
      {
        title: 'Designation',
        href: '/designation',
        icon: Crown,
        show: can('view-designation'),
      },
      {
        title: 'Age Group',
        href: '/age-group',
        icon: Calendar,
        show: can('view-age-group'),
      },
      {
        title: 'Blood Group',
        href: '/blood-group',
        icon: Droplets,
        show: can('view-blood-group'),
      },
      {
        title: 'Gender',
        href: '/gender',
        icon: UserCircle,
        show: can('view-gender'),
      },
      {
        title: 'Status',
        href: '/status',
        icon: CheckCircle,
        show: can('view-status'),
      },
      {
        title: 'Family Income Range',
        href: '/family-income-range',
        icon: Heart,
        show: can('view-family-income-range'),
      },
    ]
  },
  {
    label: 'Geographic Data',
    items: [
      {
        title: 'Countries',
        href: '/country',
        icon: Globe,
        show: can('view-country'),
      },
      {
        title: 'State',
        href: '/state',
        icon: Map,
        show: can('view-state'),
      },
      {
        title: 'City',
        href: '/city',
        icon: Building,
        show: can('view-city'),
      },
      {
        title: 'Town',
        href: '/town',
        icon: Home,
        show: can('view-town'),
      },
      
    ]
  },
  {
    label: 'AI Assistance',
      items: [
        {
          title: 'AI Chat',
          href: '/chat',
          icon: Bot,
          show: (page.props.auth as any)?.roles?.includes('superadmin') || false,
        },
      ]
  }
];

const filteredNavigationGroups = navigationGroups.map(group => ({
  ...group,
  items: group.items.filter(item => item.show)
})).filter(group => group.items.length > 0);


</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <!-- <NavMain :items="mainNavItems" /> -->
      <NavMainGrouped :groups="filteredNavigationGroups" />
    </SidebarContent>

    <SidebarFooter>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
