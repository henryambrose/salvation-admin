<script setup lang="ts">
import { SidebarInset } from '@/components/ui/sidebar';
import { SharedData, User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);


const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const permissions = computed(() => page.props.auth?.permissions);

// Helper function to check permission
const can = (permission) => {
  return permissions.value.includes(permission);
};

console.log('User:', user);
console.log('Permissions:', permissions.value);
console.log('Can view dashboard:', can('create all'));
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
        <slot />
    </SidebarInset>
    <main v-else class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl main-bg" :class="className">
        <slot />
    </main>
</template>
