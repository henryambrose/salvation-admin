import { onBeforeUnmount, onMounted } from 'vue';

export function useScrollRestoration() {
  const STORAGE_KEY = 'sidebar-scroll-position';

  const saveScrollPosition = () => {
    const sidebarContent = document.querySelector('[data-slot="sidebar-content"]');
    if (sidebarContent) {
      const scrollPosition = sidebarContent.scrollTop;
      sessionStorage.setItem(STORAGE_KEY, scrollPosition.toString());
    }
  };

  const restoreScrollPosition = () => {
    const savedPosition = sessionStorage.getItem(STORAGE_KEY);
    if (savedPosition) {
      const sidebarContent = document.querySelector('[data-slot="sidebar-content"]');
      if (sidebarContent) {
        sidebarContent.scrollTop = parseInt(savedPosition, 10);
      }
    }
  };

  const clearScrollPosition = () => {
    sessionStorage.removeItem(STORAGE_KEY);
  };

  // Auto-save scroll position when navigating away
  onBeforeUnmount(() => {
    saveScrollPosition();
  });

  // Auto-restore scroll position when component mounts
  onMounted(() => {
    // Use a small delay to ensure DOM is fully rendered
    setTimeout(() => {
      restoreScrollPosition();
    }, 100);
  });

  return {
    saveScrollPosition,
    restoreScrollPosition,
    clearScrollPosition
  };
}
