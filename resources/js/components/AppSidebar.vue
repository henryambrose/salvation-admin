<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { type NavItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import { BookOpen, UserCircle, MessageSquare } from 'lucide-vue-next';
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

// Custom ordered navigation items
const orderedNavItems: NavItem[] = [
  {
    title: 'Members',
    href: '/member/index',
    icon: UserCircle,
    show: can('view-member'),
  },
  {
    title: 'Community',
    href: '/community',
    icon: BookOpen,
    show: can('view-community'),
  },
  {
    title: 'Zone',
    href: '/zone',
    icon: BookOpen,
    show: can('view-zone'),
  },
  {
    title: 'Community Clusters',
    href: '/community-clusters',
    icon: BookOpen,
    show: (page.props.auth as any)?.roles?.includes('superadmin') || (page.props.auth as any)?.roles?.includes('admin'),
  },
  {
    title: 'Clusters',
    href: '/clusters',
    icon: BookOpen,
    show: (page.props.auth as any)?.roles?.includes('superadmin') || (page.props.auth as any)?.roles?.includes('admin'),
  },
  {
    title: 'SCC Head',
    href: '/scc-head',
    icon: UserCircle,
    show: can('view-s-c-c-head'),
  },
  {
    title: 'PPC Head',
    href: '/ppc-head',
    icon: UserCircle,
    show: can('view-p-p-c-head'),
  },
  {
    title: 'Relationship',
    href: '/relationship',
    icon: BookOpen,
    show: can('view-relationship'),
  },
  {
    title: 'Age Group',
    href: '/age-group',
    icon: BookOpen,
    show: can('view-age-group'),
  },
  {
    title: 'Parishes',
    href: '/parish',
    icon: BookOpen,
    show: can('view-parish'),
  },
  {
    title: 'Blood Group',
    href: '/blood-group',
    icon: BookOpen,
    show: can('view-blood-group'),
  },
  {
    title: 'Countries',
    href: '/country',
    icon: BookOpen,
    show: can('view-country'),
  },
  {
    title: 'State',
    href: '/state',
    icon: BookOpen,
    show: can('view-state'),
  },
  {
    title: 'Town',
    href: '/town',
    icon: BookOpen,
    show: can('view-town'),
  },
];

const filteredOrderedNavItems = orderedNavItems.filter((item) => item.show);

const footerNavItems: NavItem[] = [
  {
    title: 'Users',
    href: '/users/index',
    icon: UserCircle,
    show: can('view-Users'),
  },
  {
    title: 'Role Permissions',
    href: '/roles-permissions',
    icon: BookOpen,
    show: can('update-role-permissions'),
  },
  {
    title: 'AI Chat',
    href: '/chat',
    icon: MessageSquare,
    show: (page.props.auth as any)?.roles?.includes('superadmin') || false,
  },
];

const filteredFooterNavItems = footerNavItems.filter((item) => item.show);
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
      <NavMain :items="filteredOrderedNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <!-- <NavFooter :items="footerNavItems" /> -->
      <NavFooter :items="filteredFooterNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
