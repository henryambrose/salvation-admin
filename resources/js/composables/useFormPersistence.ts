import { ref, watch, onMounted, type Ref } from 'vue';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';

export function useFormPersistence<T extends object>(
  formData: Ref<T>,
  storageKey: string,
  options: {
    enabled?: boolean;
    debounceMs?: number;
    onRestore?: (data: T) => void;
    excludeKeys?: string[];
  } = {}
) {
  const {
    enabled = true,
    debounceMs = 1000,
    excludeKeys = [],
  } = options;

  const { info } = useToast();
  const hasRestoredData = ref(false);
  const lastSavedAt = ref<Date | null>(null);
  let saveTimeout: NodeJS.Timeout | null = null;

  /**
   * Save form data to localStorage
   */
  function save() {
    if (!enabled) return;

    try {
      const dataToSave = { ...formData.value };

      // Remove excluded keys
      excludeKeys.forEach(key => {
        delete (dataToSave as any)[key];
      });

      const savedData = {
        data: dataToSave,
        timestamp: new Date().toISOString(),
        version: '1.0', // For future compatibility
      };

      localStorage.setItem(storageKey, JSON.stringify(savedData));
      lastSavedAt.value = new Date();
    } catch (error) {
      console.error('Failed to save form data to localStorage:', error);
    }
  }

  /**
   * Restore form data from localStorage
   */
  function restore(): T | null {
    if (!enabled) return null;

    try {
      const savedItem = localStorage.getItem(storageKey);
      if (!savedItem) return null;

      const parsed = JSON.parse(savedItem);

      // Check if data is not too old (e.g., older than 24 hours)
      const savedAt = new Date(parsed.timestamp);
      const now = new Date();
      const hoursSinceSave = (now.getTime() - savedAt.getTime()) / (1000 * 60 * 60);

      if (hoursSinceSave > 24) {
        // Data is too old, remove it
        clear();
        return null;
      }

      hasRestoredData.value = true;
      return parsed.data as T;
    } catch (error) {
      console.error('Failed to restore form data from localStorage:', error);
      return null;
    }
  }

  /**
   * Clear saved form data from localStorage
   */
  function clear() {
    if (!enabled) return;

    try {
      localStorage.removeItem(storageKey);
      hasRestoredData.value = false;
      lastSavedAt.value = null;
    } catch (error) {
      console.error('Failed to clear form data from localStorage:', error);
    }
  }

  /**
   * Check if there's saved data available
   */
  function hasSavedData(): boolean {
    if (!enabled) return false;

    try {
      const savedItem = localStorage.getItem(storageKey);
      return savedItem !== null;
    } catch {
      return false;
    }
  }

  /**
   * Get info about saved data
   */
  function getSavedDataInfo(): { timestamp: Date; age: string } | null {
    if (!enabled) return null;

    try {
      const savedItem = localStorage.getItem(storageKey);
      if (!savedItem) return null;

      const parsed = JSON.parse(savedItem);
      const savedAt = new Date(parsed.timestamp);
      const now = new Date();
      const minutesAgo = Math.floor((now.getTime() - savedAt.getTime()) / (1000 * 60));

      let age: string;
      if (minutesAgo < 1) {
        age = 'just now';
      } else if (minutesAgo < 60) {
        age = `${minutesAgo} minute${minutesAgo !== 1 ? 's' : ''} ago`;
      } else {
        const hoursAgo = Math.floor(minutesAgo / 60);
        age = `${hoursAgo} hour${hoursAgo !== 1 ? 's' : ''} ago`;
      }

      return { timestamp: savedAt, age };
    } catch {
      return null;
    }
  }

  // Watch form data and auto-save with debounce
  if (enabled) {
    watch(
      formData,
      () => {
        if (saveTimeout) {
          clearTimeout(saveTimeout);
        }

        saveTimeout = setTimeout(() => {
          save();
        }, debounceMs);
      },
      { deep: true }
    );
  }

  // On mount, check for saved data
  onMounted(() => {
    if (!enabled) return;

    const savedInfo = getSavedDataInfo();
    if (savedInfo) {
      const { confirm: showConfirm } = useConfirm();
      showConfirm({
        title: 'Restore Unsaved Work',
        message: `Found unsaved work from ${savedInfo.age}. Would you like to restore it?`,
        confirmText: 'Restore',
        cancelText: 'Discard',
        type: 'info',
        onConfirm: () => {
          const restoredData = restore();
          if (restoredData) {
            // Merge restored data with current form data
            Object.assign(formData.value, restoredData);
            info(`Form data restored from ${savedInfo.age}`);
            options.onRestore?.(restoredData);
          }
        },
        onCancel: () => {
          clear();
        },
      });
    }
  });

  return {
    save,
    restore,
    clear,
    hasSavedData,
    getSavedDataInfo,
    hasRestoredData,
    lastSavedAt,
  };
}
