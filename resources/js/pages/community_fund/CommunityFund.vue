<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SelectInput } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Member, type SharedData, type User, type BreadcrumbItem } from '@/types';
import { List } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    communityFund?: {
        id: number;
        member_id: number;
        amount: number;
        year: string;
    };
    members: Member[];
}

const props = defineProps<Props>();

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Community Funds',
        href: '/community-fund/index',
    },
    {
        title: props.communityFund ? 'Edit Community Fund' : 'Create Community Fund',
        href: props.communityFund ? `/community-fund/edit/${props.communityFund.id}` : '/community-fund/create',
    },
];

const enhancedMembers = computed(() => {
    return {
        data: page.props.members.map(item => ({
            id: item.id,
            name: item.first_name + ' ' + (item.middle_name || '') + ' ' + item.last_name,
        })),
    };
});

const form = useForm({
    id: props.communityFund?.id || '',
    member_id: props.communityFund?.member_id || '',
    amount: props.communityFund?.amount || '',
    year: props.communityFund?.year || '',
});

const submit = () => {
    const routeName = props.communityFund?.id ? 'community-fund.update' : 'community-fund.store'; // Determine the route
    const method = props.communityFund?.id ? 'put' : 'post'; // Determine the HTTP method

    form[method](route(routeName, { id: props.communityFund?.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="props.communityFund ? 'Edit Community Fund' : 'Create Community Fund'" />
        <div class="p-4 bg-white shadow rounded">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">
                    {{ props.communityFund ? 'Edit Community Fund' : 'Create Community Fund' }}
                </h2>
                <div class="btn-group flex space-x-2">
                    <Button as="a" href="/community-fund/index" class="btn btn-secondary">
                        <component :is="List" />
                        <span>Community Fund List</span>
                    </Button>
                </div>
            </div>
        </div>
        <div class="p-4 bg-white shadow rounded">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="member_id">Member</Label>
                        <SelectInput
                            id="member_id"
                            v-model="form.member_id"
                            :options="enhancedMembers.data"
                            class="mt-1 block w-full"
                            placeholder="Select Member"
                        />
                        <InputError class="mt-2" :message="form.errors.member_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="amount">Amount</Label>
                        <Input
                            id="amount"
                            v-model="form.amount"
                            type="number"
                            class="mt-1 block w-full"
                            placeholder="Enter Amount"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.amount" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="year">Year</Label>
                        <Input
                            id="year"
                            v-model="form.year"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Enter Year (e.g., 2025)"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.year" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Save</Button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                    </Transition>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
