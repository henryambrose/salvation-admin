import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import axios from 'axios';

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
axios.defaults.withCredentials = true;

// Function to refresh CSRF token
const refreshCsrfToken = async () => {
  try {
    await axios.get('/csrf-cookie', { withCredentials: true });
    const newToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (newToken) {
      axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
    }
    return true;
  } catch (error) {
    console.error('Failed to refresh CSRF token:', error);
    return false;
  }
};

// Enhanced CSRF error handling
axios.interceptors.response.use(
  response => response,
  async (error) => {
    if (error.response?.status === 419) {
      console.log('CSRF token mismatch detected, attempting to refresh...');
      
      // Try to refresh the CSRF token
      const refreshed = await refreshCsrfToken();
      
      if (refreshed) {
        // Retry the original request with new token
        console.log('CSRF token refreshed, retrying request...');
        const newToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (newToken && error.config) {
          error.config.headers['X-CSRF-TOKEN'] = newToken;
          return axios.request(error.config);
        }
      } else {
        // If refresh fails, redirect to login or reload page
        console.log('CSRF token refresh failed, redirecting to login...');
        window.location.href = '/login';
        return Promise.reject(error);
      }
    }
    return Promise.reject(error);
  }
);

// Initialize CSRF token on page load
const initializeCsrfToken = () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
  }
};

// Call initialization
initializeCsrfToken();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Initialize theme
initializeTheme();

// Disable browser scroll restoration
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}
