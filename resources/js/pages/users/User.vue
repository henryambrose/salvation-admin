<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import FormHeader from '@/components/FormHeader.vue';
import FormBody from '@/components/FormBody.vue';
import { List } from 'lucide-vue-next';

interface Props {
    user?: {
        id: number;
        name: string;
        email: string;
    };
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    { name: 'Users', href: '/users/index' },
    { name: props.user ? 'Edit User' : 'Create User', href: props.user ? `/users/${props.user.id}/edit` : '/users/create' },
];

function submit() {
    if (props.user) {
        form.put(`/users/${props.user.id}`);
    } else {
        form.post('/users');
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="props.user ? 'Edit User' : 'Create User'" />
        <FormHeader>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">{{ props.user ? 'Edit User' : 'Create User' }}</h2>
                <div class="btn-group flex space-x-2">
                    <Button as="a" href="/users/index" class="btn btn-secondary">
                        <component :is="List" />
                        <span>Users List</span>
                    </Button>
                </div>
            </div>
        </FormHeader>

        <FormBody>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">{{ props.user ? 'New Password (leave blank to keep current)' : 'Password' }}</Label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        :required="!props.user"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ props.user ? 'Update' : 'Create' }}
                    </Button>
                </div>
            </form>
        </FormBody>
    </AppLayout>
</template>