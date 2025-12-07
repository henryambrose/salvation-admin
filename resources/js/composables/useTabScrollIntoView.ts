import { onMounted, onBeforeUnmount, ref } from 'vue';

/**
 * Options for configuring tab scroll behavior
 */
interface TabScrollOptions {
  /**
   * Whether the auto-scroll is enabled
   * @default true
   */
  enabled?: boolean;

  /**
   * Defines the vertical alignment of the element within the viewport
   * @default 'center'
   */
  block?: ScrollLogicalPosition;

  /**
   * Defines the scroll animation behavior
   * @default 'smooth'
   */
  behavior?: ScrollBehavior;

  /**
   * Additional offset to account for sticky headers/footers (in pixels)
   * @default 80
   */
  offset?: number;

  /**
   * CSS selectors for elements that should be excluded from auto-scroll
   * @default []
   */
  excludeSelectors?: string[];

  /**
   * Debounce delay in milliseconds for rapid tab navigation
   * @default 50
   */
  debounceDelay?: number;
}

/**
 * Default options for tab scroll behavior
 */
const DEFAULT_OPTIONS: Required<TabScrollOptions> = {
  enabled: true,
  block: 'center',
  behavior: 'smooth',
  offset: 80,
  excludeSelectors: [],
  debounceDelay: 50,
};

/**
 * Composable that implements auto-scroll behavior when navigating forms with Tab key
 *
 * Features:
 * - Detects Tab/Shift+Tab keyboard navigation
 * - Automatically scrolls focused elements into view
 * - Distinguishes between keyboard and mouse focus
 * - Respects sticky headers/footers with offset calculation
 * - Excludes specified elements (dropdowns, modals, etc.)
 * - Debounces rapid tabbing to prevent scroll jank
 *
 * @param options - Configuration options for scroll behavior
 *
 * @example
 * ```typescript
 * // In AppLayout.vue or any component
 * import { useTabScrollIntoView } from '@/composables/useTabScrollIntoView';
 *
 * useTabScrollIntoView({
 *   block: 'center',
 *   behavior: 'smooth',
 *   offset: 80,
 *   excludeSelectors: ['[data-no-autoscroll]', '[role="listbox"]'],
 * });
 * ```
 */
export function useTabScrollIntoView(options: TabScrollOptions = {}) {
  // Merge provided options with defaults
  const config = { ...DEFAULT_OPTIONS, ...options };

  // Track if the last interaction was via keyboard (Tab key)
  const isKeyboardNavigation = ref(false);

  // Debounce timer reference
  let debounceTimer: number | null = null;

  /**
   * Check if an element should be excluded from auto-scroll
   */
  const shouldExcludeElement = (element: HTMLElement): boolean => {
    if (!config.excludeSelectors.length) return false;

    return config.excludeSelectors.some((selector) => {
      try {
        return element.matches(selector) || element.closest(selector) !== null;
      } catch (error) {
        console.warn(`Invalid selector in excludeSelectors: ${selector}`);
        return false;
      }
    });
  };

  /**
   * Check if an element is fully visible in the viewport
   */
  const isElementInViewport = (element: HTMLElement): boolean => {
    const rect = element.getBoundingClientRect();
    const offset = config.offset;

    return (
      rect.top >= offset &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) - offset &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  };

  /**
   * Scroll an element into view with configured options
   */
  const scrollElementIntoView = (element: HTMLElement): void => {
    try {
      element.scrollIntoView({
        behavior: config.behavior,
        block: config.block,
        inline: 'nearest',
      });
    } catch (error) {
      // Fallback for browsers that don't support scrollIntoView options
      element.scrollIntoView();
    }
  };

  /**
   * Handle keydown events to detect Tab navigation
   */
  const handleKeyDown = (event: KeyboardEvent): void => {
    if (event.key === 'Tab') {
      isKeyboardNavigation.value = true;
    }
  };

  /**
   * Handle mousedown events to reset keyboard navigation flag
   */
  const handleMouseDown = (): void => {
    isKeyboardNavigation.value = false;
  };

  /**
   * Handle focus events and trigger scroll if needed
   */
  const handleFocus = (event: FocusEvent): void => {
    if (!config.enabled || !isKeyboardNavigation.value) {
      return;
    }

    const target = event.target as HTMLElement;

    // Skip if element should be excluded
    if (shouldExcludeElement(target)) {
      return;
    }

    // Clear existing debounce timer
    if (debounceTimer !== null) {
      clearTimeout(debounceTimer);
    }

    // Debounce the scroll action to handle rapid tabbing
    debounceTimer = window.setTimeout(() => {
      // Check if element is already visible in viewport
      if (!isElementInViewport(target)) {
        scrollElementIntoView(target);
      }

      // Reset the keyboard navigation flag after a delay
      setTimeout(() => {
        isKeyboardNavigation.value = false;
      }, 100);

      debounceTimer = null;
    }, config.debounceDelay);
  };

  /**
   * Setup event listeners
   */
  onMounted(() => {
    // Use capture phase to catch events early
    document.addEventListener('keydown', handleKeyDown, true);
    document.addEventListener('mousedown', handleMouseDown, true);
    document.addEventListener('focusin', handleFocus, true);
  });

  /**
   * Clean up event listeners
   */
  onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleKeyDown, true);
    document.removeEventListener('mousedown', handleMouseDown, true);
    document.removeEventListener('focusin', handleFocus, true);

    // Clear any pending debounce timer
    if (debounceTimer !== null) {
      clearTimeout(debounceTimer);
    }
  });

  /**
   * Return control functions for advanced usage
   */
  return {
    /**
     * Check if keyboard navigation is currently active
     */
    isKeyboardNavigation: isKeyboardNavigation,
  };
}
