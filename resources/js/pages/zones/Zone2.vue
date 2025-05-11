<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { ref, computed, onMounted } from 'vue';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'

const props = defineProps({
  zones: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
{ key: 'id', label: 'Id', sortable: true },
{ key: 'name', label: 'Name', sortable: true },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Zones Management" />
        <DataTable
            :data="zones"
            :columns="columns"
            :filters="filters"
            :fetch-url="fetchUrl"
            />
    </AppLayout>
</template>
