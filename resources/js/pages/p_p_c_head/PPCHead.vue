<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SelectInput } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Member, type SharedData, type User, Communities, PPCHead, type BreadcrumbItem } from '@/types';
import { List } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import FormHeader from '@/components/FormHeader.vue';
import FormBody from '@/components/FormBody.vue';
import { SearchDropdown } from '@/components/ui/searchDropdown';

interface Props {
    ppc_head?: PPCHead;
    communities: Communities;
    members: Member[];
}

const props = defineProps<Props>();

const page = usePage<SharedData>();
const user = page.props.auth.user as User;


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'PPC Heads',
        href: '/ppc-head/index',
    },
    {
        title: props.ppc_head ? 'Edit PPC Head' : 'Create PPC Head',
        href: props.ppc_head ? `/ppc-head/edit/${props.ppc_head.id}` : '/ppc-head/create',
    },
];

const enhancedMembers = computed(() => {
    return {
        data: page.props.members.map(item => ({
            id: item.id,
            name: item.first_name + ' ' + item.middle_name + ' ' + item.last_name,
        })),
    };
});

console.log('Enhanced Members:', enhancedMembers.value);

const form = useForm({
    id: props.ppc_head?.id || '',
    member_id: props.ppc_head?.member_id || '',
    community_id: props.ppc_head?.community_id || '',
});

const submit = () => {
    const routeName = props.ppc_head?.id ? 'ppc-head.update' : 'ppc-head.store'; // Determine the route
    const method = props.ppc_head?.id ? 'put' : 'post'; // Determine the HTTP method

    form[method](route(routeName, { id: props.ppc_head?.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="props.ppc_head ? 'Edit PPC Head' : 'Create PPC Head'" />
        <FormHeader>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">
                    {{ props.ppc_head ? 'Edit PPC Head' : 'Create PPC Head' }}
                </h2>
                <div class="btn-group flex space-x-2">
                    <Button as="a" href="/ppc-head/index" class="btn btn-secondary">
                        <component :is="List" />
                        <span>PPC Head List</span>
                    </Button>
                </div>
            </div>
        </FormHeader>
        <FormBody>
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="member_id">Member</Label>
                        <!-- <SelectInput
                            id="member_id"
                            v-model="form.member_id"
                            :options="enhancedMembers.data"
                            class="mt-1 block w-full"
                            placeholder="Select Member"
                        /> -->
                        <SearchDropdown
                        id="member_id"
                        v-model="form.member_id"
                        :options="enhancedMembers.data"

                        />
                        <InputError class="mt-2" :message="form.errors.member_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="community_id">Community</Label>
                        <!-- <SelectInput
                            id="community_id"
                            v-model="form.community_id"
                            :options="page.props.communities"
                            class="mt-1 block w-full"
                            placeholder="Select Community"
                        /> -->
                        <SearchDropdown
                        id="community_id"
                        v-model="form.community_id"
                        :options="page.props.communities"

                        />
                        <InputError class="mt-2" :message="form.errors.community_id" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button class="btn btn-secondary" :disabled="form.processing">Save</Button>

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
        </FormBody>
    </AppLayout>
</template>
