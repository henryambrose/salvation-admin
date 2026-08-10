import { onMounted, onUnmounted, ref } from 'vue';

export function useSessionKeepAlive(
  options: {
    enabled?: boolean;
    intervalMinutes?: number;
    warningMinutes?: number;
    onWarning?: () => void;
    onExpired?: () => void;
  } = {},
) {
  const {
    enabled = true,
    intervalMinutes = 2, // Ping every 2 minutes
    warningMinutes = 1, // Warn 1 minute before expiry
  } = options;

  const isActive = ref(enabled);
  const lastActivity = ref(Date.now());
  const keepAliveInterval = ref<NodeJS.Timeout | null>(null);
  const warningShown = ref(false);

  // Get session lifetime from config (default 480 minutes = 8 hours)
  const sessionLifetimeMinutes = 480; // You can make this dynamic

  /**
   * Ping the server to keep session alive
   */
  async function pingServer() {
    if (!isActive.value) return;

    try {
      await fetch('/ping', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        credentials: 'same-origin',
      });

      lastActivity.value = Date.now();
      warningShown.value = false;
    } catch (error) {
      console.error('Session keep-alive ping failed:', error);
    }
  }

  /**
   * Check if session is about to expire and show warning
   */
  function checkSessionExpiry() {
    if (!isActive.value) return;

    const now = Date.now();
    const timeSinceLastActivity = (now - lastActivity.value) / 1000 / 60; // in minutes
    const timeUntilExpiry = sessionLifetimeMinutes - timeSinceLastActivity;

    // Show warning if close to expiry
    if (timeUntilExpiry <= warningMinutes && !warningShown.value) {
      warningShown.value = true;
      options.onWarning?.();
    }

    // Session expired
    if (timeUntilExpiry <= 0) {
      options.onExpired?.();
    }
  }

  /**
   * Start the keep-alive mechanism
   */
  function start() {
    if (!enabled || keepAliveInterval.value) return;

    // Initial ping
    pingServer();

    // Set up interval to ping server
    keepAliveInterval.value = setInterval(
      () => {
        pingServer();
        checkSessionExpiry();
      },
      intervalMinutes * 60 * 1000,
    ); // Convert minutes to milliseconds
  }

  /**
   * Stop the keep-alive mechanism
   */
  function stop() {
    if (keepAliveInterval.value) {
      clearInterval(keepAliveInterval.value);
      keepAliveInterval.value = null;
    }
    isActive.value = false;
  }

  /**
   * Manually trigger a ping
   */
  function refresh() {
    pingServer();
  }

  // Track user activity to reset the timer
  function trackActivity() {
    lastActivity.value = Date.now();
    warningShown.value = false;
  }

  // Set up activity listeners
  onMounted(() => {
    if (enabled) {
      // Track user activity
      const events = ['mousedown', 'keydown', 'scroll', 'touchstart'];
      events.forEach((event) => {
        window.addEventListener(event, trackActivity, { passive: true });
      });

      start();
    }
  });

  onUnmounted(() => {
    stop();

    // Clean up activity listeners
    const events = ['mousedown', 'keydown', 'scroll', 'touchstart'];
    events.forEach((event) => {
      window.removeEventListener(event, trackActivity);
    });
  });

  return {
    isActive,
    lastActivity,
    start,
    stop,
    refresh,
    pingServer,
  };
}
