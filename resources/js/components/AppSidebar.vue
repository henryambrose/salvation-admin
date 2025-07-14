<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Droplet, MapPin, CurrencyIcon, IndianRupee, UserCircle, UserCheck, Home, LogOut } from 'lucide-vue-next';
import * as Icons from 'lucide-vue-next'
import AppLogo from './AppLogo.vue';
import { permissionHelpers } from '@/composables/permissionHelpers';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const { can } = permissionHelpers();


const resolveIcon = (iconName: string) => {
    return Icons[iconName] || Icons.HelpCircle // fallback icon
}

const page = usePage()

const modules = computed(() => {
    return page.props.modules.map((module: any) => ({
        // ...module,
        title: module.name,
        href: '/'+module.slug,
        icon: resolveIcon(module.icon),
        show: can(module.slug),
        // actions: module.actions.map((action: any) => ({
        //     ...action,
        //     can: can(action.slug),
        // })),
    }));
});


// console.log('modules', modules.value);
// console.log('has member permission:', can('view-Member'));
const mainNavItems: NavItem[] = modules.value;

// const mainNavItems: NavItem[] = [
//     {
//         title: 'Dashboard',
//         href: '/dashboard',
//         icon: LayoutGrid,
//         show: can('view-Dashboard'),
//     },


//     {
//         title: 'Member',
//         href: '/member',
//         icon: UserCircle,
//         show: can('view-Member'),
//     },

//     {
//         title: 'Community',
//         href: '/community',
//         icon: CurrencyIcon,
//         show: can('view-Community'),
//     },

//     {
//         title: 'Community Fund',
//         href: '/community-fund',
//         icon: CurrencyIcon,
//         show: can('view-CommunityFund'),
//     },

//     {
//         title: 'Zone',
//         href: '/zone',
//         icon: MapPin,
//         show: can('view-Zone'),
//     },

//     {
//         title: 'Blood Group',
//         href: '/blood-group',
//         icon: Droplet,
//         show: can('view-BloodGroup'),
//     },

//     {
//         title: 'Family Income Range',
//         href: '/family-income-range',
//         icon: IndianRupee,
//         show: can('view-FamilyIncomeRange'),
//     },

//     {
//         title: 'SCC Head',
//         href: '/scc-head',
//         icon: UserCheck,
//         show: can('view-SCCHead'),
//     },

//     {
//         title: 'PPC Head',
//         href: '/ppc-head',
//         icon: UserCheck,
//         show: can('view-PPCHead'),
//     },

//     {
//         title: 'Country',
//         href: '/country',
//         icon: MapPin,
//         show: can('view-Country'),
//     },

//     {
//         title: 'State',
//         href: '/state',
//         icon: MapPin,
//         show: can('view-State'),
//     },

//     {
//         title: 'Town',
//         href: '/town',
//         icon: Home,
//         show: can('view-Town'),
//     },

// ];

const filteredMainNavItems = mainNavItems.filter(item => item.show);
// console.log('filteredMainNavItems', filteredMainNavItems);
// debugger;

const footerNavItems: NavItem[] = [
    {
        title: 'Users',
        href: '/users/index',
        icon: UserCircle,
        show: can('view-Users'),
    },
    {
        title: 'Role Permissions',
        href: '/roles-permissions',
        icon: BookOpen,
        show: can('update-role-permissions'),
    },
];

const filteredFooterNavItems = footerNavItems.filter(item => item.show);

// Add logout handler

function handleLogout() {
    router.post(route('logout'), {}, {
        onSuccess: () => {
            router.visit(route('login'));
        }
    });
}

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
            <!-- <NavMain :items="mainNavItems" /> -->
            <NavMain :items="filteredMainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavFooter :items="filteredFooterNavItems" />
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="md" as-child>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            @click.prevent="handleLogout"
                            class="flex items-center w-full"
                        >
                            <LogOut class="mr-2 h-4 w-4" />
                            Log out
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
