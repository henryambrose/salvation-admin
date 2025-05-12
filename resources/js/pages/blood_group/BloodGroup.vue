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

const props = defineProps({
  bloodGroups: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
{ key: 'id', label: 'Id', sortable: true },
{ key: 'name', label: 'Name', sortable: true },
];

const showModal = ref(false);
const form = useForm({
  name: '',
});

function submit() {
  form.post('/blood-group', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
    },
  });
}
const breadcrumbs = [
  { title: 'Blood Group', href: '/blood-group' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Blood Group" />
        <div class="p-4 bg-white shadow rounded">
            <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Blood Group</h2>
            <Button @click="showModal = true">
                ➕ Add Blood Group
            </Button>

        </div>
        </div>

        <DataTable
            :data="bloodGroups"
            :columns="columns"
            :filters="filters"
            :fetch-url="fetchUrl"
        />

        <!-- Modal -->
        <transition name="fade">

            <div v-if="showModal" class="fixed inset-0 bg-transparent bg-opacity-20 flex justify-center items-center z-50">
                <div class="bg-gradient-to-r from-grey-900 via-grey-800 to-grey-600 p-[2px] rounded-lg shadow-lg w-full max-w-md">
                    <div class="bg-white rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4">Create Blood Group</h3>
                    <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <Input
                        v-model="form.name"
                        type="text"

                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <Button variant="destructive" type="button" @click="showModal = false">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Creating...' : 'Create' }}
                        </Button>
                    </div>
                    </form>
                </div>
            </div>
            </div>

        </transition>

    </AppLayout>
</template>
<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
