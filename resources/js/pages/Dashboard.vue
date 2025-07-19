<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

defineProps({
    statCards: {
        type: Array<Record<string, any>>,
        default: () => [],
    },
    tableCards: {
        type: Array<Record<string, any>>,
        default: () => [],
    },
    ageWiseData: {
        type: Object,
        default: () => [],
    },
    birthdays: {
        type: Array<Record<string, any>>,
        default: () => [],
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
            <div class="grid grid-cols-1 gap-4">
                <Collapsible class="w-full rounded-lg shadow-md">
                    <CollapsibleTrigger class="w-full rounded p-2 shadow-md lg:rounded-lg">
                        &#127881; &#127874; Birthdays &#129395; &#127873; &#127880;
                    </CollapsibleTrigger>
                    <CollapsibleContent class="bg-gray-100 p-4">
                        <table class="w-full table-auto border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border border-gray-300 px-4 py-2 uppercase">COMMUNITY</th>
                                    <th class="border border-gray-300 px-4 py-2 uppercase">First Name</th>
                                    <th class="border border-gray-300 px-4 py-2 uppercase">Middle Name</th>
                                    <th class="border border-gray-300 px-4 py-2 uppercase">Last Name</th>
                                    <th class="border border-gray-300 px-4 py-2 uppercase">Date of Birth</th>
                                    <th class="border border-gray-300 px-4 py-2 uppercase">Age</th>
                                    <th class="border border-gray-300 px-4 py-2 uppercase">Contact No</th>
                                    <th class="border border-gray-300 px-4 py-2 uppercase">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="birthday in birthdays" :key="birthday.id">
                                    <td class="border border-gray-300 px-4 py-2">{{ birthday?.community?.name ? birthday?.community?.name : '' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ birthday.first_name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ birthday.middle_name || 'N/A' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ birthday.last_name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ birthday.date_of_birth }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ birthday.age }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ birthday.contact_no }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ birthday.email }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </CollapsibleContent>
                </Collapsible>
            </div>
            <div class="grid gap-4 md:grid-cols-4">
                <Card
                    v-for="statCard in statCards"
                    class="rounded-xl border text-white uppercase shadow-md"
                    :class="[statCard.bgClass, statCard.borderClass]"
                    :key="statCard.title"
                >
                    <CardHeader class="p-2 text-center font-bold">
                        <CardTitle class="text-xl">{{ statCard.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="border-t-2 px-10 py-8 text-center text-6xl font-bold" :class="statCard.borderClass">
                        {{ statCard.count }}
                    </CardContent>
                </Card>
                <Card
                    v-for="tableCard in tableCards"
                    class="rounded-xl border text-white uppercase shadow-md"
                    :class="[tableCard.bgClass, tableCard.borderClass]"
                    :key="tableCard.title"
                >
                    <CardHeader class="p-2 text-center font-bold">
                        <CardTitle class="text-xl">{{ tableCard.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="border-t-2 p-2 text-center font-bold" :class="tableCard.borderClass">
                        <div class="w-full max-w-md overflow-hidden">
                            <!-- Table Header -->
                            <div class="grid grid-cols-2 bg-gray-100 p-2 text-left font-semibold text-gray-800">
                                <div>Status</div>
                                <div>Count</div>
                            </div>

                            <!-- Table Rows -->
                            <!-- Scrollable Table Rows -->
                            <div class="max-h-48 divide-y divide-gray-200 overflow-y-auto">
                                <div class="grid grid-cols-2 p-2 text-left" v-for="(row, index) in tableCard.data" :key="index">
                                    <div>{{ index ? index : 'Other' }}</div>
                                    <div>{{ row }}</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
                <Card class="rounded-xl border border-purple-800 bg-purple-500 text-white uppercase shadow-md">
                    <CardHeader class="p-2 text-center font-bold">
                        <CardTitle class="text-xl">Age Wise</CardTitle>
                    </CardHeader>
                    <CardContent class="border-t-2 border-purple-800 p-2 text-center font-bold">
                        <div class="w-full max-w-md overflow-hidden">
                            <!-- Table Header -->
                            <div class="grid grid-cols-4 bg-gray-100 p-2 text-left font-semibold text-gray-800">
                                <div>Status</div>
                                <div>Count</div>
                                <div>Male</div>
                                <div>Female</div>
                            </div>

                            <!-- Scrollable Table Rows -->
                            <div class="max-h-48 divide-y divide-gray-200 overflow-y-auto">
                                <div class="grid grid-cols-4 p-2 text-left" v-for="(row, index) in ageWiseData" :key="index">
                                    <div>{{ index }}</div>
                                    <div>{{ row['Male'] + row['Female'] }}</div>
                                    <div>{{ row['Male'] }}</div>
                                    <div>{{ row['Female'] }}</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div> -->
            <div class="border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] flex-1 rounded-xl border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
