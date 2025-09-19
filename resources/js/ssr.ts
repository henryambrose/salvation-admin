import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, DefineComponent, h } from 'vue';
import { route as ziggyRoute } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
interface ZiggyConfig {
  location?: string;
  [key: string]: any; // allow other props from Ziggy
}
createServer((page) =>
  createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
      resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue', { eager: false })) as Promise<DefineComponent>,
    setup({ App, props, plugin }) {
      const app = createSSRApp({ render: () => h(App, props) });

      // Configure Ziggy for SSR...
      const ziggyConfig = Object.assign({}, (page.props.ziggy as ZiggyConfig) || {}, {
        location: new URL((page.props.ziggy as ZiggyConfig)?.location || 'http://localhost'),
      });

      // Create route function...
      const route = (name?: any, params?: any, absolute?: boolean) => {
        if (name === undefined) {
          return ziggyRoute as any;
        }
        return (ziggyRoute as any)(name, params, absolute, ziggyConfig);
      };

      // Make route function available globally...
      app.config.globalProperties.route = route as any;

      // Make route function available globally for SSR...
      if (typeof window === 'undefined') {
        (globalThis as any).route = route;
      }

      app.use(plugin);

      return app;
    },
  }),
);
