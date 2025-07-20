<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { type NavItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import { BookOpen, LogOut, UserCircle } from 'lucide-vue-next';
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
];

const filteredFooterNavItems = footerNavItems.filter((item) => item.show);

function handleLogout() {
  router.post(
    route('logout'),
    {},
    {
      onSuccess: () => {
        router.visit(route('login'));
      },
    },
  );
}
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
      <NavMain :items="filteredMainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <!-- <NavFooter :items="footerNavItems" /> -->
      <NavFooter :items="filteredFooterNavItems" />
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton as-child>
            <Link :href="route('logout')" method="post" as="button" @click.prevent="handleLogout" class="flex w-full items-center">
              <LogOut class="mr-2 h-4 w-4" />
              Log out
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
