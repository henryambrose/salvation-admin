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
import DataTable from '@/components/DataTable.vue';
import { router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    zones: Object,
    filters: Object,
}


const props = defineProps<Props>();

const filters = reactive({
    search: props.filters.search || '',
})
// Trigger search with debounce (optional)
let timeout = null
function searchZones() {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    router.get('/zones', { search: filters.search }, { preserveState: true, replace: true })
  }, 300)
}

function goToPage(url) {
  if (url) router.get(url, {}, { preserveState: true })
}


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Zone Management',
        href: '/zone/index',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

// const form = useForm({
//     name: user.name,
//     email: user.email,
// });

// const submit = () => {
//     form.patch(route('profile.update'), {
//         preserveScroll: true,
//     });
// };

// Your data can be fetched from a Laravel API
// const zones = ref([]);

// Fetch data from Laravel backend
const fetchZones = async () => {
  try {
    const response = await fetch(route('zone.index'));
    zones.value = await response.json();
    console.log('Fetched zones:', zones.value);
  } catch (error) {
    console.error('Error fetching zones:', error);
  }
};

// Define columns
const columns = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'description', label: 'Description' },

];

// Handle edit and delete actions
const handleEdit = (user) => {
  // Implement edit functionality
  console.log('Edit user:', user);
};

const handleDelete = (user) => {
  // Implement delete functionality
  console.log('Delete user:', user);
};

// Fetch data when component mounts
onMounted(() => {
    // fetchZones();
});

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Zones Management" />

        <DataTable
            :data="zones.data"
            :columns="columns"
            @edit="handleEdit"
            @delete="handleDelete"/>
    </AppLayout>
</template>
