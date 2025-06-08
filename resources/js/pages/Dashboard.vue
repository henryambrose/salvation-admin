<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardContent from '@/components/ui/card/CardContent.vue';

const props = defineProps({
    statCards: {
        type: Array,
        default: () => [],
    },
    tableCards: {
        type: Array,
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
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-4">
                <Card v-for="statCard in statCards" class="rounded-xl uppercase border text-white shadow-md border-2" :class="[statCard.bgClass, statCard.borderClass]" :key="statCard.title">
                    <CardHeader class="p-2 text-center font-bold">
                        <CardTitle class="text-xl">{{ statCard.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="px-10 py-8 text-6xl font-bold text-center border-t-2" :class="statCard.borderClass">
                        {{ statCard.count }}
                    </CardContent>
                </Card>
                <Card v-for="tableCard in tableCards" class="rounded-xl uppercase border text-white shadow-md border-2" :class="[tableCard.bgClass, tableCard.borderClass]" :key="tableCard.title">
                    <CardHeader class="p-2 text-center font-bold">
                        <CardTitle class="text-xl">{{ tableCard.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="p-2 font-bold text-center border-t-2" :class="tableCard.borderClass">
                        <div class="w-full max-w-md overflow-hidden">
                            <!-- Table Header -->
                            <div class="grid grid-cols-2 bg-gray-100 font-semibold text-left text-gray-800 p-2">
                                <div>Status</div>
                                <div>Count</div>
                            </div>

                            <!-- Table Rows -->
                            <div class="divide-y divide-gray-200">
                                <div class="grid grid-cols-2 text-left p-2" v-for="(row, index) in tableCard.data" :key="index">
                                    <div>{{ index }}</div>
                                    <div>{{ row }}</div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
