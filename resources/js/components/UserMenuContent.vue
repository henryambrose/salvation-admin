<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings, Users, Shield } from 'lucide-vue-next';
import { permissionHelpers } from '@/composables/permissionHelpers';

interface Props {
  user: User;
}

const { can } = permissionHelpers();

const handleLogout = () => {
  // Simple redirect to logout URL
  window.location.href = '/logout';
};

defineProps<Props>();
</script>

<template>
  <DropdownMenuLabel class="p-0 font-normal">
    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
      <UserInfo :user="user" :show-email="true" />
    </div>
  </DropdownMenuLabel>
  <DropdownMenuSeparator />
  <DropdownMenuGroup>
    <!-- <DropdownMenuItem :as-child="true" v-if="can('read-users') || (user as any)?.roles?.includes('superadmin')">
      <Link class="block w-full" href="/users" prefetch as="button">
        <Users class="mr-2 h-[1rem] w-[1rem]" />
        Users
      </Link>
    </DropdownMenuItem>
    <DropdownMenuItem :as-child="true" v-if="can('update-role-permissions')">
      <Link class="block w-full" href="/roles-permissions" prefetch as="button">
        <Shield class="mr-2 h-[1rem] w-[1rem]" />
        Role Permissions
      </Link>
    </DropdownMenuItem> -->
    <DropdownMenuItem :as-child="true">
      <Link class="block w-full" :href="route('profile.edit')" prefetch as="button">
        <Settings class="mr-2 h-[1rem] w-[1rem]" />
        Settings
      </Link>
    </DropdownMenuItem>
  </DropdownMenuGroup>
  <DropdownMenuSeparator />
  <DropdownMenuItem @click="handleLogout">
    <LogOut class="mr-2 h-[1rem] w-[1rem]" />
    Log out
  </DropdownMenuItem>
</template>
