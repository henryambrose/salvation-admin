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
  familyIncomeRange: Object,
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
//   starting_range: '',
//   ending_range: '',

});

function submit() {
  form.post('/family-income-range', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showModal.value = false;
    },
  });
}
const breadcrumbs = [
  { title: 'Family Income Range', href: '/family-income-range' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Family Income Range" />
        <div class="p-4 bg-white shadow rounded">
            <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Family Income Range</h2>
            <Button @click="showModal = true">
                ➕ Add Family Income Range
            </Button>

        </div>
        </div>

        <DataTable
            :data="familyIncomeRange"
            :columns="columns"
            :filters="filters"
            :fetch-url="fetchUrl"
        />

        <!-- Modal -->
        <transition name="fade">

            <div v-if="showModal" class="fixed inset-0 bg-transparent bg-opacity-20 flex justify-center items-center z-50">
                <div class="bg-gradient-to-r from-grey-900 via-grey-800 to-grey-600 p-[2px] rounded-lg shadow-lg w-full max-w-md">
                    <div class="bg-white rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4">Create Family Income Range</h3>
                    <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Range</label>
                        <Input
                        v-model="form.name"
                        type="text"

                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    <!-- <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Starting Range</label>
                        <Input
                        v-model="form.starting_range"
                        type="text"

                        />
                        <div v-if="form.errors.starting_range" class="text-red-500 text-sm mt-1">{{ form.errors.starting_range }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Ending Range</label>
                        <Input
                        v-model="form.ending_range"
                        type="text"

                        />
                        <div v-if="form.errors.ending_range" class="text-red-500 text-sm mt-1">{{ form.errors.ending_range }}</div>
                    </div> -->

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
