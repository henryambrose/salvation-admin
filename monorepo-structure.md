# Monorepo Structure: Members (Root), Fund, Graveyard — Single Laravel App

This document describes a clean, scalable structure for **one Laravel app** that serves three “sub‑apps”:
- **Members** (root/default app)
- **Fund**
- **Graveyard**

It separates **domain code per app** (controllers/models/services/migrations/pages) while sharing a **single** Laravel core (auth, config, public, Vite/Tailwind/PostCSS). The UI **layout is shared** (top bar + left nav), while **content/pages** differ per app.

---

## 1) Top‑Level Overview

```
laravel-root/
├─ app/
│  ├─ Http/…                         # global HTTP kernel, middleware, etc.
│  ├─ Models/…                       # global/shared models (if any)
│  └─ Modules/                       # <— per-app (domain) PHP code
│     ├─ Members/                    # Optional: keep members domain here or in app/Http/Controllers etc.
│     │  ├─ Console/Commands/
│     │  ├─ Http/{Controllers,Middleware,Requests,Resources}/
│     │  ├─ Models/ Policies/ Rules/ Services/ Helpers/ Facades/
│     │  └─ Providers/ModuleServiceProvider.php
│     ├─ Fund/
│     │  ├─ Console/Commands/
│     │  ├─ Http/{Controllers,Middleware,Requests,Resources}/
│     │  ├─ Models/ Policies/ Rules/ Services/ Helpers/ Facades/
│     │  └─ Providers/ModuleServiceProvider.php
│     └─ Grave/
│        ├─ Console/Commands/
│        ├─ Http/{Controllers,Middleware,Requests,Resources}/
│        ├─ Models/ Policies/ Rules/ Services/ Helpers/ Facades/
│        └─ Providers/ModuleServiceProvider.php
│
├─ bootstrap/                        # global
├─ config/                           # global
├─ database/
│  ├─ migrations/                    # global
│  ├─ seeders/                       # global
│  └─ modules/                       # <— per-app DB artifacts
│     ├─ fund/{migrations,seeders,factories}
│     └─ grave/{migrations,seeders,factories}
│
├─ public/                           # global
├─ resources/
│  ├─ css/
│  │  └─ app.css                     # Tailwind entry shared by all apps
│  ├─ js/
│  │  ├─ app.ts                      # Single Inertia+Vue boot for all apps
│  │  ├─ layouts/AppShell.vue        # Shared top bar + sidebar layout
│  │  ├─ components/                 # Shared UI
│  │  ├─ composables/                # Shared composables
│  │  ├─ PagesMembers/               # Members pages (content differs)
│  │  ├─ PagesFund/                  # Fund pages
│  │  └─ PagesGrave/                 # Graveyard pages
│  └─ views/
│     └─ app.blade.php               # Single root Blade for all apps
│
├─ routes/
│  ├─ web.php                        # Members/default routes
│  ├─ fund.php                       # Fund routes
│  └─ grave.php                      # Graveyard routes
│
├─ postcss.config.js                 # JS (not TS) to avoid ts-node dependency
├─ tailwind.config.js                # Shared Tailwind scanning all pages
├─ vite.config.ts                    # Single entry for shared layout
├─ composer.json
└─ package.json
```

> **Global stays global**: `bootstrap/`, `config/`, `public/`, **single** `vite.config.ts`, **single** `postcss.config.js`, **single** `tailwind.config.js`.  
> **Per‑app**: code under `app/Modules/<App>`, database under `database/modules/<app>`, pages under `resources/js/Pages<App>`.

---

## 2) Common vs. Per‑App

| Area | Common (Shared) | Per‑App (Isolated) |
|---|---|---|
| Auth / Session / Guards | ✅ |  |
| Config (`config/*`) | ✅ |  |
| Public assets (`public/*`) | ✅ |  |
| Vite / Tailwind / PostCSS | ✅ single set |  |
| Vue Layout (AppShell) | ✅ one layout |  |
| Controllers / Models / Services |  | ✅ under `app/Modules/<App>` |
| Requests / Policies / Rules |  | ✅ under `app/Modules/<App>` |
| DB Migrations/Seeders |  | ✅ under `database/modules/<app>` |
| Vue Pages |  | ✅ under `resources/js/Pages<App>` |
| Routes |  | ✅ `routes/<app>.php` |

---

## 3) Composer Autoload (PSR‑4)

```jsonc
// composer.json
{
  "autoload": {
    "psr-4": {
      "App\": "app/",
      "Modules\Members\": "app/Modules/Members/",
      "Modules\Fund\": "app/Modules/Fund/",
      "Modules\Grave\": "app/Modules/Grave/"
    }
  }
}
```
```bash
composer dump-autoload
```

---

## 4) Module Service Providers

**Example:** `app/Modules/Fund/Providers/ModuleServiceProvider.php`
```php
<?php

namespace Modules\Fund\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bindings/singletons here if needed
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/fund.php'));
        $this->loadMigrationsFrom(database_path('modules/fund/migrations'));
        // $this->loadViewsFrom(resource_path('modules/fund/views'), 'fund'); // optional
    }
}
```
Register providers in `config/app.php`:
```php
'providers' => [
    // ...
    Modules\Fund\Providers\ModuleServiceProvider::class,
    Modules\Grave\Providers\ModuleServiceProvider::class,
    // Modules\Members\Providers\ModuleServiceProvider::class, // optional
],
```

