<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import MemberLayout from '@/layouts/member/Layout.vue';
import { Member, type BreadcrumbItem, type SharedData, type User } from '@/types';

interface Props {
    member?: Member;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Member Details',
        href: '/member/profile-details',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const member = page.props.member as Member;

const form = useForm({
    id: member?.id ? member.id : '',
    first_name: member?.first_name ? member.first_name : '',
    middle_name: member?.middle_name ? member.middle_name : '',
    last_name: member?.last_name ? member.last_name : '',
});

const submit = () => {
    form.post(route('member.profile-details.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Member Details" />

        <MemberLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile Details" description="Update member's profile details" />

                <form @submit.prevent="submit" class="space-y-6">

                    <div class="grid gap-2">
                        <Label for="first_name">First Name</Label>
                        <Input id="first_name" class="mt-1 block w-full" v-model="form.first_name" required autocomplete="first_name" placeholder="First name" />
                        <InputError class="mt-2" :message="form.errors.first_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="middle_name">Middle Name</Label>
                        <Input id="middle_name" class="mt-1 block w-full" v-model="form.middle_name" required autocomplete="middle_name" placeholder="Middle name" />
                        <InputError class="mt-2" :message="form.errors.middle_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="last_name">Last Name</Label>
                        <Input id="last_name" class="mt-1 block w-full" v-model="form.last_name" required autocomplete="last_name" placeholder="Last name" />
                        <InputError class="mt-2" :message="form.errors.last_name" />
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

        </MemberLayout>
    </AppLayout>
</template>
