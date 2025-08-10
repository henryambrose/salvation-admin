// Create a minimal composable to fix the build error
export function useScrollRestoration() {
  // Return empty functions since this is not being used
  return {
    saveScrollPosition: () => {},
    restoreScrollPosition: () => {},
    clearScrollPosition: () => {}
  };
}
