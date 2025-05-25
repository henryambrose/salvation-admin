<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SelectInput } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Member, type SharedData, type User, Communities, SCCHead, type BreadcrumbItem } from '@/types';
import { List } from 'lucide-vue-next';
import { ref } from 'vue';


interface Props {
    scc_head?: SCCHead;
    communities: Communities;
    members: Member[];
}


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Member Details',
        href: '/member/profile-details',
    },
];

const props = defineProps<Props>();

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const member = page.props.member as Member;

const form = useForm({
    id: props.scc_head?.id ? props.scc_head.id : '',
    member_id: props.scc_head?.member_id ? props.scc_head.member_id : '',
    community_id: props.scc_head?.community_id ? props.scc_head.community_id : '',
});

const submit = () => {
    const routeName = props.scc_head?.id ? 'scc-head.update' : 'scc-head.store'; // Determine the route
    const method = props.scc_head?.id ? 'put' : 'post'; // Determine the HTTP method

    form[method](route(routeName, { id: props.scc_head?.id }), {
        preserveScroll: true,
    });
};

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Members" />
        <div class="p-4 bg-white shadow rounded">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Members</h2>
                <div class="btn-group flex space-x-2">
                    <Button as="a" href="/member/index" class="btn btn-secondary">
                        <component :is="List" />
                        <span>Member List</span>
                    </Button>
                </div>
            </div>
        </div>
        <div class="p-4 bg-white shadow rounded">

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="member_id">Member ID</Label>
                        <SelectInput
                            id="member_id"
                            v-model="form.member_id"
                            :options="page.props.members"
                            class="mt-1 block w-full"
                            placeholder="Select Community"
                        />
                        <InputError class="mt-2" :message="form.errors.member_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="community_id">Community ID</Label>
                        <SelectInput
                            id="community_id"
                            v-model="form.community_id"
                            :options="page.props.communities"
                            class="mt-1 block w-full"
                            placeholder="Select Community"
                        />
                        <InputError class="mt-2" :message="form.errors.community_id" />
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
