<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage, router } from '@inertiajs/vue3';

interface NavigationGroup {
  label: string;
  items: NavItem[];
}

defineProps<{
  groups: NavigationGroup[];
}>();

const page = usePage<SharedData>();

const isActivePage = (itemHref: string, currentUrl: string): boolean => {
  // Handle different URL patterns for the same page
  const urlVariations = {
    '/member/index': ['/member/index', '/member'],
    '/community': ['/community/index','/community'],
    '/parish': ['/parish/index','/parish'],
    '/zone': ['/zone/index','/zone'],
    '/community-clusters': ['/community-clusters/index','/community-clusters'],
    '/clusters': ['/clusters/index','/clusters'],
    '/cells-and-association': ['/cells-and-association/index','/cells-and-association'],
    '/cells-and-association-members': ['/cells-and-association-members/index','/cells-and-association-members'],
    '/scc-head': ['/scc-head/index','/scc-head'],
    '/ppc-head': ['/ppc-head/index','/ppc-head'],
    '/relationship': ['/relationship/index','/relationship'],
    '/designation': ['/designation/index','/designation'],
    '/age-group': ['/age-group/index','/age-group'],
    '/blood-group': ['/blood-group/index','/blood-group'],
    '/gender': ['/gender/index','/gender'],
    '/family-income-range': ['/family-income-range/index','/family-income-range'],
    '/status': ['/status/index','/status'],
    '/country': ['/country/index','/country'],
    '/state': ['/state/index','/state'],
    '/city': ['/city/index','/city'],
    '/town': ['/town/index','/town'],
    '/users': ['/users/index','/users'],
  };
  
  // Check if the current URL matches any variation of the item href
  const variations = urlVariations[itemHref as keyof typeof urlVariations];
  if (variations) {
    return variations.includes(currentUrl);
  }
  
  // Default exact match for any other pages
  return itemHref === currentUrl;
};

const handleNavigation = (href: string) => {
  // Navigate to the new page without scroll restoration
  router.visit(href, {
    preserveScroll: false,
    preserveState: false,
  });
};
</script>

<template>
  <div class="space-y-6">
    <SidebarGroup v-for="group in groups" :key="group.label" class="px-2 py-0">
      <SidebarGroupLabel class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3 px-2 bg-blue-50 dark:bg-blue-900/20 py-1 rounded-md border-l-2 border-blue-500">
        {{ group.label }}
      </SidebarGroupLabel>
      <SidebarMenu class="space-y-1">
        <SidebarMenuItem v-for="item in group.items" :key="item.title">
          <SidebarMenuButton :is-active="isActivePage(item.href, page.url)" :tooltip="item.title" class="gap-1" @click="handleNavigation(item.href)">
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarGroup>
  </div>
</template> 