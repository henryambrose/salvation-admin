<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { TextareaInput } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { ref, computed, onMounted } from 'vue';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'
import { Plus } from 'lucide-vue-next';


const props = defineProps({
  members: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
{ key: 'id', label: 'Id', sortable: true },
{ key: 'first_name', label: 'First Name', sortable: true },
];


const breadcrumbs = [
  { title: 'Members', href: '/member/index' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Members" />
        <div class="p-4 bg-white shadow rounded">
            <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Members</h2>
            <div class="btn-group flex space-x-2">
                <Button as="a" href="/member/profile-details/create" class="btn btn-secondary">
                    <component :is="Plus" />
                    <span>Add Member</span>
                </Button>
            </div>
        </div>
        </div>

        <DataTable
            :data="members"
            :columns="columns"
            :filters="filters"
            :fetch-url="fetchUrl"
        />

    </AppLayout>
</template>
