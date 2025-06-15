<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { SCCHead } from '@/types';
import DataTable from '@/components/DataTable2.vue';
import { router } from '@inertiajs/vue3'
import { Pencil, Plus, Trash } from 'lucide-vue-next';
import DatatableHeader from '@/components/DatatableHeader.vue';


const props = defineProps({
  s_c_c_heads: Object,
  filters: Object,
  fetchUrl: String,
});

const columns = [
{ key: 'id', label: 'Id', sortable: true },
{ key: 'member_first_name', label: 'Member Name', sortable: true },
{ key: 'community_name', label: 'Community Name', sortable: true },

];

console.log('SCC Heads:', props.s_c_c_heads.data);

const breadcrumbs = [
  { title: 'SCC Heads', href: '/scc-head/index' },
];

function editSCCHead(sccHead : SCCHead) {
  // open edit modal logic
  router.get(route('scc-head.edit', sccHead.id));
}

function deleteSCCHead(id : SCCHead['id']) {
  if (confirm('Delete this SCC Head?')) {
    router.delete(route('scc-head.destroy', id));
  }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="SSC Heads" />
        <DatatableHeader>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">SCC Head</h2>
                <div class="btn-group flex space-x-2">
                    <Button as="a" href="/scc-head/create" class="btn btn-secondary">
                        <component :is="Plus" />
                        <span>Add SCC Head</span>
                    </Button>
                </div>
            </div>
        </DatatableHeader>

        <DataTable
            :data="s_c_c_heads"
            :columns="columns"
            :filters="filters"
            :fetch-url="fetchUrl"
            :has-actions="true"
        >
            <template #actions="{ row }">
                <Button class="btn btn-secondary mr-2" @click="editSCCHead(row)">
                    <component :is="Pencil" />
                    <span>Edit</span>
                </Button>
                <Button class="btn btn-secondary mr-2" @click="deleteSCCHead(row.id)">
                    <component :is="Trash" />
                    <span>Delete</span>
                </Button>
            </template>
        </DataTable>


    </AppLayout>
</template>
