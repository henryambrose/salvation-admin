import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import axios from 'axios';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

// Axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
axios.defaults.withCredentials = true;

async function bootstrap() {
  try {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
  } catch (e) {
    console.debug('CSRF prefetch skipped:', e);
  }

  const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

  // Preload all possible page locations (Linux is case-sensitive!)
  const registry = {
    ...import.meta.glob('./Pages/**/*.vue'),
    ...import.meta.glob('./PagesMembers/**/*.vue'),
    ...import.meta.glob('./PagesFund/**/*.vue'),
    ...import.meta.glob('./PagesGraveyard/**/*.vue'),
    ...import.meta.glob('./pages/**/*.vue'), // optional lowercase fallback
  } as Record<string, () => Promise<{ default: DefineComponent }>>;

  createInertiaApp({
    title: (title) => `${title} - ${appName}`,

    // IMPORTANT: return the *component*, not the module
    resolve: async (name) => {
      const candidates: string[] = [];

      // Handle special prefixes first
      if (name.startsWith('Fund/')) {
        const p = name.slice(5);
        candidates.push(`./PagesFund/${p}.vue`);
      }
      if (name.startsWith('PagesGraveyard/')) {
        const p = name.replace(/^PagesGraveyard\//, '');
        candidates.push(`./PagesGraveyard/${p}.vue`);
      }

      // Common locations in priority order
      candidates.push(
        `./Pages/${name}.vue`,
        `./PagesMembers/${name}.vue`,
        `./PagesFund/${name}.vue`,
        `./PagesGraveyard/${name}.vue`,
        `./pages/${name}.vue`,
      );

      for (const key of candidates) {
        const importer = registry[key];
        if (importer) {
          const mod = await importer(); // { default: DefineComponent }
          return mod.default; // ✅ DefineComponent
        }
      }

      throw new Error(`Page not found: ${name}`);
    },

    setup({ el, App, props, plugin }) {
      createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue)
        .mount(el);
    },

    progress: { color: '#4B5563' },
  });

  initializeTheme();

  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }
}

bootstrap();
