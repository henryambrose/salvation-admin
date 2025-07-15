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

// Define columns, including an 'actions' column for buttons
const columns = [
  { key: 'id', label: 'Id' },
  { key: 'name', label: 'Name' },
  // Add description column only if your backend returns it
  // { key: 'description', label: 'Description' },
  // No need to add an 'actions' column; DataTable renders Edit/Delete by default
];

// Handle edit and delete actions
const handleEdit = (zone) => {
  router.get(route('zone.edit', { zone: zone.id }));
};

const handleDelete = (zone) => {
  if (confirm('Are you sure you want to delete this zone?')) {
    router.delete(route('zone.destroy', { zone: zone.id }), {
      preserveScroll: true,
    });
  }
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
            :data="props.zones.data"
            :columns="columns"
            has-actions
            @edit="handleEdit"
            @delete="handleDelete"
        >
          <template #actions="{ row }">
            <button @click="handleEdit(row)" class="text-primary hover:text-primary-dark mr-3">
              Edit
            </button>
            <button @click="handleDelete(row)" class="text-red-600 hover:text-red-800">
              Delete
            </button>
          </template>
        </DataTable>
    </AppLayout>
</template>
