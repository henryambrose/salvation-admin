<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { User, Lock, Palette } from 'lucide-vue-next';

const sidebarNavItems: NavItem[] = [
  {
    title: 'Profile',
    href: '/settings/profile',
    icon: User,
  },
  {
    title: 'Password',
    href: '/settings/password',
    icon: Lock,
  },
  {
    title: 'Appearance',
    href: '/settings/appearance',
    icon: Palette,
  },
];

const page = usePage();

const currentPath = (page.props.ziggy as any)?.location ? new URL((page.props.ziggy as any).location).pathname : '';
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="container mx-auto px-4 py-8">
      <!-- Header Section -->
      <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
          <User class="h-8 w-8 text-blue-600 dark:text-blue-400" />
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Account Settings</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Manage your profile and account preferences</p>
      </div>

      <div class="mx-auto max-w-6xl">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
          <!-- Sidebar Navigation -->
          <div class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
              <nav class="space-y-2">
                <Link
                  v-for="item in sidebarNavItems"
                  :key="item.href"
                  :href="item.href"
                  :class="[
                    'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200',
                    currentPath === item.href
                      ? 'bg-blue-100 text-blue-700 shadow-sm dark:bg-blue-900 dark:text-blue-300'
                      : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white'
                  ]"
                >
                  <component :is="item.icon" class="h-5 w-5" />
                  {{ item.title }}
                </Link>
              </nav>
            </div>
          </div>

          <!-- Main Content Area -->
          <div class="lg:col-span-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
              <slot />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