---

## 5) Routes & Inertia Page Conventions

- **Members** render: `Inertia::render('members/<Page>')` → `resources/js/PagesMembers/<Page>.vue`
- **Fund** render: `Inertia::render('fund/<Page>')` → `resources/js/PagesFund/<Page>.vue`
- **Grave** render: `Inertia::render('grave/<Page>')` → `resources/js/PagesGrave/<Page>.vue`

**routes/fund.php**
```php
<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['web','auth','can:access-fund'])
  ->prefix('fund')
  ->group(function () {
      Route::get('/', fn () => Inertia::render('fund/Dashboard'))->name('fund.dashboard');
  });
```

**routes/grave.php** is analogous.

In `app/Providers/RouteServiceProvider.php`:
```php
public function boot(): void
{
    $this->routes(function () {
        require base_path('routes/web.php');
        require base_path('routes/fund.php');
        require base_path('routes/grave.php');
    });
}
```

---

## 6) Single Vite Entry (Shared Layout)

**vite.config.ts**
```ts
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.ts'],
      refresh: true,
    }),
    vue(),
  ],
  css: { postcss: './postcss.config.js' },
});
```

**postcss.config.js**
```js
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};
```

**tailwind.config.js**
```js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.vue",
    "./resources/**/*.js",
    "./resources/**/*.ts",
  ],
  theme: { extend: {} },
  plugins: [],
};
```

**resources/views/app.blade.php** (single root view)
```blade
<!doctype html>
<html>
  <head>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
  </head>
  <body class="font-sans antialiased">
    @inertia
  </body>
</html>
```

---

## 7) Vue Boot (One App, Three Namespaces)

**resources/js/app.ts**
```ts
import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

// Glob all namespaces
const membersPages = import.meta.glob('./PagesMembers/**/*.vue');
const fundPages    = import.meta.glob('./PagesFund/**/*.vue');
const gravePages   = import.meta.glob('./PagesGrave/**/*.vue');

const map = { members: membersPages, fund: fundPages, grave: gravePages } as const;

createInertiaApp({
  resolve: (name: string) => {
    const [ns, ...rest] = name.split('/');
    const page = rest.join('/');
    const dict = (map as any)[ns];
    if (!dict) throw new Error(`Unknown namespace: ${ns}`);
    const key = `./Pages${ns.charAt(0).toUpperCase()}${ns.slice(1)}/${page}.vue`;
    if (!dict[key]) throw new Error(`Page not found: ${key}`);
    return dict[key]();
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
  progress: { color: '#4B5563' },
});
```

**Shared Layout** (wrap all pages):
`resources/js/layouts/AppShell.vue`
```vue
<script setup lang="ts">
import Topbar from '../components/Topbar.vue';
import Sidebar from '../components/Sidebar.vue';
</script>

<template>
  <div class="min-h-screen flex">
    <Sidebar />
    <div class="flex-1 flex flex-col">
      <Topbar />
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
```

**Page Example:** `resources/js/PagesFund/Dashboard.vue`
```vue
<script setup lang="ts">
import AppShell from '../layouts/AppShell.vue';
</script>

<template>
  <AppShell>
    <h1 class="text-xl font-semibold">Fund — Dashboard</h1>
  </AppShell>
</template>
```

---

## 8) Permissions & App Switcher

- Protect route groups with permissions: `can:access-fund`, `can:access-grave`.
- App switcher in Topbar can navigate to `/`, `/fund`, `/grave` based on permissions provided via `usePage().props.auth`.

---

## 9) Optional: Separate Build Entries

If you ever need per-app CSS/JS bundles (e.g., drastically different themes), switch to multiple inputs:

```ts
laravel({
  input: {
    members: ['resources/css/app.css',  'resources/js/app.ts'],
    fund:    ['resources/css/fund.css', 'resources/js/fund/main.ts'],
    grave:   ['resources/css/grave.css','resources/js/grave/main.ts'],
  },
  buildDirectory: 'build',
  refresh: true,
});
```

And include per-app bundles in per-app Blade roots. For **same layout**, the single-entry model above is simpler.

---

## 10) Hand‑Off Checklist

- [ ] `composer.json` PSR‑4 for `Modules\Fund\` and `Modules\Grave\` (and `Modules\Members\` if used)
- [ ] `database/modules/{fund,grave}/migrations` and seeders
- [ ] `routes/{fund,grave}.php` with `Inertia::render('fund/*')` and `('grave/*')`
- [ ] `resources/js/Pages{Fund,Grave}/*` pages using `AppShell`
- [ ] `postcss.config.js` (JS), `tailwind.config.js`, `vite.config.ts` present and valid
- [ ] `resources/css/app.css` contains Tailwind directives
- [ ] Remove any `postcss.config.ts` to avoid ts-node requirement

---

## 11) Quick Start

```bash
npm i
composer install
npm run dev
php artisan serve

# Visit
/         -> Members
/fund     -> Fund
/grave    -> Graveyard
```
