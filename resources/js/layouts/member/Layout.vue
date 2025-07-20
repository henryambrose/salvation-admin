<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
  {
    title: 'Profile Details',
    href: '/member/profile-details/create',
  },
  {
    title: 'Contact Details',
    href: '/member/contact-details',
  },
  {
    title: 'Education Details',
    href: '/member/education-details',
  },
  {
    title: 'Address Details',
    href: '/member/address-details',
  },
  {
    title: 'Family Details',
    href: '/member/family-details',
  },
  {
    title: 'Community & Other Details',
    href: '/member/community-details',
  },
];

const page = usePage();
console.log('page.props.ziggy', page.props.ziggy);
const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
  <div class="px-4 py-6">
    <Heading title="Members" description="Manage your member profile and it's other details" />

    <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-y-0 lg:space-x-12">
      <aside class="w-full max-w-xl lg:w-48">
        <nav class="flex flex-col space-y-1 space-x-0">
          <Button
            v-for="item in sidebarNavItems"
            :key="item.href"
            variant="ghost"
            :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
            as-child
          >
            <Link :href="item.href">
              {{ item.title }}
            </Link>
          </Button>
        </nav>
      </aside>

      <Separator class="my-6 md:hidden" />

      <div class="flex-1 md:max-w-2xl">
        <section class="max-w-xl space-y-12">
          <slot />
        </section>
      </div>
    </div>
  </div>
</template>
