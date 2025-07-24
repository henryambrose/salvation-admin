<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';

import FormBody from '@/components/FormBody.vue';
import FormHeader from '@/components/FormHeader.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { List } from 'lucide-vue-next';
import { type BreadcrumbItem, type SharedData } from '@/types';

interface Props {
  ageGroup?: {
    id: number;
    name: string;
    description?: string;
    min_age?: number;
    max_age?: number;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Age Groups',
    href: '/age-group/index',
  },
  {
    title: props.ageGroup ? 'Edit Age Group' : 'Create Age Group',
    href: props.ageGroup ? `/age-group/edit/${props.ageGroup.id}` : '/age-group/create',
  },
];

const page = usePage<SharedData>();

const form = useForm({
  id: props.ageGroup?.id || '',
  name: props.ageGroup?.name || '',
  description: props.ageGroup?.description || '',
  min_age: props.ageGroup?.min_age || '',
  max_age: props.ageGroup?.max_age || '',
});

const submit = () => {
  const routeName = props.ageGroup?.id ? 'age-group.update' : 'age-group.store';
  const method = props.ageGroup?.id ? 'put' : 'post';

  form[method](route(routeName, { id: props.ageGroup?.id }), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="props.ageGroup ? 'Edit Age Group' : 'Create Age Group'" />
    <FormHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">{{ props.ageGroup ? 'Edit Age Group' : 'Create Age Group' }}</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/age-group/index">
            <component :is="List" />
            <span>Age Group List</span>
          </Button>
        </div>
      </div>
    </FormHeader>
    <FormBody>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid gap-2">
          <Label for="name">Age Group Name</Label>
          <Input id="name" v-model="form.name" class="mt-1 block w-full" placeholder="Enter age group name" required />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="grid gap-2">
          <Label for="description">Description</Label>
          <Input id="description" v-model="form.description" class="mt-1 block w-full" placeholder="Description" />
          <InputError class="mt-2" :message="form.errors.description" />
        </div>
        <div class="grid gap-2">
          <Label for="min_age">Min Age</Label>
          <Input id="min_age" type="number" v-model="form.min_age" class="mt-1 block w-full" required />
          <InputError class="mt-2" :message="form.errors.min_age" />
        </div>
        <div class="grid gap-2">
          <Label for="max_age">Max Age</Label>
          <Input id="max_age" type="number" v-model="form.max_age" class="mt-1 block w-full" required />
          <InputError class="mt-2" :message="form.errors.max_age" />
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
    </FormBody>
  </AppLayout>
</template>
