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
      resolve: async (name) => {
          // List of pages that are in PagesMembers directory
          const memberPages = [
              'Dashboard', 
              'member', 
              'community', 
              'ExternalMembers',
              'zones',
              'town',
              'status',
              'state',
              's_c_c_head',
              'parish',
              'relationship',
              'p_p_c_head',
              'income_range',
              'designation',
              'gender',
              'country',
              'community_clusters',
              'city',
              'clusters',
              'cells-and-association',
              'cells-and-association-members',
              'blood_group',
              'age_group',
              'users'
          ]; // All member module components
          
          // List of pages that are in PagesFund directory
          const fundPages = [
              'Dashboard',
              'AnnualContributions',
              'MassIntentions'
          ]; // All fund module components
          
          try {
              // First, try to resolve based on explicit fund pages (higher priority for Fund app)
              if (fundPages.some(page => name.startsWith(page))) {
                  // Check if the name contains a slash (nested structure like Dashboard/Index)
                  if (name.includes('/')) {
                      return await resolvePageComponent(`./PagesFund/${name}.vue`, import.meta.glob<DefineComponent>('./PagesFund/**/*.vue'));
                  } else {
                      return await resolvePageComponent(`./PagesFund/${name}.vue`, import.meta.glob<DefineComponent>('./PagesFund/**/*.vue'));
                  }
              }
              
              // Then, try to resolve based on explicit member pages
              if (memberPages.some(page => name.startsWith(page))) {
                  return await resolvePageComponent(`./PagesMembers/${name}.vue`, import.meta.glob<DefineComponent>('./PagesMembers/**/*.vue'));
              }
              
              // If not explicitly defined, try PagesFund first (for Fund app components)
              try {
                  return await resolvePageComponent(`./PagesFund/${name}.vue`, import.meta.glob<DefineComponent>('./PagesFund/**/*.vue'));
              } catch (fundError) {
                  // If not found in PagesFund, try pages directory
                  return await resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue'));
              }
          } catch (error) {
              // Fallback: try the other directories
              try {
                  // Try pages directory first
                  return await resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue'));
              } catch (fallbackError) {
                  try {
                      // Try PagesMembers as last resort
                      return await resolvePageComponent(`./PagesMembers/${name}.vue`, import.meta.glob<DefineComponent>('./PagesMembers/**/*.vue'));
                  } catch (finalError) {
                      throw new Error(`Page not found: ${name}`);
                  }
              }
          }
      },
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
