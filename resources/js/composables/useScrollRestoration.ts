import { onMounted, onBeforeUnmount } from 'vue'

export function useScrollRestoration() {
  // Disabled scroll restoration to prevent auto-scroll behavior
  const saveScrollPosition = () => {
    // Do nothing - scroll restoration disabled
  }

  const restoreScrollPosition = () => {
    // Do nothing - scroll restoration disabled
  }

  onMounted(() => {
    // Do nothing - scroll restoration disabled
  })

  onBeforeUnmount(() => {
    // Do nothing - scroll restoration disabled
  })

  return {
    saveScrollPosition,
    restoreScrollPosition
  }
} 