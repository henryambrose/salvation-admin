<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Droplet, MapPin, CurrencyIcon, IndianRupee, UserCircle, UserCheck, Home } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },

    {
        title: 'Member',
        href: '/member',
        icon: UserCircle,
    },

    {
        title: 'Community',
        href: '/community',
        icon: CurrencyIcon,
    },

    {
        title: 'Community Fund',
        href: '/community-fund',
        icon: CurrencyIcon,
    },

    {
        title: 'Zone',
        href: '/zone',
        icon: MapPin,
    },

    {
        title: 'Blood Group',
        href: '/blood-group',
        icon: Droplet,
    },

    {
        title: 'Family Income Range',
        href: '/family-income-range',
        icon: IndianRupee,
    },

    {
        title: 'SCC Head',
        href: '/scc-head',
        icon: UserCheck,
    },

    {
        title: 'PPC Head',
        href: '/ppc-head',
        icon: UserCheck,
    },

    {
        title: 'Country',
        href: '/country',
        icon: MapPin,
    },

    {
        title: 'State',
        href: '/state',
        icon: MapPin,
    },

    {
        title: 'Town',
        href: '/town',
        icon: Home,
    },



];

// const footerNavItems: NavItem[] = [
//     {
//         title: 'Github Repo',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits#vue',
//         icon: BookOpen,
//     },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
