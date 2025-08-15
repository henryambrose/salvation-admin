import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import axios from 'axios';


axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
axios.defaults.withCredentials = true;

// Add CSRF token to all requests
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

// optional one-time auto-heal on 419
axios.interceptors.response.use(r => r, async (err) => {
  if (err.response?.status === 419 && !err.config.__retried) {
    err.config.__retried = true;
    try { await axios.get('/sanctum/csrf-cookie').catch(() => {}); } catch {}
    return axios(err.config);
  }
  return Promise.reject(err);
});


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

// This will set light / dark mode on page load...
initializeTheme();

// Disable browser scroll restoration to prevent auto-scroll to top
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}
