<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { useScrollRestoration } from '@/composables/useScrollRestoration';
import { type NavItem, type SharedData } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { nextTick, onUnmounted, ref } from 'vue';

interface NavigationGroup {
  label: string;
  items: NavItem[];
}

defineProps<{
  groups: NavigationGroup[];
  currentPage?: string;
  lastPage?: string;
}>();

const page = usePage<SharedData>();
const isComponentMounted = ref(true);
const { saveScrollPosition, restoreScrollPosition } = useScrollRestoration();

// Cleanup on unmount
onUnmounted(() => {
  isComponentMounted.value = false;
});

// Get current URL path from window.location for more reliable path detection
const getCurrentPath = () => {
  const path = window.location.pathname;
  return path;
};

const isActivePage = (itemHref: string, currentUrl: string): boolean => {
  // Handle Fund routes with more precise matching

  // Simple test for Fund routes
  if (itemHref.startsWith('/fund')) {
    // Fund route detected
  }

  // Handle Fund dashboard - should be active for /fund and /fund/dashboard
  if (itemHref === '/fund') {
    const isActive = currentUrl === '/fund' || currentUrl === '/fund/dashboard';
    return isActive;
  }

  // Handle Fund sub-routes - RETURN EARLY for all Fund routes
  if (itemHref.startsWith('/fund/')) {
    // For Fund sub-routes, check if current URL starts with the item href
    // This handles: /fund/annual-contributions, /fund/categories, etc.
    // AND their nested routes like /fund/annual-contributions/create, /fund/annual-contributions/123/edit
    const isActive = currentUrl.startsWith(itemHref);
    return isActive; // RETURN EARLY - don't continue to other logic
  }

  // Handle Graveyard routes explicitly (mirror Fund logic)
  if (itemHref === '/graveyard') {
    // Active on base dashboard
    return currentUrl === '/graveyard' || currentUrl === '/graveyard/dashboard';
  }

  if (itemHref.startsWith('/graveyard/')) {
    // Handle specific graveyard route conflicts
    if (itemHref === '/graveyard/obituaries') {
      // For "All Obituaries", only match exact route or its direct children, not nested subroutes
      return currentUrl === itemHref ||
             currentUrl.startsWith(itemHref + '/') &&
             !currentUrl.includes('/condolences/') &&
             !currentUrl.includes('/cleanup');
    }

    // For other graveyard routes, use standard nested route detection
    return currentUrl.startsWith(itemHref);
  }

  // Handle clashing URLs first - use exact match for URLs that might clash with others
  if (itemHref === '/cells-and-association' || itemHref === '/cells-and-association-members') {
    return currentUrl === itemHref || currentUrl.startsWith(itemHref + '/');
  }

  // Handle community and community-clusters with better nested route detection
  if (itemHref === '/community') {
    return currentUrl === itemHref || currentUrl.startsWith(itemHref + '/');
  }

  if (itemHref === '/community-clusters') {
    // This should match: /community-clusters, /community-clusters/create, /community-clusters/123/edit, etc.
    return currentUrl === itemHref || currentUrl.startsWith(itemHref + '/');
  }

  // Handle different URL patterns for the same page
  const urlVariations = {
    '/member/index': ['/member/index', '/member'],
    '/parish': ['/parish/index', '/parish'],
    '/zone': ['/zone/index', '/zone'],
    '/clusters': ['/clusters/index', '/clusters'],
    '/scc-head': ['/scc-head/index', '/scc-head'],
    '/ppc-head': ['/ppc-head/index', '/ppc-head'],
    '/relationship': ['/relationship/index', '/relationship'],
    '/designation': ['/designation/index', '/designation'],
    '/age-group': ['/age-group/index', '/age-group'],
    '/blood-group': ['/blood-group/index', '/blood-group'],
    '/gender': ['/gender/index', '/gender'],
    '/income-range': ['/income-range/index', '/income-range'],
    '/status': ['/status/index', '/status'],
    '/country': ['/country/index', '/country'],
    '/state': ['/state/index', '/state'],
    '/city': ['/city/index', '/city'],
    '/town': ['/town/index', '/town'],
    '/users': ['/users/index', '/users'],
    '/roles-permissions': ['/roles-permissions/index', '/roles-permissions'],
  };

  // Check if the current URL matches any variation of the item href
  const variations = urlVariations[itemHref as keyof typeof urlVariations];
  if (variations) {
    // Check if current URL starts with any of the variations
    const isActive = variations.some((variation) => currentUrl.startsWith(variation));
    return isActive;
  }

  // For other pages, check if current URL starts with the item href
  const isActive = currentUrl.startsWith(itemHref);
  return isActive;
};

const handleNavigation = (href: string) => {
  // Save the current sidebar scroll position before navigation
  saveScrollPosition();

  // Navigate to the new page
  router.visit(href, {
    preserveScroll: false,
    preserveState: false,
    onSuccess: () => {
      // Only execute if component is still mounted
      if (!isComponentMounted.value) return;

      // Use nextTick and additional delay to ensure DOM is fully updated
      nextTick(() => {
        if (!isComponentMounted.value) return;

        // Wait a bit more for the DOM to stabilize
        setTimeout(() => {
          if (!isComponentMounted.value) return;
          restoreScrollPosition();
        }, 150);
      });
    },
  });
};
</script>

<template>
  <div class="space-y-6">
    <SidebarGroup v-for="group in groups" :key="group.label" class="px-2 py-0">
      <SidebarGroupLabel
        class="mb-3 rounded-md border-l-2 border-blue-500 bg-blue-50 px-2 py-1 text-xs font-bold tracking-wider text-blue-600 uppercase dark:bg-blue-900/20 dark:text-blue-400"
      >
        {{ group.label }}
      </SidebarGroupLabel>
      <SidebarMenu class="space-y-1">
        <SidebarMenuItem v-for="item in group.items" :key="item.title">
          <SidebarMenuButton
            :is-active="isActivePage(item.href, getCurrentPath())"
            :tooltip="item.title"
            class="gap-1"
            @click="handleNavigation(item.href)"
          >
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarGroup>
  </div>
</template>
