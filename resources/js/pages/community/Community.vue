<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';

import FormBody from '@/components/FormBody.vue';
import FormHeader from '@/components/FormHeader.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';

interface Props {
  community?: {
    id: number;
    name: string;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Communities',
    href: '/community/index',
  },
  {
    title: props.community ? 'Edit Community' : 'Create Community',
    href: props.community ? `/community/edit/${props.community.id}` : '/community/create',
  },
];

const page = usePage<SharedData>();

const form = useForm({
  id: props.community?.id || '',
  name: props.community?.name || '',
});

const submit = () => {
  const routeName = props.community?.id ? 'community.update' : 'community.store'; // Determine the route
  const method = props.community?.id ? 'put' : 'post'; // Determine the HTTP method

  form[method](route(routeName, { id: props.community?.id }), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="props.community ? 'Edit Community' : 'Create Community'" />
    <FormHeader>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold">{{ props.community ? 'Edit Community' : 'Create Community' }}</h2>
        <div class="btn-group flex space-x-2">
          <Button as="a" href="/community/index">
            <component :is="List" />
            <span>Community List</span>
          </Button>
        </div>
      </div>
    </FormHeader>
    <FormBody>
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid gap-2">
          <Label for="name">Community Name</Label>
          <Input id="name" v-model="form.name" class="mt-1 block w-full" placeholder="Enter community name" required />
          <InputError class="mt-2" :message="form.errors.name" />
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
