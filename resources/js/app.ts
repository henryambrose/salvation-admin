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

async function bootstrap() {
    try {
      // prefetch CSRF cookie; safe to call again before auth-sensitive requests
      await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
    } catch (e) {
      console.debug('CSRF prefetch skipped:', e);
    }


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
}
bootstrap();
